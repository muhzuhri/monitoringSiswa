<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuruProfilController extends GuruBaseController
{
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
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
