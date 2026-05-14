<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Logbook;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembimbingSiswaController extends PembimbingBaseController
{
    /**
     * Menampilkan daftar siswa binaan yang dibimbing oleh pembimbing login.
     */
    public function daftarSiswa(Request $request)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $search     = $request->input('search');
        $periodeId  = $request->input('periode');
        $today      = Carbon::now()->toDateString();

        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->with([
            'guru', 'pembimbing', 'tahunAjaran',
            'absensis' => fn($q) => $q->whereDate('tanggal', $today),
            'penilaians' => fn($q) => $q->where('pemberi_nilai', 'Dosen Pembimbing')
        ])->orderBy('nama', 'asc')->get();

        foreach ($allSiswas as $s) {
            $s->progress_percent = $this->calculateProgress($s);
            $s->absen_hari_ini = $s->absensis->first();
        }

        $siswasActive = $allSiswas->filter(function($s) use ($today) {
            return $s->status !== 'selesai' && (!$s->tgl_selesai_magang || $s->tgl_selesai_magang >= $today);
        });
        
        $siswasHistory = $allSiswas->filter(function ($s) use ($periodeId, $today) {
            $isSelesai = $s->status === 'selesai' || ($s->tgl_selesai_magang && $s->tgl_selesai_magang < $today);
            if (!$isSelesai) return false;
            return !$periodeId || (string)$s->id_tahun_ajaran === (string)$periodeId;
        });

        $periodeOptions = TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_pembimbing', $pembimbing->id_pembimbing))
            ->orderBy('tgl_mulai', 'desc')->get();

        return view('pembimbing.daftarSiswa', compact(
            'pembimbing', 'siswasActive', 'siswasHistory', 'search', 'periodeId', 'periodeOptions'
        ));
    }

    /**
     * Menampilkan rekap absensi siswa tertentu.
     */
    public function absensiSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $siswa = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->where('nisn', $nisn)
            ->firstOrFail();

        $statusVerifikasi = $request->input('status_verifikasi');

        // Build full attendance history including dynamic Alpha for missing workdays
        $internStart = $siswa->tgl_mulai_magang ? Carbon::parse($siswa->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $siswa->tgl_selesai_magang ? Carbon::parse($siswa->tgl_selesai_magang)->startOfDay() : null;
        $today       = Carbon::now()->startOfDay();

        // Date range: from internship start to today
        $rangeStart = $internStart ?? Carbon::now()->startOfMonth();
        $rangeEnd   = Carbon::now();
        if ($internEnd && $rangeEnd->gt($internEnd)) {
            $rangeEnd = $internEnd->copy();
        }

        // Fetch all DB attendance records within range
        $dbRecords = $siswa->absensis()
            ->whereBetween('tanggal', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->get()
            ->keyBy('tanggal');

        // Build full list: DB records + dynamic Alpha for missing workdays
        $fullHistory = collect();
        $current = $rangeStart->copy();
        while ($current <= $rangeEnd) {
            $dateStr   = $current->toDateString();
            $isWeekend = $current->isWeekend();

            if (isset($dbRecords[$dateStr])) {
                $fullHistory->push($dbRecords[$dateStr]);
            } elseif (!$isWeekend && $current->lt($today) && $internStart && $current->gte($internStart)) {
                $fullHistory->push((object)[
                    'id_absensi'  => 'dynamic_' . $dateStr,
                    'tanggal'     => $dateStr,
                    'jam_masuk'   => null,
                    'jam_pulang'  => null,
                    'status'      => 'alpa',
                    'verifikasi'  => 'pending',
                    'foto_masuk'  => null,
                    'foto_pulang' => null,
                    'keterangan'  => null,
                    'is_dynamic'  => true,
                ]);
            }
            $current->addDay();
        }

        $fullHistory = $fullHistory->sortByDesc('tanggal')->values();

        if ($statusVerifikasi && in_array($statusVerifikasi, ['pending', 'verified', 'rejected'])) {
            $fullHistory = $fullHistory->filter(function($a) use ($statusVerifikasi) {
                $v = is_object($a) ? ($a->verifikasi ?? 'pending') : ($a['verifikasi'] ?? 'pending');
                return $v === $statusVerifikasi;
            })->values();
        }

        $rekap = [
            'total' => $fullHistory->count(),
            'hadir' => $fullHistory->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $fullHistory->where('status', 'izin')->count(),
            'sakit' => $fullHistory->where('status', 'sakit')->count(),
            'alpa'  => $fullHistory->where('status', 'alpa')->count(),
        ];

        $absensis = $fullHistory;

        return view('pembimbing.absensiSiswa', compact('pembimbing', 'siswa', 'absensis', 'rekap', 'statusVerifikasi'));
    }

    /**
     * Memproses validasi absensi (Approve/Reject)
     */
    public function validasiAbsensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'keterangan' => 'nullable|string',
            'siswa_nisn' => 'required_if:is_dynamic,1'
        ]);

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        if (strpos($id, 'dynamic_') === 0) {
            $tanggal = str_replace('dynamic_', '', $id);
            $nisn = $request->siswa_nisn;

            $siswa = Siswa::where('nisn', $nisn)->firstOrFail();
            if ($siswa->id_pembimbing != $pembimbing->id_pembimbing) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            Absensi::create([
                'nisn' => $nisn,
                'tanggal' => $tanggal,
                'status' => 'alpa',
                'verifikasi' => $request->status,
                'keterangan' => $request->keterangan ?? '-',
            ]);
        } else {
            $absensi = Absensi::findOrFail($id);

            if ($absensi->siswa->id_pembimbing != $pembimbing->id_pembimbing) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            $updateData = [
                'verifikasi' => $request->status,
                'keterangan' => $request->keterangan ?? $absensi->keterangan,
            ];

            if ($request->status === 'rejected' && in_array($absensi->status, ['hadir', 'terlambat', 'izin', 'sakit'])) {
                $updateData['status'] = 'alpa';
            }

            $absensi->update($updateData);
        }

        return redirect()->back()->with('success', 'Absensi berhasil divalidasi.');
    }

    /**
     * Verifikasi semua absensi pending untuk siswa tertentu (termasuk Alpa otomatis)
     */
    public function validasiSemuaAbsensi(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $internStart = $siswa->tgl_mulai_magang ? Carbon::parse($siswa->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $siswa->tgl_selesai_magang ? Carbon::parse($siswa->tgl_selesai_magang)->startOfDay() : null;
        $today       = Carbon::now()->startOfDay();

        $rangeStart = $internStart ?? Carbon::now()->startOfMonth();
        $rangeEnd   = Carbon::now();
        if ($internEnd && $rangeEnd->gt($internEnd)) {
            $rangeEnd = $internEnd->copy();
        }

        $dbRecords = $siswa->absensis()
            ->whereBetween('tanggal', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->pluck('tanggal')
            ->toArray();

        $newRecords = [];
        $current = $rangeStart->copy();
        while ($current <= $rangeEnd) {
            $dateStr = $current->toDateString();
            if (!$current->isWeekend() && $current->lt($today) && !in_array($dateStr, $dbRecords)) {
                $newRecords[] = [
                    'nisn' => $siswa->nisn,
                    'tanggal' => $dateStr,
                    'status' => 'alpa',
                    'verifikasi' => 'verified',
                    'keterangan' => 'Validasi Otomatis (Alpa)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $current->addDay();
        }

        if (!empty($newRecords)) {
            Absensi::insert($newRecords);
        }

        $updatedCount = Absensi::where('nisn', $nisn)
            ->where('verifikasi', 'pending')
            ->update([
                'verifikasi' => 'verified',
                'updated_at' => now()
            ]);

        $totalProcessed = count($newRecords) + $updatedCount;

        if ($totalProcessed > 0) {
            return redirect()->back()->with('success', $totalProcessed . ' absensi (termasuk Alpa otomatis) berhasil disetujui sekaligus.');
        }

        return redirect()->back()->with('info', 'Tidak ada absensi baru atau pending yang perlu disetujui.');
    }

    /**
     * Menampilkan logbook/kegiatan siswa tertentu.
     */
    public function logbookSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $siswa = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)->where('nisn', $nisn)->firstOrFail();

        $status = $request->input('status');
        $query = $siswa->logbooks();

        if ($status && in_array($status, ['pending', 'verified', 'rejected'])) {
            $query->where('status', $status);
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('pembimbing.logbookSiswa', compact('pembimbing', 'siswa', 'logbooks', 'status'));
    }

    /**
     * Memproses validasi logbook (Approve/Reject) beserta komentar pembimbing
     */
    public function validasiLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $logbook = Logbook::findOrFail($id);
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        
        $isMySiswa = Siswa::where('nisn', $logbook->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook->status = $request->status;
        $logbook->catatan_pembimbing = $request->catatan_pembimbing;
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi.');
    }

    /**
     * Verifikasi semua logbook pending untuk siswa tertentu
     */
    public function validasiSemuaLogbook(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $updatedCount = Logbook::where('nisn', $nisn)
            ->where('status', 'pending')
            ->update([
                'status' => 'verified',
                'catatan_pembimbing' => 'Validasi massal oleh pembimbing'
            ]);

        if ($updatedCount > 0) {
            return redirect()->back()->with('success', $updatedCount . ' logbook berhasil divalidasi sekaligus.');
        }

        return redirect()->back()->with('info', 'Tidak ada logbook dengan status pending untuk divalidasi.');
    }
}
