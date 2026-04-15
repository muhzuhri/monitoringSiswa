<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Admin;
use App\Contracts\HasRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminRekapController extends Controller
{
    protected function authorizeAccess(): Admin|\App\Models\Pimpinan
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && in_array($role, ['admin', 'pimpinan']), 403);
        return $user;
    }

    public function index()
    {
        $user = $this->authorizeAccess();
        
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

        return view('admin.rekap', compact('user', 'stats', 'tahunAjarans'));
    }

    public function rekapSiswaAktif(Request $request)
    {
        $this->authorizeAccess();
        $periode = $request->input('periode');

        $query = Siswa::where(function($q) {
            $q->where('status', '!=', 'selesai')
              ->orWhereNull('status');
        })->where(function($q) {
            $q->where('tgl_selesai_magang', '>=', now())
              ->orWhereNull('tgl_selesai_magang');
        });

        if ($periode) {
            $query->where('id_tahun_ajaran', $periode);
        }

        $data = $query->orderBy('nama')->get();
        $ta = $periode ? \App\Models\TahunAjaran::find($periode) : null;

        return $this->generateSiswaPdf($data, 'Daftar Siswa Magang Aktif', $request->has('download'), $ta);
    }

    public function rekapSiswaSelesai(Request $request)
    {
        $this->authorizeAccess();
        $periode = $request->input('periode');

        $query = Siswa::where(function($q) {
            $q->where('status', 'selesai')
              ->orWhere('tgl_selesai_magang', '<', now());
        });

        if ($periode) {
            $query->where('id_tahun_ajaran', $periode);
        }

        $data = $query->orderBy('nama')->get();
        $ta = $periode ? \App\Models\TahunAjaran::find($periode) : null;

        return $this->generateSiswaPdf($data, 'Daftar Siswa Magang Selesai', $request->has('download'), $ta);
    }

    public function rekapSiswaTotal(Request $request)
    {
        $this->authorizeAccess();
        $periode = $request->input('periode');

        $query = Siswa::query();

        if ($periode) {
            $query->where('id_tahun_ajaran', $periode);
        }

        $data = $query->orderBy('nama')->get();
        $ta = $periode ? \App\Models\TahunAjaran::find($periode) : null;

        return $this->generateSiswaPdf($data, 'Daftar Total Siswa Magang', $request->has('download'), $ta);
    }

    public function rekapGuru(Request $request)
    {
        $this->authorizeAccess();
        $periode = $request->input('periode');

        $query = Guru::query();
        if ($periode) {
            $query->where('id_tahun_ajaran', $periode);
        }

        $data = $query->orderBy('nama')->get();
        $ta = $periode ? \App\Models\TahunAjaran::find($periode) : null;

        $pdf = Pdf::loadView('admin.pdf.rekapGuru', [
            'items' => $data,
            'title' => 'Laporan Rekap Guru Pembimbing',
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $ta ? $ta->tahun_ajaran : null
        ]);

        $fileName = "Rekap_Guru_" . date('d_M_Y') . ".pdf";
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    private function generateSiswaPdf($data, $title, $shouldDownload, $ta = null)
    {
        $pdf = Pdf::loadView('admin.pdf.rekapSiswa', [
            'items' => $data,
            'title' => $title,
            'date' => Carbon::now()->translatedFormat('d F Y'),
            'tahun_ajaran' => $ta ? $ta->tahun_ajaran : null
        ]);

        $fileName = str_replace(' ', '_', $title) . "_" . date('d_M_Y') . ".pdf";
        if ($shouldDownload) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }
}
