<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SiswaProfilController extends SiswaBaseController
{
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
}
