<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\KonfigurasiLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaLaporanController extends SiswaBaseController
{
    /**
     * Menampilkan halaman Laporan Siswa.
     */
    public function laporan()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();


        // Laporan Akhir
        $laporanAkhir = $user->laporanAkhir;

        // Rekap Absensi (Keseluruhan - Dari awal magang)
        $absensiData = $this->getFullAttendanceData($user);

        $rekapAbsensi = [
            'hadir' => $absensiData->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $absensiData->where('status', 'izin')->count(),
            'sakit' => $absensiData->where('status', 'sakit')->count(),
            'alpa' => $absensiData->where('status', 'alpa')->count(),
        ];

        // Penilaian (Both from Supervisor and Teacher)
        $penilaians = $user->penilaians()
            ->with('penilaianDetails.kriteria')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.laporan', compact('user', 'laporanAkhir', 'rekapAbsensi', 'penilaians'));
    }

    /**
     * Mengunggah Laporan Akhir PKL.
     */
    public function uploadLaporanAkhir(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $request->validate([
            'file_laporan' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        $file = $request->file('file_laporan');
        $filename = 'Laporan_Akhir_' . str_replace(' ', '_', $user->nama) . '_' . $user->nisn . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('laporan_akhir', $filename, 'public');

        $user->laporanAkhirs()->create([
            'nisn' => $user->nisn,
            'file' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan akhir berhasil diunggah.');
    }


    /**
     * Preview / Download Laporan Akhir.
     */
    public function previewLaporanAkhir(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $laporanAkhir = $user->laporanAkhir;
        if (!$laporanAkhir) {
            return abort(404, 'Laporan tidak ditemukan.');
        }

        $path = storage_path('app/public/' . $laporanAkhir->file);
        if (!file_exists($path)) {
            return abort(404, 'File laporan tidak ditemukan.');
        }

        if ($request->has('download')) {
            return response()->download($path);
        }

        return response()->file($path);
    }

    /**
     * Download Jurnal Kegiatan Mingguan.
     */
    public function downloadJurnalMingguan(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Fetch logs including Alphas
        $logbooks = $this->getFullLogbookData($user)
            ->sortBy('tanggal')
            ->values();
        $user->load('pembimbing');

        // Jurnal Mingguan version (Table: No, Tanggal, Pembimbing Lapangan, Kegiatan, Status)
        $fileName = "Jurnal_Mingguan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', [
            'user' => $user,
            'logbooks' => $logbooks,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'kegiatan_mingguan')->first()
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Individu).
     */
    public function downloadRekapAbsensiIndividu(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Fetch attendance including dynamic Alphas
        $absensis = $this->getFullAttendanceData($user)
            ->sortBy('tanggal')
            ->values();

        // Hitung juga ringkasannya
        $rekapAbsensi = [
            'hadir' => $absensis->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $absensis->where('status', 'izin')->count(),
            'sakit' => $absensis->where('status', 'sakit')->count(),
            'alpa' => $absensis->where('status', 'alpa')->count(),
        ];

        $fileName = "Rekap_Absensi_Individu_{$user->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', [
            'user' => $user,
            'absensis' => $absensis,
            'rekapAbsensi' => $rekapAbsensi,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'absensi_individu')->first()
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Berkelompok/Bulanan).
     */
    public function downloadRekapAbsensiKelompok(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Find the earliest and latest attendance record date for the group
        $groupNisns = function($query) use ($user) {
            $query->select('nisn')->from('siswa')
                  ->where('nisn_ketua', $user->nisn_ketua ?: $user->nisn)
                  ->orWhere('nisn', $user->nisn_ketua ?: $user->nisn);
        };

        $firstAbsen = Absensi::whereIn('nisn', $groupNisns)->min('tanggal');
        $latestAbsen = Absensi::whereIn('nisn', $groupNisns)->max('tanggal');

        $startRange = $firstAbsen ? Carbon::parse($firstAbsen)->startOfMonth() : Carbon::now()->startOfMonth();
        $endRange = $latestAbsen ? Carbon::parse($latestAbsen)->endOfMonth() : Carbon::now()->endOfMonth();
        
        // Calculate months between start of first attendance and end of latest attendance
        $months = [];
        $current = $startRange->copy();
        
        while ($current <= $endRange) {
            $month = $current->month;
            $year = $current->year;
            
            // Fetch all group members if any
            $anggota = [];
            if ($user->nisn_ketua) {
                $anggota = Siswa::where('nisn_ketua', $user->nisn_ketua)->with(['absensis' => function($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
                }])->get();
            } else {
                // If not in a group, just the user
                $userClone = clone $user;
                $userClone->load(['absensis' => function($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
                }]);
                $anggota = collect([$userClone]);
            }

            $months[] = [
                'month' => $month,
                'year' => $year,
                'anggota' => $anggota,
                'daysInMonth' => $current->daysInMonth,
                'monthName' => $current->translatedFormat('F')
            ];
            
            $current->addMonth();
        }

        $fileName = "Rekap_Absensi_Kelompok_{$user->nisn}.pdf";

        $pdf = Pdf::loadView('siswa.rekapAbsensiKelompok', [
            'user' => $user,
            'months' => $months,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'absensi_kelompok')->first()
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Penilian Siswa.
     */
    public function cetakPenilaian(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        $penilaianId = $request->query('id_penilaian');
        
        if (!$penilaianId) {
            return back()->with('error', 'ID Penilaian tidak ditemukan.');
        }

        $penilaian = \App\Models\Penilaian::where('id_penilaian', $penilaianId)
            ->where('nisn', $user->nisn)
            ->with(['penilaianDetails.kriteria'])
            ->firstOrFail();

        $siswa = $user->load(['pembimbing', 'tahunAjaran', 'guru']);
        $pembimbing = $siswa->pembimbing;

        $fileName = "Penilaian_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";

        // Determine which template to use based on pemberi_nilai
        $viewTemplate = ($penilaian->pemberi_nilai == 'Guru Pembimbing') ? 'guru.printPenilaian' : 'pembimbing.cetakPenilaian';

        $pdf = Pdf::loadView($viewTemplate, [
            'siswa' => $siswa,
            'pembimbing' => $pembimbing,
            'penilaian' => $penilaian,
            'user' => $siswa->guru, // Pass guru if needed for guru template
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', $penilaian->pemberi_nilai == 'Guru Pembimbing' ? 'penilaian_guru' : 'penilaian_pembimbing')->first()
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Cetak Sertifikat Magang Siswa.
     */
    public function cetakSertifikat(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        // Sertifikat hanya tersedia jika status 'selesai' DAN sudah ada penilaian dari Guru & Pembimbing
        $hasGuruPenilaian = $user->penilaians()->where('pemberi_nilai', 'Guru Pembimbing')->exists();
        $hasPembimbingPenilaian = $user->penilaians()->where('pemberi_nilai', 'Dosen Pembimbing')->exists();

        if ($user->status !== 'selesai' || !$hasGuruPenilaian || !$hasPembimbingPenilaian) {
            return back()->with('info', 'Sertifikat akan tersedia setelah Anda menyelesaikan magang dan mendapatkan penilaian lengkap dari Guru Pembimbing dan Pembimbing Lapangan.');
        }

        $user->load(['pembimbing', 'tahunAjaran']);
        
        $fileName = "Sertifikat_Magang_{$user->nisn}.pdf";

        $pdf = Pdf::loadView('siswa.sertifikat', [
            'user' => $user,
            'konfigurasi' => KonfigurasiLaporan::where('tipe_laporan', 'sertifikat')->first()
        ])
            ->setPaper('a4', 'landscape');
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }
}
