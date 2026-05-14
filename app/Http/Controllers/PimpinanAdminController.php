<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PimpinanAdminController extends PimpinanBaseController
{
    public function kelolaAdmin(Request $request)
    {
        $user = $this->authorizePimpinan();
        $search = $request->input('search');
        
        $query = Admin::query();
        if ($search) {
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        $admins = $query->orderBy('nama', 'asc')->paginate(10);
        
        return view('pimpinan.admin', compact('user', 'admins', 'search'));
    }

    public function storeAdmin(Request $request)
    {
        $this->authorizePimpinan();
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function updateAdmin(Request $request, $id)
    {
        $this->authorizePimpinan();
        $admin = Admin::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:admin,email,' . $id . ',id_admin',
            'password' => 'nullable|min:6',
        ]);

        $admin->nama = $request->nama;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return back()->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroyAdmin($id)
    {
        $this->authorizePimpinan();
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }
}
