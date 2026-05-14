<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\KonfigurasiLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PembimbingLaporanController extends PembimbingBaseController
{
    /**
     * Mencetak jurnal kegiatan mingguan siswa binaan (menggunakan template siswa)
     */
    public function cetakJurnalSiswa($nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['pembimbing', 'logbooks' => function($q) {
                $q->orderBy('tanggal', 'asc');
            }])->firstOrFail();

        $logbooks = $user->logbooks;
        $fileName = "Jurnal_Kegiatan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', [
            'user' => $user,
            'logbooks' => $logbooks,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'kegiatan_mingguan')->first()
        ]);
        return $pdf->download($fileName);
    }

    /**
     * Mencetak rekap absensi individu siswa binaan (menggunakan template siswa)
     */
    public function cetakAbsensiSiswa($nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with('absensis')->firstOrFail();

        $absensis = $user->absensis()->orderBy('tanggal', 'asc')->get();

        $rekapAbsensi = [
            'hadir' => $absensis->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alpa' => $absensis->where('status', 'alpa')->count(),
        ];

        $fileName = "Rekap_Absensi_{$user->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', [
            'user' => $user,
            'absensis' => $absensis,
            'rekapAbsensi' => $rekapAbsensi,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'absensi_individu')->first()
        ]);
        return $pdf->download($fileName);
    }

    /**
     * Mencetak laporan siswa
     */
    public function cetakLaporanSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
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

        $pdf = Pdf::loadView('pembimbing.cetakPenilaian', [
            'pembimbing' => $pembimbing, 
            'siswa' => $siswa,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'penilaian_pembimbing')->first()
        ]);

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
        $pdf = Pdf::loadView('guru.printPenilaian', [
            'user' => $guru, 
            'siswa' => $siswa, 
            'penilaian' => $penilaian,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'penilaian_guru')->first()
        ]);

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
        
        if ($siswa->status !== 'selesai' && !($siswa->tgl_selesai_magang && Carbon::parse($siswa->tgl_selesai_magang)->lt(Carbon::now()))) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.'], 404);
            }
            return back()->with('info', 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.');
        }

        $siswa->load(['pembimbing', 'tahunAjaran']);
        $fileName = "Sertifikat_Magang_{$siswa->nisn}.pdf";

        $pdf = Pdf::loadView('siswa.sertifikat', [
            'user' => $siswa,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'sertifikat')->first()
        ])
            ->setPaper('a4', 'landscape');
        
        return $pdf->stream($fileName);
    }
}
