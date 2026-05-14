<?php

namespace App\Http\Controllers;

use App\Models\InformasiDashboard;
use App\Models\ProgramStudi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SiswaDashboardController extends SiswaBaseController
{
    public function dashboard()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        $progress = $this->calculateProgress($user);
        $hariDijalani = $user->tgl_mulai_magang ? max(0, (int) Carbon::parse($user->tgl_mulai_magang)->diffInDays(Carbon::now())) : 0;

        $logbookTerisi = $user->logbooks()->count();
        $logbookVerified = $user->logbooks()->where('status', 'verified')->count();
        $totalHadir = $user->absensis()->whereIn('status', ['hadir', 'terlambat'])->count();

        $absensiHariIni = $user->absensis()->whereDate('tanggal', $today)->first();
        $logbookHariIni = $user->logbooks()->whereDate('tanggal', $today)->first();

        $informasi = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan')->get();
        $isFinished = $this->isMagangSelesai($user);

        return view('siswa.siswa', compact(
            'user', 'progress', 'hariDijalani', 'logbookTerisi', 'logbookVerified',
            'absensiHariIni', 'logbookHariIni', 'totalHadir', 'informasi', 'programStudis', 'isFinished'
        ));
    }
}
