<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\InformasiDashboard;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\Penilaian;
use App\Models\ProgramStudi;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Menampilkan dashboard Guru Pembimbing.
     */
    public function dashboard()
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // 1. Daftar Siswa Bimbingan
        $siswas = $user->siswas;
        $totalSiswa = $siswas->count();

        // 2. Monitoring Hari Ini
        $siswaNisns = $siswas->pluck('nisn')->toArray();

        $absenTodayCount = Absensi::where('nisn', $siswaNisns)
            ->whereDate('tanggal', $today)
            ->count();
        $belumAbsen = max(0, $totalSiswa - $absenTodayCount);

        $logbookTodayCount = Logbook::whereIn('nisn', $siswaNisns)
            ->whereDate('tanggal', $today)
            ->count();
        $belumLogbook = max(0, $totalSiswa - $logbookTodayCount);

        // 3. Rata-rata Progres PKL
        $totalProgress = 0;
        foreach ($siswas as $siswa) {
            if ($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang) {
                $start = Carbon::parse($siswa->tgl_mulai_magang);
                $end = Carbon::parse($siswa->tgl_selesai_magang);
                $totalDays = max(1, $start->diffInDays($end));
                $daysPassed = $start->isFuture() ? 0 : (int) $start->diffInDays(Carbon::now());
                $p = min(100, round(($daysPassed / $totalDays) * 100));
                $totalProgress += $p;
            }
        }
        $rataProgress = $totalSiswa > 0 ? round($totalProgress / $totalSiswa) : 0;

        // 4. Notifikasi / Pending Verifikasi
        $pendingLogbookCount = Logbook::whereIn('nisn', $siswaNisns)
            ->where('status', 'pending')
            ->count();

        $pendingLaporanCount = LaporanAkhir::whereIn('nisn', $siswaNisns)
            ->where('status', 'pending')
            ->count();

        // 5. Data Chart: Presensi Bulan Ini
        $statsPresensi = [
            'hadir' => Absensi::whereIn('nisn', $siswaNisns)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => Absensi::whereIn('nisn', $siswaNisns)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'izin')->count(),
            'sakit' => Absensi::whereIn('nisn', $siswaNisns)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'sakit')->count(),
            'alpa' => Absensi::whereIn('nisn', $siswaNisns)->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'alpa')->count(),
        ];

        // 6. Data Chart: Rata-rata Nilai
        $avgNilaiTeknis = Penilaian::whereIn('nisn', $siswaNisns)->avg('nilai_teknis') ?: 0;
        $avgNilaiNonTeknis = Penilaian::whereIn('nisn', $siswaNisns)->avg('nilai_non_teknis') ?: 0;

        // Preview Siswa (Limit 5)
        $siswaPreviews = $user->siswas()->with([
            'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            }
        ])->limit(5)->get();

        // Informasi Dashboard
        $informasi     = InformasiDashboard::getInstance();
        $programStudis = ProgramStudi::where('aktif', true)->orderBy('urutan')->get();

        return view('guru.guru', compact(
            'user',
            'totalSiswa',
            'belumAbsen',
            'belumLogbook',
            'rataProgress',
            'pendingLogbookCount',
            'pendingLaporanCount',
            'statsPresensi',
            'avgNilaiTeknis',
            'avgNilaiNonTeknis',
            'siswaPreviews',
            'informasi',
            'programStudis'
        ));
    }

    /**
     * Menampilkan daftar semua siswa bimbingan.
     */
    public function daftarSiswa(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $npsn = $request->input('npsn');
        $periodeId = $request->input('periode');
        $today = Carbon::now()->toDateString();

        // 1. Siswa Bimbingan (yang sudah dikoordinir guru ini)
        $query = $user->siswas();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('perusahaan', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        // Tampilkan hanya siswa yang aktif (belum selesai)
        $query->where(function($q) {
            $q->where(function($sq) {
                $sq->where('status', '!=', 'selesai')
                   ->orWhereNull('status');
            })->where(function($subQ) {
                $subQ->where('tgl_selesai_magang', '>=', now())
                     ->orWhereNull('tgl_selesai_magang');
            });
        });

        $siswas = $query->with([
            'absensis' => function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            }
        ])->orderBy('nama', 'asc')->get();

        // Grouping logic for Siswa Bimbingan
        $groupedSiswas = $siswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok',
                'type' => ($group->count() > 1 || $leader->tipe_magang === 'kelompok') ? 'Kelompok' : 'Individu'
            ];
        });

        // 2. Daftar Siswa Tersedia (yang belum punya guru pembimbing)
        $availableQuery = Siswa::whereNull('id_guru');

        if ($npsn) {
            $availableQuery->where('npsn', $npsn);
        }

        if ($search) {
            $availableQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('sekolah', 'like', "%{$search}%");
            });
        }

        $availableSiswas = $availableQuery->orderBy('nama', 'asc')->get();
        $groupedAvailable = $availableSiswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok',
                'type' => ($group->count() > 1 || $leader->tipe_magang === 'kelompok') ? 'Kelompok' : 'Individu'
            ];
        });

        // Hitung progress untuk setiap siswa bimbingan
        foreach ($siswas as $siswa) {
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

        // 3. Riwayat Siswa Binaan (yang statusnya sudah selesai)
        $riwayatQuery = $user->siswas()
            ->where(function($q) {
                $q->where('status', 'selesai')
                  ->orWhere('tgl_selesai_magang', '<', now());
            });

        if ($periodeId) {
            $riwayatQuery->where('id_tahun_ajaran', $periodeId);
        }

        $riwayatSiswas = $riwayatQuery->orderBy('tgl_selesai_magang', 'desc')->get();

        $groupedRiwayat = $riwayatSiswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok',
                'type' => ($group->count() > 1 || $leader->tipe_magang === 'kelompok') ? 'Kelompok' : 'Individu'
            ];
        });

        $periodeOptions = \App\Models\TahunAjaran::whereHas('siswas', function ($q) use ($user) {
            $q->where('id_guru', $user->id_guru);
        })->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.daftarSiswa', compact(
            'user', 
            'siswas', 
            'search', 
            'npsn', 
            'periodeId',
            'periodeOptions',
            'groupedSiswas', 
            'groupedAvailable', 
            'availableSiswas', 
            'riwayatSiswas',
            'groupedRiwayat'
        ));
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
     * Menampilkan logbook/kegiatan siswa tertentu.
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
     * Verifikasi logbook siswa (Approve/Reject).
     */
    public function verifikasiLogbook(Request $request, $id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();

        $validated = $request->validate([
            'status' => ['required', 'in:verified,rejected'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $logbook = \App\Models\Logbook::findOrFail($id);

        // Pastikan logbook ini milik siswa bimbingan guru ini
        if ($logbook->siswa->id_guru != $user->id_guru) {
            return back()->with('error', 'Anda tidak memiliki akses untuk memverifikasi logbook ini.');
        }

        $logbook->update([
            'status' => $validated['status'],
            'catatan_pembimbing' => $validated['catatan'],
        ]);

        $statusLabel = $validated['status'] == 'verified' ? 'disetujui' : 'ditolak';
        return back()->with('success', "Logbook berhasil {$statusLabel}.");
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
            'izin' => $siswa->absensis()->where('status', 'izin')->count(),
            'sakit' => $siswa->absensis()->where('status', 'sakit')->count(),
            'alpa' => $siswa->absensis()->where('status', 'alpa')->count(),
        ];

        return view('guru.absensiSiswa', compact('user', 'siswa', 'absensis', 'rekap'));
    }

    /**
     * Export rekap absensi ke PDF.
     */
    public function exportAbsensiSiswa($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $absensis = $siswa->absensis()->orderBy('tanggal', 'asc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('siswa.printJurnal', [
            'user' => $siswa,
            'logbooks' => $siswa->logbooks()->orderBy('tanggal', 'asc')->get()->map(function ($log) use ($siswa) {
                $log->absen = $siswa->absensis()->whereDate('tanggal', $log->tanggal)->first();
                return $log;
            })
        ]);

        return $pdf->download("Rekap_Monitoring_{$siswa->nama}.pdf");
    }

    /**
     * Menampilkan daftar laporan akhir yang perlu diverifikasi.
     */
    public function verifikasiLaporan(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $siswaNisns = $user->siswas->pluck('nisn')->toArray();

        // 1. Ambil laporan terbaru untuk setiap siswa yang berstatus pending
        $queryPending = \App\Models\LaporanAkhir::whereIn('nisn', $siswaNisns)
            ->where('status', 'pending')
            ->with('siswa');

        if ($search) {
            $queryPending->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $laporanPending = $queryPending->orderBy('created_at', 'desc')->get();

        // 2. Ambil semua riwayat laporan
        $queryHistory = \App\Models\LaporanAkhir::whereIn('nisn', $siswaNisns)
            ->where('status', '!=', 'pending')
            ->with('siswa');

        if ($search) {
            $queryHistory->whereHas('siswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        if ($periodeId) {
            $queryHistory->whereHas('siswa', function ($q) use ($periodeId) {
                $q->where('id_tahun_ajaran', $periodeId);
            });
        }

        $historyLaporan = $queryHistory->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $periodeOptions = \App\Models\TahunAjaran::whereHas('siswas', function ($q) use ($user) {
            $q->where('id_guru', $user->id_guru);
        })->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.verifikasi', compact('user', 'laporanPending', 'historyLaporan', 'search', 'periodeId', 'periodeOptions'));
    }

    /**
     * Menampilkan detail verifikasi laporan akhir.
     */
    public function showVerifikasiLaporan($id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $laporan = \App\Models\LaporanAkhir::with('siswa')->findOrFail($id);

        // Pastikan siswa ini bimbingan guru ini
        if ($laporan->siswa->id_guru != $user->id_guru) {
            return redirect()->route('guru.verifikasi')->with('error', 'Akses ditolak.');
        }

        // Ambil riwayat versi laporan siswa ini
        $history = $laporan->siswa->laporanAkhirs()->where('id_laporan', '!=', $id)->get();

        return view('guru.verifikasi', compact('user', 'laporan', 'history')); // We can merge views if small or use different if complex. User provided verifikasi.blade.php.
    }

    /**
     * Update status verifikasi laporan akhir.
     */
    public function updateVerifikasiLaporan(Request $request, $id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $laporan = \App\Models\LaporanAkhir::with('siswa')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string',
            'keterangan_revisi' => 'nullable|string',
        ]);

        $laporan->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Jika disetujui, update status siswa ke "Selesai"
        if ($validated['status'] == 'approved') {
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
    public function exportPenilaian($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
        $penilaian = $siswa->penilaians()
            ->where('pemberi_nilai', 'Guru Pembimbing')
            ->with(['penilaianDetails.kriteria'])
            ->firstOrFail();

        $pdf = Pdf::loadView('guru.printPenilaian', compact('user', 'siswa', 'penilaian'));

        return $pdf->download("Penilaian_Siswa_{$siswa->nisn}_{$siswa->nama}.pdf");
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

        // Start Magang from Tahun Ajaran
        $startMagang = $leader->tahunAjaran ? \Carbon\Carbon::parse($leader->tahunAjaran->tgl_mulai) : \Carbon\Carbon::now()->startOfMonth();
        
        // Find range end based on latest activity in the group
        $latestAbsen = \App\Models\Absensi::whereIn('nisn', function($query) use ($nisnKetua) {
            $query->select('nisn')->from('siswa')
                  ->where('nisn_ketua', $nisnKetua)
                  ->orWhere('nisn', $nisnKetua);
        })->max('tanggal');

        $endRange = $latestAbsen ? \Carbon\Carbon::parse($latestAbsen) : \Carbon\Carbon::now();
        if ($endRange->isPast() && $leader->tgl_selesai_magang) {
             $endRange = \Carbon\Carbon::parse($leader->tgl_selesai_magang);
        } else if ($endRange->isPast()) {
            $endRange = \Carbon\Carbon::now();
        }
        
        $months = [];
        $current = $startMagang->copy()->startOfMonth();
        
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
}
