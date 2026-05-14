<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PembimbingProfilController extends PembimbingBaseController
{
    /**
     * Menampilkan halaman profil pembimbing
     */
    public function profil()
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        return view('pembimbing.profil', compact('pembimbing'));
    }

    /**
     * Menyimpan pembaruan profil pembimbing
     */
    public function updateProfil(Request $request)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pembimbing,email,' . $pembimbing->id_pembimbing . ',id_pembimbing',
            'no_telp' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:100',
            'instansi' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $pembimbing->nama = $request->nama;
        $pembimbing->email = $request->email;
        $pembimbing->no_telp = $request->no_telp;
        $pembimbing->jabatan = $request->jabatan;
        $pembimbing->instansi = $request->instansi;

        if ($request->filled('password')) {
            $pembimbing->password = Hash::make($request->password);
        }

        $pembimbing->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
