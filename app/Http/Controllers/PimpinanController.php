<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Pembimbing;
use App\Models\Pimpinan;
use App\Models\TahunAjaran;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PimpinanController extends Controller
{
    protected function authorizePimpinan(): Pimpinan
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'pimpinan', 403);

        return $user;
    }

    public function index()
    {
        $user = $this->authorizePimpinan();
        
        $stats = [
            'total_siswa' => Siswa::count(),
            'siswa_aktif' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => Guru::count(),
            'total_pembimbing' => Pembimbing::count(),
        ];

        return view('pimpinan.home', compact('user', 'stats'));
    }

    public function siswa(Request $request)
    {
        $user = $this->authorizePimpinan();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = \Carbon\Carbon::now()->toDateString();

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

        $siswa = $query->orderBy('nama')->paginate(100);

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
        $lokasis = \App\Models\LokasiAbsensi::orderBy('nama_lokasi')->get();

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
        $guru = Guru::with('siswas')->orderBy('nama')->paginate(10);

        return view('pimpinan.guru', compact('user', 'guru'));
    }

    public function pembimbing()
    {
        $user = $this->authorizePimpinan();
        $pembimbing = Pembimbing::with('siswas')->orderBy('nama')->paginate(10);

        return view('pimpinan.pembimbing', compact('user', 'pembimbing'));
    }

    public function rekap()
    {
        $user = $this->authorizePimpinan();
        
        $stats = [
            'siswa_aktif' => Siswa::where(function($q) {
                $q->where('status', '!=', 'selesai')
                  ->orWhereNull('status');
            })->where(function($q) {
                $q->where('tgl_selesai_magang', '>=', now())
                  ->orWhereNull('tgl_selesai_magang');
            })->count(),
            'siswa_selesai' => Siswa::where('status', 'selesai')
                ->orWhere('tgl_selesai_magang', '<', now())
                ->count(),
            'total_siswa' => Siswa::count(),
            'total_guru' => Guru::count(),
        ];

        $tahunAjarans = \App\Models\TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

        return view('pimpinan.rekap', compact('user', 'stats', 'tahunAjarans'));
    }

    public function rekapStats(Request $request)
    {
        $this->authorizePimpinan();
        $periode = $request->input('periode');

        $queryAktif = Siswa::where(function($q) {
            $q->where('status', '!=', 'selesai')
              ->orWhereNull('status');
        })->where(function($q) {
            $q->where('tgl_selesai_magang', '>=', now())
              ->orWhereNull('tgl_selesai_magang');
        });

        $querySelesai = Siswa::where(function($q) {
            $q->where('status', 'selesai')
              ->orWhere('tgl_selesai_magang', '<', now());
        });

        $queryTotal = Siswa::query();
        $queryGuru  = Guru::query();

        if ($periode) {
            $queryAktif->where('id_tahun_ajaran', $periode);
            $querySelesai->where('id_tahun_ajaran', $periode);
            $queryTotal->where('id_tahun_ajaran', $periode);
            $queryGuru->where('id_tahun_ajaran', $periode);
        }

        return response()->json([
            'siswa_aktif'   => $queryAktif->count(),
            'siswa_selesai' => $querySelesai->count(),
            'total_siswa'   => $queryTotal->count(),
            'total_guru'    => $queryGuru->count(),
        ]);
    }
}
