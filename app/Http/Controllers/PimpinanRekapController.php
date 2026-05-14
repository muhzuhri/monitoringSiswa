<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class PimpinanRekapController extends PimpinanBaseController
{
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

        $tahunAjarans = TahunAjaran::orderBy('tahun_ajaran', 'desc')->get();

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
