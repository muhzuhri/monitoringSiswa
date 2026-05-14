<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Pembimbing;
use App\Models\InformasiDashboard;
use App\Models\ProgramStudi;

class PimpinanDashboardController extends PimpinanBaseController
{
    public function index()
    {
        $user = $this->authorizePimpinan();
        
        $stats = [
            'total_siswa' => Siswa::count(),
            'siswa_aktif' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => Guru::count(),
            'total_pembimbing' => Pembimbing::count(),
        ];

        $informasi = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan', 'asc')->get();

        return view('pimpinan.home', compact('user', 'stats', 'informasi', 'programStudis'));
    }
}
