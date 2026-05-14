<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Logbook;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GuruSiswaController extends GuruBaseController
{
    /**
     * Menampilkan daftar semua siswa bimbingan.
     */
    public function daftarSiswa(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = Carbon::now()->toDateString();
        $npsn = $user->npsn;

        $query = $user->siswas()->where(function($q) use ($today) {
            $q->where(function($sub) {
                $sub->where('status', '!=', 'selesai')->orWhereNull('status');
            })->where(function($sub) use ($today) {
                $sub->whereNull('tgl_selesai_magang')->orWhere('tgl_selesai_magang', '>=', $today);
            });
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        $siswas = $query->with(['guru', 'pembimbing', 'tahunAjaran', 'absensis' => fn($q) => $q->whereDate('tanggal', $today)])
            ->orderBy('nama', 'asc')->get();

        foreach ($siswas as $s) {
            $s->progress_percent = $this->calculateProgress($s);
            $s->absen_hari_ini = $s->absensis->first();
        }
        $groupedSiswas = $this->mapGroupedSiswa($siswas);

        $availableQuery = Siswa::whereNull('id_guru');
        if ($npsn) {
            $availableQuery->where('npsn', $npsn);
        }

        if ($search) {
            $availableQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        $availableSiswas = $availableQuery->orderBy('nama', 'asc')->get();
        $groupedAvailable = $this->mapGroupedSiswa($availableSiswas);

        $riwayatQuery = $user->siswas()->with(['guru', 'pembimbing', 'tahunAjaran'])->where(function($q) use ($today) {
            $q->where('status', 'selesai')->orWhere('tgl_selesai_magang', '<', $today);
        });
        if ($periodeId) $riwayatQuery->where('id_tahun_ajaran', $periodeId);
        $riwayatSiswas = $riwayatQuery->orderBy('tgl_selesai_magang', 'desc')->get();
        $groupedRiwayat = $this->mapGroupedSiswa($riwayatSiswas);

        $periodeOptions = TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_guru', $user->id_guru))
            ->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.daftarSiswa', compact(
            'user', 'siswas', 'search', 'npsn', 'periodeId', 'periodeOptions',
            'groupedSiswas', 'groupedAvailable', 'availableSiswas', 'riwayatSiswas', 'groupedRiwayat'
        ));
    }

    /**
     * Memilih siswa bimbingan (Claim).
     */
    public function claimSiswa($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)->whereNull('id_guru')->firstOrFail();

        if ($siswa->tipe_magang === 'kelompok') {
            Siswa::where('nisn_ketua', $siswa->nisn_ketua)->whereNull('id_guru')->update(['id_guru' => $user->id_guru]);
            $message = "Berhasil menambahkan kelompok {$siswa->nama} sebagai bimbingan Anda.";
        } else {
            $siswa->update(['id_guru' => $user->id_guru]);
            $message = "Berhasil menambahkan {$siswa->nama} sebagai siswa bimbingan Anda.";
        }

        return back()->with('success', $message);
    }

    /**
     * Menampilkan logbook kegiatan siswa tertentu.
     */
    public function logbookSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $status = $request->input('status');
        $query = $siswa->logbooks();
        if ($status) $query->where('status', $status);
        $logbooks = $query->orderBy('tanggal', 'desc')->get();

        return view('guru.logbookSiswa', compact('user', 'siswa', 'logbooks', 'status'));
    }

    /**
     * Memproses verifikasi logbook.
     */
    public function verifikasiLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan' => 'nullable|string'
        ]);

        $logbook = Logbook::findOrFail($id);
        $siswa = Siswa::where('nisn', $logbook->nisn)->first();

        if (!$siswa || $siswa->id_guru != Auth::user()->id_guru) {
            return back()->with('error', 'Akses ditolak.');
        }

        $logbook->update(['status' => $request->status, 'catatan_guru' => $request->catatan]);

        return back()->with('success', 'Logbook berhasil divalidasi.');
    }

    /**
     * Menampilkan rekap absensi siswa tertentu.
     */
    public function absensiSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->with('absensis')->firstOrFail();

        $absensis = $siswa->absensis()->orderBy('tanggal', 'desc')->paginate(15);
        $rekap = [
            'total' => $siswa->absensis()->count(),
            'hadir' => $siswa->absensis()->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $siswa->absensis()->where('status', 'izin')->count(),
            'sakit' => $siswa->absensis()->where('status', 'sakit')->count(),
            'alpa'  => $siswa->absensis()->where('status', 'alpa')->count(),
        ];

        return view('guru.absensiSiswa', compact('user', 'siswa', 'absensis', 'rekap'));
    }
}
