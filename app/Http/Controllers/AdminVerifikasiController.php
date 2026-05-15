<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Admin;
use App\Contracts\HasRole;
use App\Mail\AccountCreatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminVerifikasiController extends Controller
{
    protected function authorizeAdmin(): Admin
    {
        $user = Auth::user();
        $role = $user instanceof HasRole ? $user->getRole() : null;
        abort_unless($user && $role === 'admin', 403);

        return $user;
    }

    public function index()
    {
        $admin = $this->authorizeAdmin();

        $siswaPending = Siswa::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $guruPending = Guru::where('status', 'pending')->orderBy('created_at', 'desc')->get();

        return view('admin.verifikasiRegistrasi', compact('admin', 'siswaPending', 'guruPending'));
    }

    public function verifikasiSiswa(Request $request, $nisn)
    {
        $this->authorizeAdmin();
        $siswa = Siswa::where('nisn', $nisn)->firstOrFail();
        
        $action = $request->input('action'); // 'approve' or 'reject'

        if ($action === 'approve') {
            $siswa->update(['status' => 'aktif']);

            // Kirim Email (Queued for speed)
            try {
                Mail::to($siswa->email)->queue(new AccountCreatedMail(
                    $siswa->nama,
                    $siswa->email,
                    'Siswa Magang'
                ));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengantrekan email verifikasi siswa: ' . $e->getMessage());
            }

            return back()->with('success', "Akun siswa {$siswa->nama} berhasil diverifikasi.");
        } else {
            // Jika reject, hapus atau beri status rejected. User ingin verifikasi, biasanya reject berarti hapus.
            $siswa->delete();
            return back()->with('success', "Pendaftaran siswa {$siswa->nama} ditolak dan dihapus.");
        }
    }

    public function verifikasiGuru(Request $request, $id_guru)
    {
        $this->authorizeAdmin();
        $guru = Guru::where('id_guru', $id_guru)->firstOrFail();
        
        $action = $request->input('action');

        if ($action === 'approve') {
            $guru->update(['status' => 'aktif']);

            // Kirim Email (Queued for speed)
            try {
                Mail::to($guru->email)->queue(new AccountCreatedMail(
                    $guru->nama,
                    $guru->email,
                    'Guru Pembimbing'
                ));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengantrekan email verifikasi guru: ' . $e->getMessage());
            }

            return back()->with('success', "Akun guru {$guru->nama} berhasil diverifikasi.");
        } else {
            $guru->delete();
            return back()->with('success', "Pendaftaran guru {$guru->nama} ditolak dan dihapus.");
        }
    }

    public function verifikasiSemuaSiswa()
    {
        $this->authorizeAdmin();
        $siswaPending = Siswa::where('status', 'pending')->get();
        $count = $siswaPending->count();

        foreach ($siswaPending as $siswa) {
            $siswa->update(['status' => 'aktif']);
            try {
                Mail::to($siswa->email)->queue(new AccountCreatedMail(
                    $siswa->nama,
                    $siswa->email,
                    'Siswa Magang'
                ));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengantrekan email bulk verifikasi siswa: ' . $e->getMessage());
            }
        }

        return back()->with('success', "{$count} siswa berhasil diverifikasi.");
    }

    public function verifikasiSemuaGuru()
    {
        $this->authorizeAdmin();
        $guruPending = Guru::where('status', 'pending')->get();
        $count = $guruPending->count();

        foreach ($guruPending as $guru) {
            $guru->update(['status' => 'aktif']);
            try {
                Mail::to($guru->email)->queue(new AccountCreatedMail(
                    $guru->nama,
                    $guru->email,
                    'Guru Pembimbing'
                ));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengantrekan email bulk verifikasi guru: ' . $e->getMessage());
            }
        }

        return back()->with('success', "{$count} guru berhasil diverifikasi.");
    }
}
