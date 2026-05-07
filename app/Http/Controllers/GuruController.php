<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\InformasiDashboard;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\KriteriaPenilaian;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Menampilkan dashboard Guru Pembimbing.
     */
    public function dashboard()
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        
        // Data for the view (Informasi & Program Studi are currently used)
        $informasi = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan')->get();

        // Note: Statistics (absenToday, logbookCount, etc.) are removed as they are 
        // not displayed in current guru.blade.php view, improving performance.

        return view('guru.guru', compact('user', 'informasi', 'programStudis'));
    }

    /**
     * Helper to calculate student internship progress percentage
     */
    private function calculateProgress($siswa)
    {
        if (!$siswa->tgl_mulai_magang || !$siswa->tgl_selesai_magang) {
            return 0;
        }

        $start = Carbon::parse($siswa->tgl_mulai_magang);
        $end = Carbon::parse($siswa->tgl_selesai_magang);
        $now = Carbon::now();

        if ($now->lt($start)) return 0;
        if ($now->gt($end)) return 100;

        $totalDays = max(1, $start->diffInDays($end));
        $daysPassed = (int) $start->diffInDays($now);

        return min(100, round(($daysPassed / $totalDays) * 100));
    }

    /**
     * Menampilkan daftar semua siswa bimbingan.
     */
    public function daftarSiswa(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $today = Carbon::now()->toDateString();
        $npsn = $user->npsn; // Otomatis gunakan NPSN Guru

        // 1. Siswa Bimbingan (Aktif)
        $query = $user->siswas()->where(function($q) use ($today) {
            $q->where(function($sub) {
                $sub->where('status', '!=', 'selesai')->orWhereNull('status');
            })->where(function($sub) use ($today) {
                $sub->whereNull('tgl_selesai_magang')->orWhere('tgl_selesai_magang', '>=', $today);
            });
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        $siswas = $query->with(['guru', 'pembimbing', 'tahunAjaran', 'absensis' => fn($q) => $q->whereDate('tanggal', $today)])
            ->orderBy('nama', 'asc')->get();

        // Map and Group
        foreach ($siswas as $s) {
            $s->progress_percent = $this->calculateProgress($s);
            $s->absen_hari_ini = $s->absensis->first();
        }
        $groupedSiswas = $this->mapGroupedSiswa($siswas);

        // 2. Daftar Siswa Tersedia
        $availableQuery = Siswa::whereNull('id_guru');

        // Selalu filter berdasarkan NPSN sekolah yang sama dengan Guru
        if ($npsn) {
            $availableQuery->where('npsn', $npsn);
        }

        if ($search) {
            $availableQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        $availableSiswas = $availableQuery->orderBy('nama', 'asc')->get();
        $groupedAvailable = $this->mapGroupedSiswa($availableSiswas);

        // 3. Riwayat Siswa (Selesai)
        $riwayatQuery = $user->siswas()->with(['guru', 'pembimbing', 'tahunAjaran'])->where(function($q) use ($today) {
            $q->where('status', 'selesai')
              ->orWhere('tgl_selesai_magang', '<', $today);
        });
        if ($periodeId) $riwayatQuery->where('id_tahun_ajaran', $periodeId);
        $riwayatSiswas = $riwayatQuery->orderBy('tgl_selesai_magang', 'desc')->get();
        $groupedRiwayat = $this->mapGroupedSiswa($riwayatSiswas);

        $periodeOptions = \App\Models\TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_guru', $user->id_guru))
            ->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.daftarSiswa', compact(
            'user', 'siswas', 'search', 'npsn', 'periodeId', 'periodeOptions',
            'groupedSiswas', 'groupedAvailable', 'availableSiswas', 'riwayatSiswas', 'groupedRiwayat'
        ));
    }

    /**
     * Helper to group students by leader and prepare for view
     */
    private function mapGroupedSiswa($siswas)
    {
        return $siswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok',
            ];
        });
    }

    /**
     * Memilih siswa bimbingan (Claim).
     */
    public function claimSiswa($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = Siswa::where('nisn', $nisn)->whereNull('id_guru')->firstOrFail();

        if ($siswa->tipe_magang === 'kelompok') {
            // Klaim seluruh anggota kelompok
            Siswa::where('nisn_ketua', $siswa->nisn_ketua)
                ->whereNull('id_guru')
                ->update(['id_guru' => $user->id_guru]);
            $message = "Berhasil menambahkan kelompok {$siswa->nama} sebagai bimbingan Anda.";
        } else {
            // Klaim individu
            $siswa->update(['id_guru' => $user->id_guru]);
            $message = "Berhasil menambahkan {$siswa->nama} sebagai siswa bimbingan Anda.";
        }

        return back()->with('success', $message);
    }

    /**
     * Menampilkan logbook kegiatan siswa tertentu.
     */
    public function logbookSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $status = $request->input('status');
        $query = $siswa->logbooks();

        if ($status) {
            $query->where('status', $status);
        }

        $logbooks = $query->orderBy('tanggal', 'desc')->get();

        return view('guru.logbookSiswa', compact('user', 'siswa', 'logbooks', 'status'));
    }

    /**
     * Memproses verifikasi logbook.
     */
    public function verifikasiLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan' => 'nullable|string'
        ]);

        $logbook = Logbook::findOrFail($id);
        $siswa = Siswa::where('nisn', $logbook->nisn)->first();

        if (!$siswa || $siswa->id_guru != Auth::user()->id_guru) {
            return back()->with('error', 'Akses ditolak.');
        }

        $logbook->update([
            'status' => $request->status,
            'catatan_guru' => $request->catatan
        ]);

        return back()->with('success', 'Logbook berhasil divalidasi.');
    }

    /**
     * Menampilkan rekap absensi siswa tertentu.
     */
    public function absensiSiswa(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->with('absensis')->firstOrFail();

        $absensis = $siswa->absensis()->orderBy('tanggal', 'desc')->paginate(15);
        $rekap = [
            'total' => $siswa->absensis()->count(),
            'hadir' => $siswa->absensis()->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin'  => $siswa->absensis()->where('status', 'izin')->count(),
            'sakit' => $siswa->absensis()->where('status', 'sakit')->count(),
            'alpa'  => $siswa->absensis()->where('status', 'alpa')->count(),
        ];

        return view('guru.absensiSiswa', compact('user', 'siswa', 'absensis', 'rekap'));
    }

    /**
     * Daftar Laporan Akhir yang perlu diverifikasi.
     */
    public function verifikasiLaporan(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $siswaNisns = $user->siswas->pluck('nisn')->toArray();

        $query = LaporanAkhir::whereIn('nisn', $siswaNisns)->with('siswa');

        if ($search) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
        }

        $laporanPending = (clone $query)->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        
        $historyQuery = (clone $query)->where('status', '!=', 'pending');
        if ($periodeId) $historyQuery->whereHas('siswa', fn($q) => $q->where('id_tahun_ajaran', $periodeId));
        $historyLaporan = $historyQuery->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $periodeOptions = TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_guru', $user->id_guru))
            ->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.verifikasi', compact('user', 'laporanPending', 'historyLaporan', 'search', 'periodeId', 'periodeOptions'));
    }

    /**
     * Show detail verifikasi.
     */
    public function showVerifikasiLaporan($id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $laporan = LaporanAkhir::with('siswa')->findOrFail($id);

        if ($laporan->siswa->id_guru != $user->id_guru) {
            return redirect()->route('guru.verifikasi')->with('error', 'Akses ditolak.');
        }

        $history = $laporan->siswa->laporanAkhirs()->where('id_laporan', '!=', $id)->get();
        return view('guru.verifikasi', compact('user', 'laporan', 'history'));
    }

    /**
     * Update verifikasi laporan akhir.
     */
    public function updateVerifikasiLaporan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $laporan = LaporanAkhir::with('siswa')->findOrFail($id);
        if ($laporan->siswa->id_guru != Auth::user()->id_guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $laporan->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        if ($request->status == 'approved') {
            $laporan->siswa->update(['status' => 'selesai']);
        }

        return redirect()->route('guru.verifikasi')->with('success', 'Verifikasi laporan berhasil diperbarui.');
    }

    /**
     * Menampilkan daftar siswa untuk diberikan penilaian.
     */
    public function daftarPenilaian(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');

        // Base query for teacher's students
        $query = $user->siswas()->with('penilaians');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->get();

        // Separate into Pending and Done
        $siswasPending = $allSiswas->filter(function ($s) {
            return !$s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
        });

        $siswasDone = $allSiswas->filter(function ($s) use ($periodeId) {
            $hasNilai = $s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
            if (!$hasNilai) return false;
            if ($periodeId && (string)$s->id_tahun_ajaran !== (string)$periodeId) return false;
            return true;
        });

        $periodeOptions = \App\Models\TahunAjaran::whereHas('siswas', function ($q) use ($user) {
            $q->where('id_guru', $user->id_guru);
        })->orderBy('tgl_mulai', 'desc')->get();

        // 1. Fetch CURRENT custom criteria (strictly those assigned to this specific guru)
        $kriteriaKustom = \App\Models\KriteriaPenilaian::where('id_guru', $user->id_guru)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        // 2. INITIALIZATION (Fall-back only):
        if ($kriteriaKustom->count() === 0) {
            $defaults = \App\Models\KriteriaPenilaian::whereNull('id_guru')
                ->whereNull('id_pembimbing')
                ->whereIn('tipe', ['guru_kepribadian', 'guru_kemampuan'])
                ->get();

            foreach ($defaults as $d) {
                \App\Models\KriteriaPenilaian::create([
                    'nama_kriteria' => $d->nama_kriteria,
                    'tipe' => $d->tipe,
                    'jurusan' => $d->jurusan,
                    'urutan' => $d->urutan,
                    'id_guru' => $user->id_guru,
                ]);
            }
            
            // Re-fetch after the one-time operation
            $kriteriaKustom = \App\Models\KriteriaPenilaian::where('id_guru', $user->id_guru)
                ->orderBy('tipe')
                ->orderBy('urutan')
                ->get();
        }

        $kriteria = $kriteriaKustom;

        return view('guru.penilaian', compact('user', 'siswasPending', 'siswasDone', 'search', 'periodeId', 'periodeOptions', 'kriteria', 'kriteriaKustom'));
    }

    /**
     * Menampilkan form input penilaian untuk siswa tertentu.
     */
    public function inputPenilaian($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
        $penilaian = $siswa->penilaians()
            ->where('pemberi_nilai', 'Guru Pembimbing')
            ->with('penilaianDetails.kriteria')
            ->first();

        // Ambil kriteria khusus guru
        $kriteria = \App\Models\KriteriaPenilaian::whereIn('tipe', ['guru_kepribadian', 'guru_kemampuan'])
            ->where('id_guru', $user->id_guru)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        return view('guru.penilaian', compact('user', 'siswa', 'penilaian', 'kriteria'));
    }

    /**
     * Menyimpan hasil penilaian siswa.
     */
    public function storePenilaian(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $request->validate([
            'kategori' => 'required|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
            'saran' => 'nullable|string',
        ]);

        // Hitung rata-rata otomatis
        $allScores = collect($request->scores);
        $avg = $allScores->avg();

        // Create or update Penilaian head
        $penilaian = \App\Models\Penilaian::updateOrCreate(
            ['nisn' => $nisn, 'pemberi_nilai' => 'Guru Pembimbing'],
            [
                'rata_rata' => $avg,
                'kategori' => $request->kategori,
                'saran' => $request->saran,
            ]
        );

        // Update details
        \App\Models\PenilaianDetail::where('id_penilaian', $penilaian->id_penilaian)->delete();
        foreach ($request->scores as $kriteriaId => $score) {
            \App\Models\PenilaianDetail::create([
                'id_penilaian' => $penilaian->id_penilaian,
                'id_kriteria' => $kriteriaId,
                'skor' => $score
            ]);
        }

        return redirect()->route('guru.penilaian')->with('success', 'Penilaian untuk ' . $siswa->nama . ' berhasil disimpan.');
    }

    /**
     * Mengekspor hasil penilaian ke PDF.
     */
    public function exportPenilaian(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
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

        $pdf = Pdf::loadView('guru.printPenilaian', compact('user', 'siswa', 'penilaian'));

        return $pdf->stream("Penilaian_Siswa_{$siswa->nisn}_{$siswa->nama}.pdf");
    }

    /**
     * Menampilkan halaman profil guru.
     */
    public function profil()
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        return view('guru.profil', compact('user'));
    }

    /**
     * Memperbarui profil guru.
     */
    public function updateProfil(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:guru,email,' . $user->id_guru . ',id_guru',
            'no_hp' => 'nullable|string|max:15',
            'jabatan' => 'nullable|string|max:100',
            'sekolah' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:10',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jabatan' => $request->jabatan,
            'sekolah' => $request->sekolah,
            'npsn' => $request->npsn,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
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
            'logbooks' => $logbooks
        ]);

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
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

        $fileName = "Rekap_Absensi_Individu_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', [
            'user' => $siswa,
            'absensis' => $absensis
        ]);

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Download Rekap Absensi (Kelompok) Siswa.
     */
    public function downloadRekapAbsensiKelompok(Request $request, $nisnKetua)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        
        // Find leader and ensure they belong to this teacher
        $leader = $user->siswas()->where('nisn', $nisnKetua)->firstOrFail();

        // Find the earliest and latest attendance record date for the group
        $groupNisns = function($query) use ($nisnKetua) {
            $query->select('nisn')->from('siswa')
                  ->where('nisn_ketua', $nisnKetua)
                  ->orWhere('nisn', $nisnKetua);
        };

        $firstAbsen = \App\Models\Absensi::whereIn('nisn', $groupNisns)->min('tanggal');
        $latestAbsen = \App\Models\Absensi::whereIn('nisn', $groupNisns)->max('tanggal');

        $startRange = $firstAbsen ? \Carbon\Carbon::parse($firstAbsen)->startOfMonth() : \Carbon\Carbon::now()->startOfMonth();
        $endRange = $latestAbsen ? \Carbon\Carbon::parse($latestAbsen)->endOfMonth() : \Carbon\Carbon::now()->endOfMonth();
        
        // Calculate months between start of first attendance and end of latest attendance
        $months = [];
        $current = $startRange->copy();
        
        while ($current <= $endRange) {
            $month = $current->month;
            $year = $current->year;
            
            // Fetch all group members
            $anggota = \App\Models\Siswa::where(function($q) use ($nisnKetua) {
                $q->where('nisn_ketua', $nisnKetua)->orWhere('nisn', $nisnKetua);
            })->with(['absensis' => function($q) use ($month, $year) {
                $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            }])->get();

            $months[] = [
                'month' => $month,
                'year' => $year,
                'anggota' => $anggota,
                'daysInMonth' => $current->daysInMonth,
                'monthName' => $current->translatedFormat('F')
            ];
            
            $current->addMonth();
        }

        $fileName = "Rekap_Absensi_Kelompok_{$leader->nisn}_" . date('d_M_Y') . ".pdf";
        $pdf = Pdf::loadView('siswa.rekapAbsensiKelompok', [
            'user' => $leader,
            'months' => $months
        ]);
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }
        return $pdf->stream($fileName);
    }

    /**
     * Menyimpan kriteria penilaian baru.
     */
    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:guru_kepribadian,guru_kemampuan',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Guru $user */
        $user = Auth::user();

        \App\Models\KriteriaPenilaian::create([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
            'id_guru' => $user->id_guru,
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
            'tipe' => 'required|in:guru_kepribadian,guru_kemampuan',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $kriteria = \App\Models\KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_guru', $user->id_guru)
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
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $kriteria = \App\Models\KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_guru', $user->id_guru)
            ->firstOrFail();

        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }

    /**
     * Cetak Penilaian dari Pembimbing Lapangan
     */
    public function cetakPenilaianPembimbing(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)
            ->with(['penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing');
            }, 'penilaians.penilaianDetails.kriteria', 'absensis', 'logbooks', 'tahunAjaran'])
            ->firstOrFail();

        $pembimbing = $siswa->pembimbing;
        if (!$pembimbing) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Siswa tidak memiliki pembimbing lapangan.'], 404);
            }
            return back()->with('warning', 'Siswa tidak memiliki pembimbing lapangan.');
        }

        $penilaian = $siswa->penilaians->first();
        if (!$penilaian) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Penilaian belum di inputkan oleh Pembimbing Lapangan.'], 404);
            }
            return back()->with('warning', 'Penilaian belum di inputkan oleh Pembimbing.');
        }

        $fileName = "Laporan_Siswa_{$siswa->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('pembimbing.cetakPenilaian', compact('pembimbing', 'siswa'));

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
        
        if ($siswa->status !== 'selesai' && !($siswa->tgl_selesai_magang && \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->lt(\Carbon\Carbon::now()))) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.'], 404);
            }
            return back()->with('info', 'Sertifikat tidak tersedia. Siswa belum menyelesaikan magang.');
        }

        $siswa->load(['pembimbing', 'tahunAjaran']);
        
        $fileName = "Sertifikat_Magang_{$siswa->nisn}.pdf";

        // View sertifikat from siswa module is reused.
        $pdf = Pdf::loadView('siswa.sertifikat', ['user' => $siswa])
            ->setPaper('a4', 'landscape');
        
        return $pdf->stream($fileName);
    }

    public function cetakLaporanAkhir(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
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
}
