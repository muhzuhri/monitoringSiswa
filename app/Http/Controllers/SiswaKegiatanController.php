<?php

namespace App\Http\Controllers;

use App\Models\LokasiAbsensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaKegiatanController extends SiswaBaseController
{
    /**
     * Menampilkan halaman Absensi dan Kegiatan (Logbook).
     */
    public function absensiKegiatan()
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        // Fetch merged attendance data (including dynamic Alphas)
        // We limit to 5 records for the preview
        $absensis = $this->getFullAttendanceData($user)
            ->sortByDesc('tanggal')
            ->take(5);
        
        // LIMIT: 5 entries for logbooks on main page
        $logbooks = $this->getFullLogbookData($user)
            ->sortByDesc('tanggal')
            ->take(5);

        $today = Carbon::now()->toDateString();
        $absensiHariIni = $user->absensis()->whereDate('tanggal', $today)->first();
        $logbookHariIni = $user->logbooks()->whereDate('tanggal', $today)->first();
        $isFinished = $this->isMagangSelesai($user);

        return view('siswa.absensiKegiatan', compact('user', 'absensis', 'logbooks', 'absensiHariIni', 'logbookHariIni', 'isFinished'));
    }

    /**
     * Ambil detail riwayat absensi via AJAX (Paginated).
     */
    public function getAbsensiDetail(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();
        
        $perPage = 5;
        $page = $request->query('page', 1);

        $allData = $this->getFullAttendanceData($user)
            ->sortByDesc('tanggal')
            ->values();

        $data = $allData->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->map(function($item) {
                return [
                    'day' => Carbon::parse($item->tanggal)->translatedFormat('l'),
                    'date' => Carbon::parse($item->tanggal)->translatedFormat('d M Y'),
                    'jam_masuk' => $item->jam_masuk ? Carbon::parse($item->jam_masuk)->format('H:i') : '-',
                    'jam_pulang' => $item->jam_pulang ? Carbon::parse($item->jam_pulang)->format('H:i') : '-',
                    'status' => ucfirst($item->status ?? 'hadir'),
                    'foto_masuk' => ($item->foto_masuk ?? null) ? $item->foto_masuk : null,
                    'foto_pulang' => ($item->foto_pulang ?? null) ? $item->foto_pulang : null,
                    'verifikasi' => $item->verifikasi ?? 'verified',
                ];
            })->values();

        return response()->json([
            'data' => $data,
            'current_page' => (int)$page,
            'has_more' => $allData->count() > ($page * $perPage)
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

        $allData = $this->getFullLogbookData($user)
            ->sortByDesc('tanggal')
            ->values();

        $data = $allData->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->map(function($item) {
                return [
                    'day' => Carbon::parse($item->tanggal)->translatedFormat('l'),
                    'date' => Carbon::parse($item->tanggal)->translatedFormat('d M Y'),
                    'jam' => $item->created_at ? Carbon::parse($item->created_at)->format('H:i') : '-',
                    'kegiatan' => $item->kegiatan,
                    'status' => $item->status ?? 'pending',
                    'catatan' => $item->catatan_pembimbing ?? '-'
                ];
            })->values();

        return response()->json([
            'data' => $data,
            'current_page' => (int)$page,
            'has_more' => $allData->count() > ($page * $perPage)
        ]);
    }

    /**
     * Simpan Absensi Siswa dengan Validasi Lokasi.
     */
    public function storeAbsensi(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        if ($this->isMagangSelesai($user)) {
            return response()->json(['success' => false, 'message' => 'Masa magang Anda telah berakhir. Anda tidak dapat melakukan absensi lagi.'], 403);
        }

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

            // Aturan Jam Masuk: 07:00 - 10:00 (Sesuai dengan ketentuan umum)
            if ($currentTime < '00:01' || $currentTime > '23:59') {
                return response()->json(['success' => false, 'message' => 'Maaf, absen masuk hanya diperbolehkan mulai pukul 07:00.'], 400);
            }

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

            // Aturan Jam Pulang: 10:00 - 23:59
            if ($currentTime < '00:00' || $currentTime > '23:59') {
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
     * Simpan Logbook Siswa.
     */
    public function storeLogbook(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        if ($this->isMagangSelesai($user)) {
            return back()->with('error', 'Masa magang Anda telah berakhir. Anda tidak dapat mengisi logbook lagi.');
        }

        $todayString = Carbon::now()->toDateString();
        
        // Check if already filled today
        $existing = $user->logbooks()->whereDate('tanggal', $todayString)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah mengisi kegiatan untuk hari ini.')->with('active_tab', 'logbook');
        }

        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'kegiatan' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $tanggal = $validated['tanggal'] ?? $todayString;

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('logbooks', 'public');
        }

        $user->logbooks()->create([
            'tanggal' => $tanggal,
            'kegiatan' => $validated['kegiatan'],
            'foto' => $fotoPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Logbook berhasil disimpan.')->with('active_tab', 'logbook');
    }
}
