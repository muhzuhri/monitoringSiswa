<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMasterDataController extends Controller
{
    public function index()
    {
        $sekolahs = Sekolah::all();
        $tahunAjarans = TahunAjaran::all();
        $user = auth()->user();
        return view('admin.master_data', compact('sekolahs', 'tahunAjarans', 'user'));
    }

    // --- SEKOLAH ---
    public function storeSekolah(Request $request)
    {
        $validated = $request->validate([
            'npsn' => 'required|string|unique:sekolah,npsn',
            'nama_sekolah' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'jenjang' => 'required|string|max:50',
            'status' => 'required|in:Negeri,Swasta',
        ]);

        Sekolah::create($validated);
        return back()->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function updateSekolah(Request $request, $id)
    {
        $sekolah = Sekolah::findOrFail($id);
        $validated = $request->validate([
            'npsn' => 'required|string|unique:sekolah,npsn,' . $id . ',id_sekolah',
            'nama_sekolah' => 'required|string|max:150',
            'alamat' => 'nullable|string',
            'jenjang' => 'required|string|max:50',
            'status' => 'required|in:Negeri,Swasta',
        ]);

        $sekolah->update($validated);
        return back()->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroySekolah($id)
    {
        Sekolah::destroy($id);
        return back()->with('success', 'Sekolah berhasil dihapus.');
    }

    // --- TAHUN AJARAN ---
    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        TahunAjaran::create($validated);
        return back()->with('success', 'Periode berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, $id)
    {
        $periode = TahunAjaran::findOrFail($id);
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $periode->update($validated);
        return back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroyPeriode($id)
    {
        TahunAjaran::destroy($id);
        return back()->with('success', 'Periode berhasil dihapus.');
    }
}
