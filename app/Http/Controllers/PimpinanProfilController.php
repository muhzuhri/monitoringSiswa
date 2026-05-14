<?php

namespace App\Http\Controllers;

use App\Models\Pimpinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PimpinanProfilController extends PimpinanBaseController
{
    public function profil()
    {
        $user = $this->authorizePimpinan();
        return view('pimpinan.profil', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $user = $this->authorizePimpinan();
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pimpinan,email,' . $user->id_pimpinan . ',id_pimpinan',
            'no_hp' => 'required|string|max:20',
            'jabatan' => 'required|string|max:100',
        ]);

        Pimpinan::query()->where('id_pimpinan', $user->id_pimpinan)->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'jabatan' => $request->jabatan,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $this->authorizePimpinan();
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        Pimpinan::query()->where('id_pimpinan', $user->id_pimpinan)->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
