<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $lokasis = \App\Models\LokasiAbsensi::orderBy('nama_lokasi')->get();
        $user = auth()->user();
        return view('admin.kelolaLokasi', compact('lokasis', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
        ]);

        \App\Models\LokasiAbsensi::create($validated);

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $lokasi = \App\Models\LokasiAbsensi::findOrFail($id);

        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $lokasi->update($validated);

        return redirect()->back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $lokasi = \App\Models\LokasiAbsensi::findOrFail($id);
        $lokasi->delete();

        return redirect()->back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
