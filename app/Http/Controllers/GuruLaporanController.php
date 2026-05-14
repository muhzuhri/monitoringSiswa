<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KonfigurasiLaporan;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GuruLaporanController extends GuruBaseController
{
    /**
     * Mengekspor hasil penilaian ke PDF.
     */
    public function exportPenilaian(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
        $penilaian = $siswa->penilaians()->where('pemberi_nilai', 'Guru Pembimbing')
            ->with(['penilaianDetails.kriteria'])->first();

        if (!$penilaian) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Penilaian belum di inputkan oleh Guru.'], 404);
            }
            return back()->with('warning', 'Penilaian belum di inputkan oleh Guru.');
        }

        $pdf = Pdf::loadView('guru.printPenilaian', [
            'user' => $user, 
            'siswa' => $siswa, 
            'penilaian' => $penilaian,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'penilaian_guru')->first()
        ]);

        return $pdf->stream("Penilaian_Siswa_{$siswa->nisn}_{$siswa->nama}.pdf");
    }

    /**
     * Download Jurnal Kegiatan Mingguan Siswa.
     */
    public function downloadJurnalMingguan(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $logbooks = $siswa->logbooks()->orderBy('tanggal', 'asc')->get();
        $siswa->load('pembimbing');

        $fileName = "Jurnal_Mingguan_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.printJurnal', [
            'user' => $siswa,
            'logbooks' => $logbooks,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'kegiatan_mingguan')->first()
        ]);

        if ($request->has('download')) return $pdf->download($fileName);
        return $pdf->stream($fileName);
    }
     
    /**
     * Download Rekap Absensi (Individu) Siswa.
     */
    public function downloadRekapAbsensiIndividu(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $absensis = $siswa->absensis()->orderBy('tanggal', 'asc')->get();
        $rekapAbsensi = [
            'hadir' => $absensis->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alpa' => $absensis->where('status', 'alpa')->count(),
        ];

        $fileName = "Rekap_Absensi_Individu_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', [
            'user' => $siswa,
            'absensis' => $absensis,
            'rekapAbsensi' => $rekapAbsensi,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'absensi_individu')->first()
        ]);

        if ($request->has('download')) return $pdf->download($fileName);
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Kelompok) Siswa.
     */
    public function downloadRekapAbsensiKelompok(Request $request, $nisnKetua)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $leader = $user->siswas()->where('nisn', $nisnKetua)->firstOrFail();

        $groupNisns = function($query) use ($nisnKetua) {
            $query->select('nisn')->from('siswa')->where('nisn_ketua', $nisnKetua)->orWhere('nisn', $nisnKetua);
        };

        $firstAbsen = Absensi::whereIn('nisn', $groupNisns)->min('tanggal');
        $latestAbsen = Absensi::whereIn('nisn', $groupNisns)->max('tanggal');

        $startRange = $firstAbsen ? Carbon::parse($firstAbsen)->startOfMonth() : Carbon::now()->startOfMonth();
        $endRange = $latestAbsen ? Carbon::parse($latestAbsen)->endOfMonth() : Carbon::now()->endOfMonth();
        
        $months = [];
        $current = $startRange->copy();
        
        while ($current <= $endRange) {
            $month = $current->month;
            $year = $current->year;
            
            $anggota = Siswa::where(function($q) use ($nisnKetua) {
                $q->where('nisn_ketua', $nisnKetua)->orWhere('nisn', $nisnKetua);
            })->with(['absensis' => fn($q) => $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)])->get();

            $months[] = [
                'month' => $month, 'year' => $year, 'anggota' => $anggota,
                'daysInMonth' => $current->daysInMonth, 'monthName' => $current->translatedFormat('F')
            ];
            $current->addMonth();
        }

        $fileName = "Rekap_Absensi_Kelompok_{$leader->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiKelompok', [
            'user' => $leader, 'months' => $months,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'absensi_kelompok')->first()
        ]);
        
        if ($request->has('download')) return $pdf->download($fileName);
        return $pdf->stream($fileName);
    }

    /**
     * Cetak Penilaian dari Pembimbing Lapangan
     */
    public function cetakPenilaianPembimbing(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)
            ->with(['penilaians' => fn($q) => $q->where('pemberi_nilai', 'Dosen Pembimbing'), 'penilaians.penilaianDetails.kriteria', 'tahunAjaran'])
            ->firstOrFail();

        $pembimbing = $siswa->pembimbing;
        if (!$pembimbing) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'Siswa tidak memiliki pembimbing lapangan.'], 404);
            return back()->with('warning', 'Siswa tidak memiliki pembimbing lapangan.');
        }

        $penilaian = $siswa->penilaians->first();
        if (!$penilaian) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'Penilaian belum di inputkan oleh Pembimbing Lapangan.'], 404);
            return back()->with('warning', 'Penilaian belum di inputkan oleh Pembimbing.');
        }

        $fileName = "Laporan_Siswa_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('pembimbing.cetakPenilaian', [
            'pembimbing' => $pembimbing, 'siswa' => $siswa,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'penilaian_pembimbing')->first()
        ]);
        return $pdf->stream($fileName);
    }

    /**
     * Cetak Sertifikat Magang Siswa
     */
    public function cetakSertifikatSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
        if ($siswa->status !== 'selesai' && !($siswa->tgl_selesai_magang && Carbon::parse($siswa->tgl_selesai_magang)->lt(Carbon::now()))) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.'], 404);
            return back()->with('info', 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.');
        }

        $siswa->load(['pembimbing', 'tahunAjaran']);
        $fileName = "Sertifikat_Magang_{$siswa->nisn}.pdf";
        $pdf = Pdf::loadView('siswa.sertifikat', [
            'user' => $siswa, 'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'sertifikat')->first()
        ])->setPaper('a4', 'landscape');
        return $pdf->stream($fileName);
    }

    /**
     * Unduh file laporan akhir siswa
     */
    public function cetakLaporanAkhir(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        $laporanAkhir = $siswa->laporanAkhir;
        
        if (!$laporanAkhir) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'Laporan akhir belum di inputkan oleh siswa.'], 404);
            return back()->with('warning', 'Laporan akhir belum di inputkan oleh siswa.');
        }

        $path = storage_path('app/public/' . $laporanAkhir->file);
        if (!file_exists($path)) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'File laporan tidak ditemukan di server.'], 404);
            return back()->with('warning', 'File laporan tidak ditemukan di server.');
        }

        return response()->file($path);
    }
}
