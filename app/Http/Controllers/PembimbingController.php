<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pembimbing;
use App\Models\Logbook;
use App\Models\KriteriaPenilaian;
use App\Models\PenilaianDetail;
use App\Models\Penilaian;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PembimbingController extends Controller
{
    /**
     * Menampilkan daftar siswa binaan yang dibimbing oleh dosen login.
     */
    public function daftarSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $search = $request->input('search');
        $today = Carbon::now()->toDateString();

        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing);

        // Search feature
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->with([
            'guru',
            'tahunAjaran',
            'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            },
            'penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing');
            }
        ])->orderBy('nama', 'asc')->get();

        // Hitung progress untuk setiap siswa bimbingan
        foreach ($allSiswas as $siswa) {
            if ($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang) {
                $start = Carbon::parse($siswa->tgl_mulai_magang);
                $end = Carbon::parse($siswa->tgl_selesai_magang);
                $totalDays = max(1, $start->diffInDays($end));
                $daysPassed = $start->isFuture() ? 0 : (int) $start->diffInDays(Carbon::now());
                $siswa->progress_percent = min(100, round(($daysPassed / $totalDays) * 100));
            } else {
                $siswa->progress_percent = 0;
            }

            // Status hari ini
            $siswa->absen_hari_ini = $siswa->absensis->first();
        }

        // Separate into Active/Bimbingan and History based on internship status
        // status accessor in Siswa model handles tgl_selesai_magang automatically
        $siswasActive = $allSiswas->filter(function ($s) {
            return $s->status !== 'selesai';
        });

        $siswasHistory = $allSiswas->filter(function ($s) {
            return $s->status === 'selesai';
        });

        return view('pembimbing.daftarSiswa', compact('pembimbing', 'siswasActive', 'siswasHistory', 'search'));
    }

    /**
     * Mencetak jurnal kegiatan mingguan siswa binaan (menggunakan template siswa)
     */
    public function cetakJurnalSiswa($nisn)
    {
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['pembimbing', 'logbooks' => function($q) {
                $q->orderBy('tanggal', 'asc');
            }])->firstOrFail();

        $logbooks = $user->logbooks;
        $fileName = "Jurnal_Kegiatan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', compact('user', 'logbooks'));
        return $pdf->download($fileName);
    }

    /**
     * Mencetak rekap absensi individu siswa binaan (menggunakan template siswa)
     */
    public function cetakAbsensiSiswa($nisn)
    {
        $pembimbing = Auth::user();
        $user = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with('absensis')->firstOrFail();

        $absensis = $user->absensis()->orderBy('tanggal', 'asc')->get();
        $fileName = "Rekap_Absensi_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', compact('user', 'absensis'));
        return $pdf->download($fileName);
    }

    /**
     * Menampilkan logbook/kegiatan siswa tertentu.
     */
    public function logbookSiswa(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)->where('nisn', $nisn)->firstOrFail();

        $status = $request->input('status');
        $query = $siswa->logbooks();

        if ($status && in_array($status, ['pending', 'verified', 'rejected'])) {
            $query->where('status', $status);
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('pembimbing.logbookSiswa', compact('pembimbing', 'siswa', 'logbooks', 'status'));
    }

    /**
     * Menampilkan rekap absensi siswa tertentu.
     */
    public function absensiSiswa(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)->where('nisn', $nisn)->with('absensis')->firstOrFail();

        $statusVerifikasi = $request->input('status_verifikasi');
        $query = $siswa->absensis();

        if ($statusVerifikasi && in_array($statusVerifikasi, ['pending', 'verified', 'rejected'])) {
            $query->where('verifikasi', $statusVerifikasi);
        }

        $absensis = $query->orderBy('tanggal', 'desc')->paginate(15);

        $rekap = [
            'total' => $siswa->absensis()->count(),
            'hadir' => $siswa->absensis()->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $siswa->absensis()->where('status', 'izin')->count(),
            'sakit' => $siswa->absensis()->where('status', 'sakit')->count(),
            'alpa' => $siswa->absensis()->where('status', 'alpa')->count(),
        ];

        return view('pembimbing.absensiSiswa', compact('pembimbing', 'siswa', 'absensis', 'rekap', 'statusVerifikasi'));
    }

    /**
     * Memproses validasi logbook (Approve/Reject) beserta komentar dosen
     */
    public function validasiLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan_pembimbing' => 'nullable|string'
        ]);

        $logbook = Logbook::findOrFail($id);

        // Verifikasi bahwa logbook ini milik siswa yang dibimbing oleh dosen login
        $pembimbing = Auth::user();
        $isMySiswa = Siswa::where('nisn', $logbook->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $logbook->status = $request->status;
        $logbook->catatan_pembimbing = $request->catatan_pembimbing;
        $logbook->save();

        return redirect()->back()->with('success', 'Logbook berhasil divalidasi.');
    }

    /**
     * Memproses validasi absensi (Approve/Reject)
     */
    public function validasiAbsensi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'keterangan' => 'nullable|string'
        ]);

        $absensi = \App\Models\Absensi::findOrFail($id);

        // Verifikasi bahwa absensi ini milik siswa yang dibimbing oleh dosen login
        $pembimbing = Auth::user();
        $isMySiswa = Siswa::where('nisn', $absensi->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $absensi->verifikasi = $request->status;
        if ($request->has('keterangan')) {
            $absensi->keterangan = $request->keterangan;
        }
        $absensi->save();

        return redirect()->back()->with('success', 'Absensi berhasil divalidasi.');
    }

    /**
     * Verifikasi semua logbook pending untuk siswa tertentu
     */
    public function validasiSemuaLogbook(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $updatedCount = Logbook::where('nisn', $nisn)
            ->where('status', 'pending')
            ->update([
                'status' => 'verified',
                'catatan_pembimbing' => 'Validasi massal oleh pembimbing'
            ]);

        if ($updatedCount > 0) {
            return redirect()->back()->with('success', $updatedCount . ' logbook berhasil divalidasi sekaligus.');
        }

        return redirect()->back()->with('info', 'Tidak ada logbook dengan status pending untuk divalidasi.');
    }

    /**
     * Verifikasi semua absensi pending untuk siswa tertentu
     */
    public function validasiSemuaAbsensi(Request $request, $nisn)
    {
        $pembimbing = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $updatedCount = \App\Models\Absensi::where('nisn', $nisn)
            ->where('verifikasi', 'pending')
            ->update([
                'verifikasi' => 'verified'
            ]);

        if ($updatedCount > 0) {
            return redirect()->back()->with('success', $updatedCount . ' absensi berhasil divalidasi sekaligus.');
        }

        return redirect()->back()->with('info', 'Tidak ada absensi dengan status pending untuk divalidasi.');
    }

    /**
     * Menampilkan daftar penilaian dan form evaluasi siswa
     */
    public function evaluasiSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $search = $request->input('search');

        // Base query for supervisor's students
        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing')
                  ->with('penilaianDetails.kriteria')
                  ->orderBy('created_at', 'desc');
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('sekolah', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->orderBy('nama', 'asc')->get();

        // Separate into Pending and Done (specifically for "Dosen Pembimbing")
        $siswasPending = $allSiswas->filter(function ($s) {
            return !$s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        $siswasDone = $allSiswas->filter(function ($s) {
            return $s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        // Get criteria for the modal/form
        $kriteria = KriteriaPenilaian::orderBy('tipe')->orderBy('urutan')->get();

        return view('pembimbing.penilaianSiswa', compact('pembimbing', 'siswasPending', 'siswasDone', 'kriteria', 'search'));
    }

    /**
     * Menyimpan data evaluasi/penilaian baru ke database
     */
    public function storeEvaluasi(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn',
            'kategori' => 'required|string',
            'komentar' => 'nullable|string',
            'saran' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        $pembimbing = Auth::user();

        // Verifikasi kepemilikan
        $isMySiswa = Siswa::where('nisn', $request->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Hitung rata-rata
        $allScores = collect($request->scores);
        $avg = $allScores->avg();

        // Create Penilaian head
        $penilaian = Penilaian::create([
            'nisn' => $request->nisn,
            'pemberi_nilai' => 'Dosen Pembimbing',
            'rata_rata' => $avg,
            'kategori' => $request->kategori,
            'komentar' => $request->komentar,
            'saran' => $request->saran,
        ]);

        // Create Penilaian details
        foreach ($request->scores as $kriteriaId => $score) {
            PenilaianDetail::create([
                'id_penilaian' => $penilaian->id_penilaian,
                'id_kriteria' => $kriteriaId,
                'skor' => $score
            ]);
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Menampilkan halaman rekap laporan siswa binaan
     */
    public function laporanSiswa(Request $request)
    {
        $pembimbing = Auth::user();

        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->withCount([
                'absensis as total_hadir' => function ($q) {
                    $q->where('status', 'Hadir');
                },
                'logbooks as total_logbook',
                'logbooks as disetujui_logbook' => function ($q) {
                    $q->where('status', 'verified');
                }
            ]);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $siswas = $query->paginate(10);

        return view('pembimbing.laporanSiswa', compact('pembimbing', 'siswas'));
    }

    /**
     * Mencetak laporan siswa (dummy action to simulate PDF/Excel download in real app)
     * To truly use PDF we would need Barryvdh/DomPDF which might not be installed, 
     * so we just trigger a browser print view or return a simple view.
     */
    public function cetakLaporanSiswa($nisn)
    {
        $pembimbing = Auth::user();

        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['penilaians.penilaianDetails.kriteria', 'absensis', 'logbooks', 'tahunAjaran'])
            ->firstOrFail();

        $fileName = "Laporan_Siswa_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('pembimbing.cetakPenilaian', compact('pembimbing', 'siswa'));

        return $pdf->download($fileName);
    }

    /**
     * Menampilkan halaman profil dosen
     */
    public function profil()
    {
        $pembimbing = Auth::user();
        return view('pembimbing.profil', compact('pembimbing'));
    }

    /**
     * Menyimpan pembaruan profil dosen
     */
    public function updateProfil(Request $request)
    {
        $pembimbing = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pembimbing,email,' . $pembimbing->id_pembimbing . ',id_pembimbing',
            'no_telp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'instansi' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $pembimbing->nama = $request->nama;
        $pembimbing->email = $request->email;
        $pembimbing->no_telp = $request->no_telp;
        $pembimbing->jabatan = $request->jabatan;
        $pembimbing->instansi = $request->instansi;

        if ($request->filled('password')) {
            $pembimbing->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $pembimbing->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
