<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pembimbing;
use App\Models\Logbook;
use App\Models\KriteriaPenilaian;
use App\Models\PenilaianDetail;
use App\Models\Penilaian;
use App\Models\TahunAjaran;
use App\Models\PengajuanSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PembimbingController extends Controller
{
    /**
     * Helper to calculate student internship progress percentage
     */
    private function calculateProgress($siswa)
    {
        if (!$siswa->tgl_mulai_magang || !$siswa->tgl_selesai_magang) {
            return 0;
        }

        $start = Carbon::parse($siswa->tgl_mulai_magang);
        $end = Carbon::parse($siswa->tgl_selesai_magang);
        $now = Carbon::now();

        if ($now->lt($start)) return 0;
        if ($now->gt($end)) return 100;

        $totalDays = max(1, $start->diffInDays($end));
        $daysPassed = (int) $start->diffInDays($now);

        return min(100, round(($daysPassed / $totalDays) * 100));
    }

    /**
     * Helper to ensure assessment criteria exist for supervisor
     */
    private function ensureKriteriaExists($supervisorId)
    {
        $kriteria = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        if ($kriteria->isEmpty()) {
            $allowedTypes = ['sikap_kerja', 'kompetensi_keahlian'];
            $forbiddenWords = ['Teori', 'Praktek', 'Inisiatif', 'Kreativitas', 'Kesehatan dan Keselamatan Kerja'];
            
            $defaults = KriteriaPenilaian::whereNull('id_pembimbing')
                ->whereIn('tipe', $allowedTypes)
                ->get();

            foreach ($defaults as $d) {
                $isForbidden = false;
                foreach ($forbiddenWords as $word) {
                    if (mb_stripos($d->nama_kriteria, $word) !== false) {
                        $isForbidden = true;
                        break;
                    }
                }
                if ($isForbidden) continue;
                if (mb_strtolower($d->nama_kriteria) == 'disiplin' && $d->tipe == 'kompetensi_keahlian') continue;

                KriteriaPenilaian::create([
                    'nama_kriteria' => $d->nama_kriteria,
                    'tipe' => $d->tipe,
                    'jurusan' => $d->jurusan,
                    'urutan' => $d->urutan,
                    'id_pembimbing' => $supervisorId,
                ]);
            }
            
            return KriteriaPenilaian::where('id_pembimbing', $supervisorId)
                ->orderBy('tipe')
                ->orderBy('urutan')
                ->get();
        }
        
        return $kriteria;
    }
    /**
     * Menampilkan daftar siswa binaan yang dibimbing oleh dosen login.
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
     * Mencetak jurnal kegiatan mingguan siswa binaan (menggunakan template siswa)
     */
    public function cetakJurnalSiswa($nisn)
    {
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['pembimbing', 'logbooks' => function($q) {
                $q->orderBy('tanggal', 'asc');
            }])->firstOrFail();

        $logbooks = $user->logbooks;
        $fileName = "Jurnal_Kegiatan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', compact('user', 'logbooks'));
        return $pdf->download($fileName);
    }

    /**
     * Mencetak rekap absensi individu siswa binaan (menggunakan template siswa)
     */
    public function cetakAbsensiSiswa($nisn)
    {
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with('absensis')->firstOrFail();

        $absensis = $user->absensis()->orderBy('tanggal', 'asc')->get();
        $fileName = "Rekap_Absensi_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', compact('user', 'absensis'));
        return $pdf->download($fileName);
    }

    /**
     * Menampilkan logbook/kegiatan siswa tertentu.
     */
    public function logbookSiswa(Request $request, $nisn)
    {
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
     * Menampilkan rekap absensi siswa tertentu.
     */
    public function absensiSiswa(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->where('nisn', $nisn)
            ->firstOrFail();

        $statusVerifikasi = $request->input('status_verifikasi');

        // Build full attendance history including dynamic Alpha for missing workdays
        $internStart = $siswa->tgl_mulai_magang ? Carbon::parse($siswa->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $siswa->tgl_selesai_magang ? Carbon::parse($siswa->tgl_selesai_magang)->startOfDay() : null;
        $today       = Carbon::now()->startOfDay();

        // Date range: from internship start to today (or internship end, whichever is earlier)
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
                // Missing workday → dynamic Alpha (ID using date string for identification)
                $fullHistory->push((object)[
                    'id_absensi'  => 'dynamic_' . $dateStr, // String ID for modal trigger
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

        // Sort descending by date
        $fullHistory = $fullHistory->sortByDesc('tanggal')->values();

        // Apply verifikasi filter if requested (only applicable to DB records)
        if ($statusVerifikasi && in_array($statusVerifikasi, ['pending', 'verified', 'rejected'])) {
            $fullHistory = $fullHistory->filter(function($a) use ($statusVerifikasi) {
                // Dynamic alphas have verifikasi=pending, include them when filtering pending
                $v = is_object($a) ? ($a->verifikasi ?? 'pending') : ($a['verifikasi'] ?? 'pending');
                return $v === $statusVerifikasi;
            })->values();
        }

        // Rekap counts from full history
        $rekap = [
            'total' => $fullHistory->count(),
            'hadir' => $fullHistory->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $fullHistory->where('status', 'izin')->count(),
            'sakit' => $fullHistory->where('status', 'sakit')->count(),
            'alpa'  => $fullHistory->where('status', 'alpa')->count(),
        ];

        // Get only DB records paginated (for the "Setujui Semua" pending check)
        $absensis = $fullHistory;

        return view('pembimbing.absensiSiswa', compact('pembimbing', 'siswa', 'absensis', 'rekap', 'statusVerifikasi'));
    }

    /**
     * Memproses validasi logbook (Approve/Reject) beserta komentar dosen
     */
    public function validasiLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $logbook = Logbook::findOrFail($id);

        // Verifikasi bahwa logbook ini milik siswa yang dibimbing oleh dosen login
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
     * Memproses validasi absensi (Approve/Reject)
     */
    public function validasiAbsensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'keterangan' => 'nullable|string',
            'siswa_nisn' => 'required_if:is_dynamic,1' // Required only for dynamic records
        ]);

        $pembimbing = Auth::user();

        // Handle dynamic record creation if ID starts with 'dynamic_'
        if (strpos($id, 'dynamic_') === 0) {
            $tanggal = str_replace('dynamic_', '', $id);
            $nisn = $request->siswa_nisn;

            // Find student to check ownership
            $siswa = Siswa::where('nisn', $nisn)->firstOrFail();
            if ($siswa->id_pembimbing != $pembimbing->id_pembimbing) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            // Create new Alpha record
            $absensi = Absensi::create([
                'nisn' => $nisn,
                'tanggal' => $tanggal,
                'status' => 'alpa',
                'verifikasi' => $request->status,
                'keterangan' => $request->keterangan ?? '-',
            ]);
        } else {
            // Normal record update
            $absensi = Absensi::findOrFail($id);

            if ($absensi->siswa->id_pembimbing != $pembimbing->id_pembimbing) {
                return redirect()->back()->with('error', 'Akses ditolak.');
            }

            $updateData = [
                'verifikasi' => $request->status,
                'keterangan' => $request->keterangan ?? $absensi->keterangan,
            ];

            // Jika ditolak dan status presensi adalah hadir/izin/sakit → ubah jadi alpa
            if ($request->status === 'rejected' && in_array($absensi->status, ['hadir', 'terlambat', 'izin', 'sakit'])) {
                $updateData['status'] = 'alpa';
            }

            $absensi->update($updateData);
        }

        return redirect()->back()->with('success', 'Absensi berhasil divalidasi.');
    }

    /**
     * Verifikasi semua logbook pending untuk siswa tertentu
     */
    public function validasiSemuaLogbook(Request $request, $nisn)
    {
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

    /**
     * Verifikasi semua absensi pending untuk siswa tertentu (termasuk Alpa otomatis)
     */
    public function validasiSemuaAbsensi(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        // 1. Identifikasi hari kerja yang kosong (Alpa otomatis) dan buat record-nya
        $internStart = $siswa->tgl_mulai_magang ? Carbon::parse($siswa->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $siswa->tgl_selesai_magang ? Carbon::parse($siswa->tgl_selesai_magang)->startOfDay() : null;
        $today       = Carbon::now()->startOfDay();

        $rangeStart = $internStart ?? Carbon::now()->startOfMonth();
        $rangeEnd   = Carbon::now();
        if ($internEnd && $rangeEnd->gt($internEnd)) {
            $rangeEnd = $internEnd->copy();
        }

        // Ambil data yang sudah ada di DB
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
                    'verifikasi' => 'verified', // Langsung set verified
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

        // 2. Update semua record yang statusnya masih pending menjadi verified
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
     * Menampilkan daftar pengajuan (Lupa Absensi / Kegiatan) dari siswa binaan
     */
    public function pengajuanSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $status = $request->input('status', 'pending');

        $pengajuans = PengajuanSiswa::whereHas('siswa', function($q) use ($pembimbing) {
                $q->where('id_pembimbing', $pembimbing->id_pembimbing);
            })
            ->with('siswa')
            ->when($status, function($query, $status) {
                if ($status !== 'semua') {
                    return $query->where('status', $status);
                }
                return $query;
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembimbing.pengajuanSiswa', compact('pembimbing', 'pengajuans', 'status'));
    }

    /**
     * Memproses approval/penolakan pengajuan
     */
    public function updatePengajuan(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $pembimbing = Auth::user();
        $pengajuan = PengajuanSiswa::with('siswa')->findOrFail($id);

        if ($pengajuan->siswa->id_pembimbing !== $pembimbing->id_pembimbing) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if ($request->action === 'approve') {
            $pengajuan->status = 'valid';

            // Auto insert based on type
            if ($pengajuan->jenis === 'absensi') {
                $statusHadir = 'hadir'; // default if no jam masuk to check
                if ($pengajuan->jam_masuk && substr($pengajuan->jam_masuk, 0, 5) > '08:00') {
                    $statusHadir = 'terlambat';
                }

                $existingAbsensi = \App\Models\Absensi::where('nisn', $pengajuan->nisn)
                    ->where('tanggal', $pengajuan->tanggal)
                    ->first();

                if ($existingAbsensi) {
                    $existingAbsensi->update([
                        'jam_masuk' => $pengajuan->jam_masuk ?: $existingAbsensi->jam_masuk,
                        'jam_pulang' => $pengajuan->jam_pulang ?: $existingAbsensi->jam_pulang,
                        'status' => $statusHadir,
                        'verifikasi' => 'verified',
                        'keterangan' => 'Validasi Lupa Absensi',
                    ]);
                } else {
                    Absensi::create([
                        'nisn' => $pengajuan->nisn,
                        'tanggal' => $pengajuan->tanggal,
                        'jam_masuk' => $pengajuan->jam_masuk,
                        'jam_pulang' => $pengajuan->jam_pulang,
                        'status' => $statusHadir,
                        'verifikasi' => 'verified',
                        'keterangan' => 'Validasi Lupa Absensi',
                    ]);
                }
            } else if ($pengajuan->jenis === 'kegiatan') {
                $existingLogbook = Logbook::where('nisn', $pengajuan->nisn)
                    ->where('tanggal', $pengajuan->tanggal)
                    ->first();
                    
                if ($existingLogbook) {
                    $existingLogbook->update([
                        'kegiatan' => $pengajuan->deskripsi,
                        'status' => 'verified',
                        'catatan_pembimbing' => 'Validasi Lupa Kegiatan'
                    ]);
                } else {
                    Logbook::create([
                        'nisn' => $pengajuan->nisn,
                        'tanggal' => $pengajuan->tanggal,
                        'kegiatan' => $pengajuan->deskripsi,
                        'status' => 'verified',
                        'catatan_pembimbing' => 'Validasi Lupa Kegiatan'
                    ]);
                }
            }

            $message = 'Pengajuan berhasil disetujui dan data otomatis ditambahkan ke sistem.';

        } else {
            $pengajuan->status = 'ditolak';
            $message = 'Pengajuan ditolak.';
        }

        $pengajuan->save();

        return redirect()->back()->with('success', $message);
    }

    /**
     * Menampilkan daftar penilaian dan form evaluasi siswa
     */
    public function evaluasiSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $search = $request->input('search');

        // Base query for supervisor's students
        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing')
                  ->with('penilaianDetails.kriteria')
                  ->orderBy('created_at', 'desc');
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('sekolah', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->orderBy('nama', 'asc')->get();

        // Separate into Pending and Done (specifically for "Dosen Pembimbing")
        $siswasPending = $allSiswas->filter(function ($s) {
            return !$s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        $siswasDone = $allSiswas->filter(function ($s) {
            return $s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        $kriteriaKustom = $this->ensureKriteriaExists($pembimbing->id_pembimbing);
        $kriteria = $kriteriaKustom;
            
        return view('pembimbing.penilaianSiswa', compact(
            'pembimbing', 'siswasPending', 'siswasDone', 
            'kriteria', 'kriteriaKustom', 'search'
        ));
    }

    /**
     * Menyimpan data evaluasi/penilaian baru ke database
     */
    public function storeEvaluasi(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn',
            'kategori' => 'required|string',
            'komentar' => 'nullable|string',
            'saran' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        // Verifikasi kepemilikan
        $isMySiswa = Siswa::where('nisn', $request->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Hitung rata-rata
        $allScores = collect($request->scores);
        $avg = $allScores->avg();

        // Create Penilaian head
        $penilaian = Penilaian::create([
            'nisn' => $request->nisn,
            'pemberi_nilai' => 'Dosen Pembimbing',
            'rata_rata' => $avg,
            'kategori' => $request->kategori,
            'komentar' => $request->komentar,
            'saran' => $request->saran,
        ]);

        // Create Penilaian details
        foreach ($request->scores as $kriteriaId => $score) {
            PenilaianDetail::create([
                'id_penilaian' => $penilaian->id_penilaian,
                'id_kriteria' => $kriteriaId,
                'skor' => $score
            ]);
        }

        return redirect()->route('pembimbing.evaluasi')->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Menampilkan form input evaluasi (mode halaman penuh)
     */
    public function inputEvaluasi($nisn)
    {
        $pembimbing = Auth::user();

        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $penilaian = Penilaian::where('nisn', $nisn)
            ->where('pemberi_nilai', 'Dosen Pembimbing')
            ->with('penilaianDetails.kriteria')
            ->orderBy('created_at', 'desc')
            ->first();

        $kriteriaKustom = $this->ensureKriteriaExists($pembimbing->id_pembimbing);
        $kriteria = $kriteriaKustom;

        return view('pembimbing.penilaianSiswa', compact('pembimbing', 'siswa', 'penilaian', 'kriteria', 'kriteriaKustom'));
    }

    /**
     * Mencetak laporan siswa (dummy action to simulate PDF/Excel download in real app)
     * To truly use PDF we would need Barryvdh/DomPDF which might not be installed, 
     * so we just trigger a browser print view or return a simple view.
     */
    public function cetakLaporanSiswa(Request $request, $nisn)
    {
        $pembimbing = Auth::user();

        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing');
            }, 'penilaians.penilaianDetails.kriteria', 'absensis', 'logbooks', 'tahunAjaran'])
            ->firstOrFail();

        $penilaian = $siswa->penilaians->first();
        if (!$penilaian) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Penilaian belum di inputkan oleh Pembimbing.'], 404);
            }
            return back()->with('warning', 'Penilaian belum di inputkan oleh Pembimbing.');
        }

        $fileName = "Laporan_Siswa_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('pembimbing.cetakPenilaian', compact('pembimbing', 'siswa'));

        return $pdf->stream($fileName);
    }

    /**
     * Cetak Penilaian dari Guru/Dosen Pembimbing Kampus
     */
    public function cetakPenilaianGuru(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $user */
        $user = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $user->id_pembimbing)
            ->firstOrFail();
        
        $penilaian = $siswa->penilaians()
            ->where('pemberi_nilai', 'Guru Pembimbing')
            ->with(['penilaianDetails.kriteria'])
            ->first();

        if (!$penilaian) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Penilaian belum di inputkan oleh Guru.'], 404);
            }
            return back()->with('warning', 'Penilaian belum di inputkan oleh Guru.');
        }

        $guru = $siswa->guru;
        $pdf = Pdf::loadView('guru.printPenilaian', ['user' => $guru, 'siswa' => $siswa, 'penilaian' => $penilaian]);

        return $pdf->stream("Penilaian_Siswa_{$siswa->nisn}_{$siswa->nama}.pdf");
    }

    /**
     * Cetak Laporan Akhir Siswa
     */
    public function cetakLaporanAkhir(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $user */
        $user = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $user->id_pembimbing)
            ->firstOrFail();
        
        $laporanAkhir = $siswa->laporanAkhir;
        
        if (!$laporanAkhir) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Laporan akhir belum di inputkan oleh siswa.'], 404);
            }
            return back()->with('warning', 'Laporan akhir belum di inputkan oleh siswa.');
        }

        $path = storage_path('app/public/' . $laporanAkhir->file);
        if (!file_exists($path)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'File laporan tidak ditemukan di server.'], 404);
            }
            return back()->with('warning', 'File laporan tidak ditemukan di server.');
        }

        return response()->file($path);
    }

    /**
     * Cetak Sertifikat Siswa
     */
    public function cetakSertifikatSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $user */
        $user = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $user->id_pembimbing)
            ->firstOrFail();
        
        if ($siswa->status !== 'selesai' && !($siswa->tgl_selesai_magang && \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->lt(\Carbon\Carbon::now()))) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.'], 404);
            }
            return back()->with('info', 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.');
        }

        $siswa->load(['pembimbing', 'tahunAjaran']);
        $fileName = "Sertifikat_Magang_{$siswa->nisn}.pdf";

        $pdf = Pdf::loadView('siswa.sertifikat', ['user' => $siswa])
            ->setPaper('a4', 'landscape');
        
        return $pdf->stream($fileName);
    }

    /**
     * Menampilkan halaman profil dosen
     */
    public function profil()
    {
        $pembimbing = Auth::user();
        return view('pembimbing.profil', compact('pembimbing'));
    }

    /**
     * Menyimpan pembaruan profil dosen
     */
    public function updateProfil(Request $request)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pembimbing,email,' . $pembimbing->id_pembimbing . ',id_pembimbing',
            'no_telp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'instansi' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $pembimbing->nama = $request->nama;
        $pembimbing->email = $request->email;
        $pembimbing->no_telp = $request->no_telp;
        $pembimbing->jabatan = $request->jabatan;
        $pembimbing->instansi = $request->instansi;

        if ($request->filled('password')) {
            $pembimbing->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $pembimbing->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Menampilkan halaman manajemen kriteria penilaian.
     */
    public function kriteriaPenilaian()
    {
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        
        // Ambil kriteria kustom milik pembimbing
        $kriteriaKustom = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        // Ambil kriteria default (sebagai referensi)
        $kriteriaDefault = KriteriaPenilaian::whereNull('id_pembimbing')
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        return view('pembimbing.kriteria', compact('pembimbing', 'kriteriaKustom', 'kriteriaDefault'));
    }

    /**
     * Menyimpan kriteria penilaian baru.
     */
    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;

        KriteriaPenilaian::create([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
            'id_pembimbing' => $supervisorId,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    /**
     * Memperbarui kriteria penilaian.
     */
    public function updateKriteria(Request $request, $id)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus kriteria penilaian.
     */
    public function destroyKriteria($id)
    {
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
