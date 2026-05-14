<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\Logbook;
use App\Models\PengajuanSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembimbingPengajuanController extends PembimbingBaseController
{
    /**
     * Menampilkan daftar pengajuan (Lupa Absensi / Kegiatan) dari siswa binaan
     */
    public function pengajuanSiswa(Request $request)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
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

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $pengajuan = PengajuanSiswa::with('siswa')->findOrFail($id);

        if ($pengajuan->siswa->id_pembimbing !== $pembimbing->id_pembimbing) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        if ($request->action === 'approve') {
            $pengajuan->status = 'valid';

            if ($pengajuan->jenis === 'absensi') {
                $statusHadir = 'hadir';
                if ($pengajuan->jam_masuk && substr($pengajuan->jam_masuk, 0, 5) > '08:00') {
                    $statusHadir = 'terlambat';
                }

                $existingAbsensi = Absensi::where('nisn', $pengajuan->nisn)
                    ->where('tanggal', $pengajuan->tanggal)
                    ->first();

                if ($existingAbsensi) {
                    $existingAbsensi->update([
                        'jam_masuk' => $pengajuan->jam_masuk ?: $existingAbsensi->jam_masuk,
                        'jam_pulang' => $pengajuan->jam_pulang ?: $existingAbsensi->jam_pulang,
                        'status' => $statusHadir,
                        'verifikasi' => 'verified',
                        'keterangan' => 'Validasi Lupa Absensi',
                    ]);
                } else {
                    Absensi::create([
                        'nisn' => $pengajuan->nisn,
                        'tanggal' => $pengajuan->tanggal,
                        'jam_masuk' => $pengajuan->jam_masuk,
                        'jam_pulang' => $pengajuan->jam_pulang,
                        'status' => $statusHadir,
                        'verifikasi' => 'verified',
                        'keterangan' => 'Validasi Lupa Absensi',
                    ]);
                }
            } else if ($pengajuan->jenis === 'kegiatan') {
                $existingLogbook = Logbook::where('nisn', $pengajuan->nisn)
                    ->where('tanggal', $pengajuan->tanggal)
                    ->first();
                    
                if ($existingLogbook) {
                    $existingLogbook->update([
                        'kegiatan' => $pengajuan->deskripsi,
                        'status' => 'verified',
                        'catatan_pembimbing' => 'Validasi Lupa Kegiatan'
                    ]);
                } else {
                    Logbook::create([
                        'nisn' => $pengajuan->nisn,
                        'tanggal' => $pengajuan->tanggal,
                        'kegiatan' => $pengajuan->deskripsi,
                        'status' => 'verified',
                        'catatan_pembimbing' => 'Validasi Lupa Kegiatan'
                    ]);
                }
            }

            $message = 'Pengajuan berhasil disetujui dan data otomatis ditambahkan ke sistem.';

        } else {
            $pengajuan->status = 'ditolak';
            $message = 'Pengajuan ditolak.';
        }

        $pengajuan->save();

        return redirect()->back()->with('success', $message);
    }
}
