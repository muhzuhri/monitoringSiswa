<?php

namespace App\Http\Controllers;

use App\Models\InformasiDashboard;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Auth;

class GuruDashboardController extends GuruBaseController
{
    /**
     * Menampilkan dashboard Guru Pembimbing.
     */
    public function dashboard()
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        
        $informasi = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan')->get();

        return view('guru.guru', compact('user', 'informasi', 'programStudis'));
    }
}
