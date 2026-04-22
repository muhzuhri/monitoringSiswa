<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\LokasiAbsensi;
use App\Models\LaporanAkhir;
use App\Models\Logbook;
use App\Models\PengajuanSiswa;
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
     * Simpan Absensi Siswa dengan Validasi Lokasi.
     */
    public function storeAbsensi(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i');

        // Validasi input dasar
        $request->validate([
            'type' => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $lat = $request->latitude;
        $lng = $request->longitude;

        // Ambil semua lokasi absensi yang aktif
        $lokasis = LokasiAbsensi::where('is_active', true)->get();
        
        $isValidLocation = false;
        $jarak = 0;
        $namaLokasi = "Lokasi Magang";

        if ($lokasis->isEmpty()) {
            // Fallback jika belum ada data di database (Fasilkom Default)
            $targetLat = -2.9847200554793494;
            $targetLng = 104.73225951187132;
            $maxRadius = 500;
            $jarak = $this->calculateDistance($lat, $lng, $targetLat, $targetLng);
            $isValidLocation = ($jarak <= $maxRadius);
            $namaLokasi = "Fasilkom";
        } else {
            $jarakTerdekat = null;
            $lokasiTerdekat = null;

            foreach ($lokasis as $lok) {
                $d = $this->calculateDistance($lat, $lng, $lok->latitude, $lok->longitude);
                
                // Track lokasi terdekat untuk pesan error
                if ($jarakTerdekat === null || $d < $jarakTerdekat) {
                    $jarakTerdekat = $d;
                    $lokasiTerdekat = $lok;
                }

                // Cek apakah di dalam radius salah satu lokasi
                if ($d <= $lok->radius) {
                    $isValidLocation = true;
                    $jarak = $d;
                    $namaLokasi = $lok->nama_lokasi;
                    break;
                }
            }

            if (!$isValidLocation) {
                $jarak = $jarakTerdekat;
                $namaLokasi = $lokasiTerdekat->nama_lokasi;
            }
        }

        // Cek apakah sudah absen hari ini
        $existing = $user->absensis()->whereDate('tanggal', $today)->first();

        if ($request->type === 'masuk') {
            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen masuk hari ini.'], 400);
            }

            // Aturan Jam Masuk: 07:00 - 10:00 (Sesuai kode lama, tapi saya gunakan logic yang ada)
            // Di kode sebelumnya: if ($currentTime < '07:00' || $currentTime > '24:00') -> sepertinya typo di kode asli user (24:00?)
            // Saya akan tetap mengikuti logic asli namun memperbaiki jika itu krusial. 
            // User bilang "Batas: 07:00 - 10:00" di UI, tapi di controller "07:00 - 24:00". Saya biarkan sesuai controller asli agar tidak merubah rule bisnis tanpa izin.
            if ($currentTime < '07:00' || $currentTime > '24:00') {
                return response()->json(['success' => false, 'message' => 'Maaf, absen masuk hanya diperbolehkan pukul 07:00 - 10:00.'], 400);
            }

            // Tentukan Status Awal
            $pilihan = $request->status_pilihan ?? 'hadir';

            // Pengecekan Radius hanya untuk status 'hadir'
            if ($pilihan === 'hadir' && !$isValidLocation) {
                return response()->json([
                    'success' => false, 
                    'message' => "Anda berada di luar radius {$namaLokasi} (" . round($jarak) . "m). Silakan mendekat ke lokasi.",
                    'distance' => round($jarak)
                ], 403);
            }

            if ($pilihan === 'hadir') {
                $status = ($currentTime <= '08:00') ? 'hadir' : 'terlambat';
            } else {
                $status = $pilihan; // izin atau sakit
            }

            $path = $request->file('foto')->store('absensi/masuk', 'public');

            $user->absensis()->create([
                'tanggal' => $today,
                'jam_masuk' => $now->toTimeString(),
                'foto_masuk' => $path,
                'status' => $status,
                'latitude' => $lat,
                'longitude' => $lng,
                'jarak_meter' => $jarak,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Absen berhasil dicatat sebagai ' . ucfirst($status) . '!',
                'distance' => round($jarak)
            ]);

        } elseif ($request->type === 'pulang') {
            if (!$existing) {
                return response()->json(['success' => false, 'message' => 'Anda belum melakukan absen masuk hari ini.'], 400);
            }
            if ($existing->jam_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen pulang hari ini.'], 400);
            }

            // Aturan Jam Pulang: 10:00 - 24:00 (Sesuai controller asli)
            if ($currentTime < '10:00' || $currentTime > '24:00') {
                return response()->json(['success' => false, 'message' => 'Maaf, absen pulang belum diperbolehkan.'], 400);
            }

            // Pengecekan Radius untuk Pulang
            if (!$isValidLocation) {
                return response()->json([
                    'success' => false, 
                    'message' => "Anda berada di luar radius {$namaLokasi} (" . round($jarak) . "m). Silakan mendekat ke lokasi.",
                    'distance' => round($jarak)
                ], 403);
            }

            $path = $request->file('foto')->store('absensi/pulang', 'public');

            /** @var \App\Models\Absensi $existing */
            $existing->update([
                'jam_pulang' => $now->toTimeString(),
                'foto_pulang' => $path,
                'latitude' => $lat,
                'longitude' => $lng,
                'jarak_meter' => $jarak,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Absen pulang berhasil!',
                'distance' => round($jarak)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Tipe absensi tidak valid.'], 400);
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine Formula).
     * Hasil dalam satuan Meter.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
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
     * Menampilkan halaman Pengajuan Lupa Absensi / Kegiatan.
     */
    public function pengajuan()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        // Ambil riwayat pengajuan siswa
        $pengajuans = PengajuanSiswa::where('nisn', $user->nisn)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.pengajuan', compact('user', 'pengajuans'));
    }

    /**
     * Menyimpan Pengajuan Lupa Absensi / Kegiatan.
     */
    public function storePengajuan(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:absensi,kegiatan'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'deskripsi' => ['nullable', 'string'],
            'alasan_terlambat' => ['required', 'string'],
            'bukti' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:2048'],
        ]);

        // Validasi khusus jenis
        if ($validated['jenis'] === 'absensi' && empty($validated['jam_masuk']) && empty($validated['jam_pulang'])) {
            return back()->with('error', 'Untuk jenis absensi, minimal Jam Masuk atau Jam Pulang harus diisi.');
        }

        if ($validated['jenis'] === 'kegiatan' && empty($validated['deskripsi'])) {
            return back()->with('error', 'Untuk jenis kegiatan, Deskripsi Kegiatan wajib diisi.');
        }

        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti_pengajuan', 'public');
        }

        PengajuanSiswa::create([
            'nisn' => $user->nisn,
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'jam_masuk' => $validated['jam_masuk'] ?? null,
            'jam_pulang' => $validated['jam_pulang'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'alasan_terlambat' => $validated['alasan_terlambat'],
            'bukti' => $buktiPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pengajuan berhasil dikirim dan menunggu persetujuan pembimbing.');
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

        // Fetch logs (Oldest first)
        $logbooks = $user->logbooks()->orderBy('tanggal', 'asc')->get();
        $user->load('pembimbing');

        // Jurnal Mingguan version (Table: No, Tanggal, Pembimbing Lapangan, Kegiatan, Status)
        $fileName = "Jurnal_Mingguan_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.printJurnal', compact('user', 'logbooks'));
        
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

        // Fetch attendance
        $absensis = $user->absensis()->orderBy('tanggal', 'asc')->get();

        $fileName = "Rekap_Absensi_Individu_{$user->nisn}_" . date('d_M_Y') . ".pdf";

        $pdf = Pdf::loadView('siswa.rekapAbsensiIndividu', compact('user', 'absensis'));
        
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
            'user' => $siswa->guru // Pass guru if needed for guru template
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
        
        // Status 'selesai' otomatis di model Siswa jika tgl_selesai sudah lewat
        if ($user->status !== 'selesai') {
            return back()->with('info', 'Sertifikat akan tersedia setelah masa magang Anda berakhir.');
        }

        $user->load(['pembimbing', 'tahunAjaran']);
        
        $fileName = "Sertifikat_Magang_{$user->nisn}.pdf";

        $pdf = Pdf::loadView('siswa.sertifikat', compact('user'))
            ->setPaper('a4', 'landscape');
        
        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }



}


