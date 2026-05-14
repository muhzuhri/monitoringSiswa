<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaPengajuanController extends SiswaBaseController
{
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
        $isFinished = $this->isMagangSelesai($user);

        return view('siswa.pengajuan', compact('user', 'pengajuans', 'isFinished'));
    }

    /**
     * Menyimpan Pengajuan Lupa Absensi / Kegiatan.
     */
    public function storePengajuan(Request $request)
    {
        /** @var \App\Models\Siswa $user */
        $user = Auth::user();

        if ($this->isMagangSelesai($user)) {
            return back()->with('error', 'Masa magang Anda telah berakhir. Anda tidak dapat melakukan pengajuan lagi.');
        }

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
            return back()->with('error', 'Untuk jenis absensi, minimal Jam Masuk atau Jam Pulang harus diisi.')->withInput();
        }

        if ($validated['jenis'] === 'kegiatan' && empty($validated['deskripsi'])) {
            return back()->with('error', 'Untuk jenis kegiatan, Deskripsi Kegiatan wajib diisi.')->withInput();
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
}
