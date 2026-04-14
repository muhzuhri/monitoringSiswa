<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Pembimbing;
use App\Models\Logbook;
use App\Models\KriteriaPenilaian;
use App\Models\PenilaianDetail;
use App\Models\Penilaian;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PengajuanSiswa;

class PembimbingController extends Controller
{
    /**
     * Menampilkan daftar siswa binaan yang dibimbing oleh dosen login.
     */
    public function daftarSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $search     = $request->input('search');
        $periodeId  = $request->input('periode');   // filter tahun ajaran untuk riwayat
        $today      = Carbon::now()->toDateString();

        $baseQuery = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing);

        // Search feature
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        $allSiswas = (clone $baseQuery)->with([
            'guru',
            'tahunAjaran',
            'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            },
            'penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing');
            }
        ])->orderBy('nama', 'asc')->get();

        // Hitung progress & status hari ini untuk setiap siswa
        foreach ($allSiswas as $siswa) {
            if ($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang) {
                $start     = Carbon::parse($siswa->tgl_mulai_magang);
                $end       = Carbon::parse($siswa->tgl_selesai_magang);
                $totalDays = max(1, $start->diffInDays($end));
                $daysPassed = $start->isFuture() ? 0 : (int) $start->diffInDays(Carbon::now());
                $siswa->progress_percent = min(100, round(($daysPassed / $totalDays) * 100));
            } else {
                $siswa->progress_percent = 0;
            }

            // Status hari ini
            $siswa->absen_hari_ini = $siswa->absensis->first();
        }

        // Siswa Bimbingan (aktif)
        $siswasActive = $allSiswas->filter(fn($s) => $s->status !== 'selesai');

        // Siswa Riwayat (selesai) — dengan filter periode opsional
        $siswasHistory = $allSiswas->filter(function ($s) use ($periodeId) {
            if ($s->status !== 'selesai') return false;
            if ($periodeId) {
                return (string) $s->id_tahun_ajaran === (string) $periodeId;
            }
            return true;
        });

        // Ambil semua tahun ajaran yang relevan (hanya yang punya riwayat siswa selesai milik pembimbing ini)
        $periodeOptions = TahunAjaran::whereHas('siswas', function ($q) use ($pembimbing) {
            $q->where('id_pembimbing', $pembimbing->id_pembimbing);
        })->orderBy('tgl_mulai', 'desc')->get();

        return view('pembimbing.daftarSiswa', compact(
            'pembimbing', 'siswasActive', 'siswasHistory',
            'search', 'periodeId', 'periodeOptions'
        ));
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
     * Menampilkan daftar pengajuan (Lupa Absensi / Kegiatan) dari siswa binaan
     */
    public function pengajuanSiswa(Request $request)
    {
        $pembimbing = Auth::user();
        $status = $request->input('status', 'pending');

        $pengajuans = PengajuanSiswa::whereHas('siswa', function($q) use ($pembimbing) {
                $q->where('id_pembimbing', $pembimbing->id_pembimbing);
            })
            ->with('siswa')
            ->when($status, function($query, $status) {
                if ($status !== 'semua') {
                    return $query->where('status', $status);
                }
                return $query;
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pembimbing.pengajuanSiswa', compact('pembimbing', 'pengajuans', 'status'));
    }

    /**
     * Memproses approval/penolakan pengajuan
     */
    public function updatePengajuan(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        $pembimbing = Auth::user();
        $pengajuan = PengajuanSiswa::with('siswa')->findOrFail($id);

        if ($pengajuan->siswa->id_pembimbing !== $pembimbing->id_pembimbing) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if ($request->action === 'approve') {
            $pengajuan->status = 'valid';

            // Auto insert based on type
            if ($pengajuan->jenis === 'absensi') {
                $statusHadir = 'hadir'; // default if no jam masuk to check
                if ($pengajuan->jam_masuk && substr($pengajuan->jam_masuk, 0, 5) > '08:00') {
                    $statusHadir = 'terlambat';
                }

                \App\Models\Absensi::create([
                    'nisn' => $pengajuan->nisn,
                    'tanggal' => $pengajuan->tanggal,
                    'jam_masuk' => $pengajuan->jam_masuk,
                    'jam_pulang' => $pengajuan->jam_pulang,
                    'status' => $statusHadir,
                    'verifikasi' => 'verified',
                    'keterangan' => 'Validasi Lupa Absensi',
                ]);
            } else if ($pengajuan->jenis === 'kegiatan') {
                Logbook::create([
                    'nisn' => $pengajuan->nisn,
                    'tanggal' => $pengajuan->tanggal,
                    'kegiatan' => $pengajuan->deskripsi,
                    'status' => 'verified',
                    'catatan_pembimbing' => 'Validasi Lupa Kegiatan'
                ]);
            }

            $message = 'Pengajuan berhasil disetujui dan data otomatis ditambahkan ke sistem.';

        } else {
            $pengajuan->status = 'ditolak';
            $message = 'Pengajuan ditolak.';
        }

        $pengajuan->save();

        return redirect()->back()->with('success', $message);
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

        // 1. Fetch CURRENT custom criteria (strictly those assigned to this specific supervisor)
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteriaKustom = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        // 2. INITIALIZATION (Fall-back only):
        // Only trigger if the supervisor has ZERO criteria in the database.
        // This acts as a "Welcome" or "Reset" state.
        if ($kriteriaKustom->count() === 0) {
            $allowedTypes = ['sikap_kerja', 'kompetensi_keahlian'];
            $forbiddenKeywords = ['Teori', 'Praktek', 'Inisiatif', 'Kreativitas', 'Kesehatan dan Keselamatan Kerja'];
            
            $defaults = KriteriaPenilaian::whereNull('id_pembimbing')
                ->whereIn('tipe', $allowedTypes)
                ->get();

            foreach ($defaults as $d) {
                // Skip forbidden items (Teacher-specific or redundant)
                $isForbidden = false;
                foreach ($forbiddenKeywords as $word) {
                    if (mb_stripos($d->nama_kriteria, $word) !== false) {
                        $isForbidden = true;
                        break;
                    }
                }
                if ($isForbidden) continue;
                
                // Specific: Skip Disiplin if it's in the Kompetensi category (standardize to Sikap Kerja)
                if (mb_strtolower($d->nama_kriteria) == 'disiplin' && $d->tipe == 'kompetensi_keahlian') continue;

                // Create clean copy for this supervisor
                KriteriaPenilaian::create([
                    'nama_kriteria' => $d->nama_kriteria,
                    'tipe' => $d->tipe,
                    'jurusan' => $d->jurusan,
                    'urutan' => $d->urutan,
                    'id_pembimbing' => $supervisorId,
                ]);
            }
            
            // Re-fetch after the one-time operation
            $kriteriaKustom = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
                ->orderBy('tipe')
                ->orderBy('urutan')
                ->get();
        }

        // NOTE: We no longer perform "Auto-Cleanup" or "Auto-Restoration" on every load.
        // This gives the supervisor full control over their list (can delete all or rename).

        // 3. Set $kriteria for the assessment modal to use these custom ones
        $kriteria = $kriteriaKustom;
            
        return view('pembimbing.penilaianSiswa', compact(
            'pembimbing', 'siswasPending', 'siswasDone', 
            'kriteria', 'kriteriaKustom', 'search'
        ));
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

        /** @var \App\Models\Pembimbing $pembimbing */
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

        return redirect()->route('pembimbing.evaluasi')->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Menampilkan form input evaluasi (mode halaman penuh)
     */
    public function inputEvaluasi($nisn)
    {
        $pembimbing = Auth::user();

        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $penilaian = Penilaian::where('nisn', $nisn)
            ->where('pemberi_nilai', 'Dosen Pembimbing')
            ->with('penilaianDetails.kriteria')
            ->orderBy('created_at', 'desc')
            ->first();

        $kriteria = KriteriaPenilaian::where('id_pembimbing', (string) $pembimbing->id_pembimbing)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        return view('pembimbing.penilaianSiswa', compact('pembimbing', 'siswa', 'penilaian', 'kriteria'));
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
        /** @var \App\Models\Pembimbing $pembimbing */
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

    /**
     * Menampilkan halaman manajemen kriteria penilaian.
     */
    public function kriteriaPenilaian()
    {
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        
        // Ambil kriteria kustom milik pembimbing
        $kriteriaKustom = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        // Ambil kriteria default (sebagai referensi)
        $kriteriaDefault = KriteriaPenilaian::whereNull('id_pembimbing')
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        return view('pembimbing.kriteria', compact('pembimbing', 'kriteriaKustom', 'kriteriaDefault'));
    }

    /**
     * Menyimpan kriteria penilaian baru.
     */
    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;

        KriteriaPenilaian::create([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
            'id_pembimbing' => $supervisorId,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    /**
     * Memperbarui kriteria penilaian.
     */
    public function updateKriteria(Request $request, $id)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus kriteria penilaian.
     */
    public function destroyKriteria($id)
    {
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
