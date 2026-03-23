<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SiswaController extends Controller
{
    /**
     * Menampilkan dashboard siswa dengan statistik dinamis.
     */
    public function dashboard()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Hitung Progres Magang
        $progress = 0;
        $hariDijalani = 0;

        if ($user->tgl_mulai_magang && $user->tgl_selesai_magang) {
            $start = Carbon::parse($user->tgl_mulai_magang);
            $end = Carbon::parse($user->tgl_selesai_magang);
            $today = Carbon::now();

            $totalDays = max(1, $start->diffInDays($end));
            $daysPassed = $start->isFuture() ? 0 : (int) $start->diffInDays($today);

            $progress = round(($daysPassed / $totalDays) * 100);
            $progress = min(100, max(0, $progress));
            $hariDijalani = $daysPassed;
        }

        // Statistik
        $logbookTerisi = $user->logbooks()->count();
        $logbookVerified = $user->logbooks()->where('status', 'verified')->count();

        // Count total present days
        $totalHadir = $user->absensis()->whereIn('status', ['hadir', 'terlambat'])->count();

        $todayString = Carbon::now()->toDateString();
        $absensiHariIni = $user->absensis()->whereDate('tanggal', $todayString)->first();
        $logbookHariIni = $user->logbooks()->whereDate('tanggal', $todayString)->first();

        return view('siswa.siswa', compact(
            'user',
            'progress',
            'hariDijalani',
            'logbookTerisi',
            'logbookVerified',
            'absensiHariIni',
            'logbookHariIni',
            'totalHadir'
        ));
    }

    /**
     * Menampilkan halaman Absensi dan Kegiatan (Logbook).
     */
    public function absensiKegiatan()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        // LIMIT: 7 days for attendance on main page
        $absensis = $user->absensis()->orderBy('tanggal', 'desc')->limit(7)->get();
        
        // LIMIT: 3 entries for logbooks on main page
        $logbooks = $user->logbooks()->orderBy('tanggal', 'desc')->limit(3)->get();

        $today = Carbon::now()->toDateString();
        $absensiHariIni = $user->absensis()->whereDate('tanggal', $today)->first();

        return view('siswa.absensiKegiatan', compact('user', 'absensis', 'logbooks', 'absensiHariIni'));
    }

    /**
     * Ambil detail riwayat absensi via AJAX (Paginated).
     */
    public function getAbsensiDetail(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        $perPage = 10;
        $page = $request->query('page', 1);

        $data = $user->absensis()
            ->orderBy('tanggal', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('l, d F Y'),
                    'jam_masuk' => $item->jam_masuk ? Carbon::parse($item->jam_masuk)->format('H:i') : '-',
                    'jam_pulang' => $item->jam_pulang ? Carbon::parse($item->jam_pulang)->format('H:i') : '-',
                    'status' => ucfirst($item->status),
                    'foto_masuk' => $item->foto_masuk ? asset('storage/' . $item->foto_masuk) : null,
                    'foto_pulang' => $item->foto_pulang ? asset('storage/' . $item->foto_pulang) : null,
                    'verifikasi' => $item->verifikasi,
                ];
            });

        return response()->json([
            'data' => $data,
            'current_page' => (int)$page,
            'has_more' => $user->absensis()->count() > ($page * $perPage)
        ]);
    }

    /**
     * Ambil detail riwayat logbook via AJAX (Paginated).
     */
    public function getLogbookDetail(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $perPage = 5; // Logbook typically takes more space
        $page = $request->query('page', 1);

        $data = $user->logbooks()
            ->orderBy('tanggal', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function($item) {
                return [
                    'tanggal' => Carbon::parse($item->tanggal)->translatedFormat('l, d F Y'),
                    'kegiatan' => $item->kegiatan,
                    'status' => $item->status,
                    'catatan' => $item->catatan_pembimbing
                ];
            });

        return response()->json([
            'data' => $data,
            'current_page' => (int)$page,
            'has_more' => $user->logbooks()->count() > ($page * $perPage)
        ]);
    }

    /**
     * Simpan Absensi Siswa.
     */
    public function storeAbsensi(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Cek apakah sudah absen hari ini
        $existing = $user->absensis()->whereDate('tanggal', $today)->first();

        if ($request->type === 'masuk') {
            if ($existing) {
                return back()->with('error', 'Anda sudah melakukan absen masuk hari ini.');
            }

            // Aturan Jam Masuk: 07:00 - 10:00
            if ($currentTime < '07:00' || $currentTime > '24:00') {
                return back()->with('error', 'Maaf, absen masuk hanya diperbolehkan pukul 07:00 - 10:00.');
            }

            // Tentukan Status Awal
            $pilihan = $request->status_pilihan ?? 'hadir';

            if ($pilihan === 'hadir') {
                $status = ($currentTime <= '08:00') ? 'hadir' : 'terlambat';
            } else {
                $status = $pilihan; // izin atau sakit
            }

            // Validasi Foto (Wajib untuk Hadir/Terlambat/Izin/Sakit)
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $path = $request->file('foto')->store('absensi/masuk', 'public');

            $user->absensis()->create([
                'tanggal' => $today,
                'jam_masuk' => $now->toTimeString(),
                'foto_masuk' => $path,
                'status' => $status,
            ]);

            $msg = 'Absen berhasil dicatat sebagai ' . ucfirst($status) . '!';
            return back()->with('success', $msg);

        } elseif ($request->type === 'pulang') {
            if (!$existing) {
                return back()->with('error', 'Anda belum melakukan absen masuk hari ini.');
            }
            if ($existing->jam_pulang) {
                return back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
            }

            // Aturan Jam Pulang: 15:00 - 16:30
            if ($currentTime < '10:00' || $currentTime > '24:00') {
                return back()->with('error', 'Maaf, absen pulang hanya diperbolehkan pukul 15:00 - 16:30.');
            }

            // Validasi Foto
            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $path = $request->file('foto')->store('absensi/pulang', 'public');

            $existing->update([
                'jam_pulang' => $now->toTimeString(),
                'foto_pulang' => $path,
            ]);

            return back()->with('success', 'Absen pulang berhasil!');
        }

        return back()->with('error', 'Tipe absensi tidak valid.');
    }

    /**
     * Simpan Logbook Siswa.
     */
    public function storeLogbook(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('logbooks', 'public');
        }

        $user->logbooks()->create([
            'tanggal' => $validated['tanggal'],
            'kegiatan' => $validated['kegiatan'],
            'foto' => $fotoPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Logbook berhasil disimpan.');
    }

    /**
     * Menampilkan halaman profil siswa.
     */
    public function showProfile()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        $user->load(['guru', 'pembimbing', 'tahunAjaran']);
        
        return view('siswa.profil', compact('user'));
    }

    /**
     * Update data profil siswa.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:siswa,email,' . $user->nisn . ',nisn'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas' => ['required', 'string', 'max:50'],
            'jurusan' => ['required', 'string', 'max:100'],
            'sekolah' => ['required', 'string', 'max:150'],
            'perusahaan' => ['required', 'string', 'max:150'],
            'foto_profil' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profile_photos', 'public');
            $validated['foto_profil'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password siswa.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user->update([
            'password' => $validated['new_password'], // Hash cast handles this
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Menampilkan halaman Laporan Siswa.
     */
    public function laporan()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();


        // Laporan Akhir
        $laporanAkhir = $user->laporanAkhir;

        // Rekap Absensi (Bulanan - Current Month)
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $rekapAbsensi = [
            'hadir' => $user->absensis()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'izin' => $user->absensis()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'izin')->count(),
            'sakit' => $user->absensis()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'sakit')->count(),
            'alpa' => $user->absensis()->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status', 'alpa')->count(),
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

        $path = $request->file('file_laporan')->store('laporan_akhir', 'public');

        $user->laporanAkhirs()->create([
            'nisn' => $user->nisn,
            'file' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan akhir berhasil diunggah.');
    }

    /**
     * Download Jurnal Kegiatan Mingguan.
     */
    public function downloadJurnalMingguan(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Fetch logs (Oldest first)
        $logbooks = $user->logbooks()->orderBy('tanggal', 'asc')->get();
        $user->load('pembimbing');

        // Jurnal Mingguan version (Table: No, Tanggal, Pembimbing Lapangan, Kegiatan, Status)
        $fileName = "Jurnal_Mingguan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', compact('user', 'logbooks'));

        return $pdf->download($fileName);
    }

    /**
     * Download Rekap Absensi (Individu).
     */
    public function downloadRekapAbsensiIndividu(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Fetch attendance
        $absensis = $user->absensis()->orderBy('tanggal', 'asc')->get();

        $fileName = "Rekap_Absensi_Individu_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', compact('user', 'absensis'));

        return $pdf->download($fileName);
    }

    /**
     * Download Rekap Absensi (Berkelompok/Bulanan).
     */
    public function downloadRekapAbsensiKelompok(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $startMagang = $user->tahunAjaran ? Carbon::parse($user->tahunAjaran->tgl_mulai) : Carbon::now()->startOfMonth();
        
        // Find the latest attendance record date in the group to determine the end range
        $latestAbsen = Absensi::whereIn('nisn', function($query) use ($user) {
            $query->select('nisn')->from('siswa')
                  ->where('nisn_ketua', $user->nisn_ketua ?: $user->nisn);
        })->max('tanggal');

        $endRange = $latestAbsen ? Carbon::parse($latestAbsen) : Carbon::now();
        
        // Ensure the range at least ends at now
        if ($endRange->isPast()) {
            $endRange = Carbon::now();
        }
        
        // Calculate months between start and now
        $months = [];
        $current = $startMagang->copy()->startOfMonth();
        
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

        $pdf = Pdf::loadView('siswa.rekapAbsensiKelompok', compact('user', 'months'));

        return $pdf->download($fileName);
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
            'user' => $siswa->guru // Pass guru if needed for guru template
        ]);

        return $pdf->download($fileName);
    }

}
