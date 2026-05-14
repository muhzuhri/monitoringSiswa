<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Pembimbing;
use App\Models\TahunAjaran;
use App\Models\LokasiAbsensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PimpinanMonitoringController extends PimpinanBaseController
{
    public function siswa(Request $request)
    {
        $user = $this->authorizePimpinan();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = Carbon::now()->toDateString();

        // 1. Siswa Aktif
        $query = Siswa::with(['guru', 'pembimbing', 'absensis' => function ($q) use ($today) {
            $q->whereDate('tanggal', $today);
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        // Filter Hanya Siswa Aktif
        $query->where(function($q) {
            $q->where(function($sq) {
                $sq->where('status', '!=', 'selesai')
                   ->orWhereNull('status');
            })->where(function($subQ) {
                $subQ->where('tgl_selesai_magang', '>=', now())
                     ->orWhereNull('tgl_selesai_magang');
            });
        });

        $siswa = $query->orderBy('nama', 'asc')->paginate(100);

        // Grouping logic for Siswa Aktif
        $groupedSiswas = $siswa->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok'
            ];
        });

        // Hitung status hari ini
        foreach ($siswa as $s) {
            $s->absen_hari_ini = $s->absensis->first();
        }

        // 2. Riwayat Siswa (Selesai)
        $riwayatQuery = Siswa::with(['guru', 'pembimbing'])
            ->where(function($q) {
                $q->where('status', 'selesai')
                  ->orWhere('tgl_selesai_magang', '<', now());
            });

        if ($periodeId) {
            $riwayatQuery->where('id_tahun_ajaran', $periodeId);
        }

        if ($search) {
            $riwayatQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        $riwayatSiswas = $riwayatQuery->orderBy('tgl_selesai_magang', 'desc')->get();

        $groupedRiwayat = $riwayatSiswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok'
            ];
        });

        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
        $lokasis = LokasiAbsensi::orderBy('nama_lokasi', 'asc')->get();

        return view('pimpinan.siswa', compact(
            'user',
            'siswa',
            'groupedSiswas',
            'riwayatSiswas',
            'groupedRiwayat',
            'search',
            'periodeId',
            'periodeOptions',
            'lokasis'
        ));
    }

    public function guru()
    {
        $user = $this->authorizePimpinan();
        $guru = Guru::with('siswas')->orderBy('nama', 'asc')->paginate(10);
        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();

        return view('pimpinan.guru', compact('user', 'guru', 'periodeOptions'));
    }

    public function pembimbing()
    {
        $user = $this->authorizePimpinan();
        $pembimbing = Pembimbing::with('siswas')->orderBy('nama', 'asc')->paginate(10);
        $periodeOptions = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();

        return view('pimpinan.pembimbing', compact('user', 'pembimbing', 'periodeOptions'));
    }
}
