<?php

namespace App\Http\Controllers;

use App\Models\InformasiDashboard;
use App\Models\ProgramStudi;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMasterDataController extends Controller
{
    public function index()
    {
        $sekolahs         = Sekolah::all();
        $tahunAjarans     = TahunAjaran::all();
        $informasi        = InformasiDashboard::getInstance();
        $programStudis    = ProgramStudi::orderBy('urutan')->get();
        $user             = auth()->user();

        return view('admin.master_data', compact(
            'sekolahs', 'tahunAjarans', 'informasi', 'programStudis', 'user'
        ));
    }

    // --- SEKOLAH ---
    public function storeSekolah(Request $request)
    {
        $validated = $request->validate([
            'npsn'         => 'required|string|unique:sekolah,npsn',
            'nama_sekolah' => 'required|string|max:150',
            'alamat'       => 'nullable|string',
            'jenjang'      => 'required|string|max:50',
            'status'       => 'required|in:Negeri,Swasta',
        ]);

        Sekolah::create($validated);
        return back()->with('success', 'Sekolah berhasil ditambahkan.')->with('active_tab', 'sekolah');
    }

    public function updateSekolah(Request $request, $id)
    {
        $sekolah   = Sekolah::findOrFail($id);
        $validated = $request->validate([
            'npsn'         => 'required|string|unique:sekolah,npsn,' . $id . ',id_sekolah',
            'nama_sekolah' => 'required|string|max:150',
            'alamat'       => 'nullable|string',
            'jenjang'      => 'required|string|max:50',
            'status'       => 'required|in:Negeri,Swasta',
        ]);

        $sekolah->update($validated);
        return back()->with('success', 'Sekolah berhasil diperbarui.')->with('active_tab', 'sekolah');
    }

    public function destroySekolah($id)
    {
        Sekolah::destroy($id);
        return back()->with('success', 'Sekolah berhasil dihapus.')->with('active_tab', 'sekolah');
    }

    // --- TAHUN AJARAN ---
    public function storePeriode(Request $request)
    {
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'status'       => 'required|in:aktif,tidak aktif',
        ]);

        TahunAjaran::create($validated);
        return back()->with('success', 'Periode berhasil ditambahkan.')->with('active_tab', 'periode');
    }

    public function updatePeriode(Request $request, $id)
    {
        $periode   = TahunAjaran::findOrFail($id);
        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date|after_or_equal:tgl_mulai',
            'status'       => 'required|in:aktif,tidak aktif',
        ]);

        $periode->update($validated);
        return back()->with('success', 'Periode berhasil diperbarui.')->with('active_tab', 'periode');
    }

    public function destroyPeriode($id)
    {
        TahunAjaran::destroy($id);
        return back()->with('success', 'Periode berhasil dihapus.')->with('active_tab', 'periode');
    }

    // --- INFORMASI DASHBOARD ---
    public function updateInformasi(Request $request)
    {
        $validated = $request->validate([
            'nama_fakultas'             => 'nullable|string|max:200',
            'deskripsi_banner'          => 'nullable|string',
            'visi'                      => 'nullable|string',
            'misi'                      => 'nullable|array',
            'misi.*'                    => 'nullable|string',
            'sejarah'                   => 'nullable|string',
            'jam_operasional'           => 'nullable|string|max:100',
            'deskripsi_jam_operasional' => 'nullable|string',
            'alamat_lokasi'             => 'nullable|string',
            'link_maps'                 => 'nullable|string|max:500',
            'email_kontak'              => 'nullable|string|max:150',
            'telp_kontak'               => 'nullable|string|max:50',
            'website_kontak'            => 'nullable|string|max:200',
        ]);

        // Filter misi: hapus item kosong
        $misiArray = array_values(array_filter($validated['misi'] ?? [], fn($m) => !empty(trim($m))));

        $informasi = InformasiDashboard::getInstance();
        $informasi->update([
            'nama_fakultas'             => $validated['nama_fakultas'] ?? $informasi->nama_fakultas,
            'deskripsi_banner'          => $validated['deskripsi_banner'] ?? $informasi->deskripsi_banner,
            'visi'                      => $validated['visi'] ?? $informasi->visi,
            'misi'                      => json_encode($misiArray),
            'sejarah'                   => $validated['sejarah'] ?? $informasi->sejarah,
            'jam_operasional'           => $validated['jam_operasional'] ?? $informasi->jam_operasional,
            'deskripsi_jam_operasional' => $validated['deskripsi_jam_operasional'] ?? $informasi->deskripsi_jam_operasional,
            'alamat_lokasi'             => $validated['alamat_lokasi'] ?? $informasi->alamat_lokasi,
            'link_maps'                 => $validated['link_maps'] ?? $informasi->link_maps,
            'email_kontak'              => $validated['email_kontak'] ?? $informasi->email_kontak,
            'telp_kontak'               => $validated['telp_kontak'] ?? $informasi->telp_kontak,
            'website_kontak'            => $validated['website_kontak'] ?? $informasi->website_kontak,
        ]);

        return back()->with('success', 'Informasi dashboard berhasil diperbarui.')->with('active_tab', 'informasi');
    }

    // --- PROGRAM STUDI ---
    public function storeProdi(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:150',
            'jenjang'   => 'required|string|max:10',
            'warna_dot' => 'nullable|string|max:20',
            'urutan'    => 'nullable|integer',
        ]);

        ProgramStudi::create([
            'nama'      => $validated['nama'],
            'jenjang'   => $validated['jenjang'],
            'warna_dot' => $validated['warna_dot'] ?? '#4e73df',
            'urutan'    => $validated['urutan'] ?? (ProgramStudi::max('urutan') + 1),
            'aktif'     => true,
        ]);

        return back()->with('success', 'Program studi berhasil ditambahkan.')->with('active_tab', 'informasi');
    }

    public function updateProdi(Request $request, $id)
    {
        $prodi     = ProgramStudi::findOrFail($id);
        $validated = $request->validate([
            'nama'      => 'required|string|max:150',
            'jenjang'   => 'required|string|max:10',
            'warna_dot' => 'nullable|string|max:20',
            'urutan'    => 'nullable|integer',
            'aktif'     => 'nullable|boolean',
        ]);

        $prodi->update([
            'nama'      => $validated['nama'],
            'jenjang'   => $validated['jenjang'],
            'warna_dot' => $validated['warna_dot'] ?? $prodi->warna_dot,
            'urutan'    => $validated['urutan'] ?? $prodi->urutan,
            'aktif'     => isset($validated['aktif']) ? (bool)$validated['aktif'] : $prodi->aktif,
        ]);

        return back()->with('success', 'Program studi berhasil diperbarui.')->with('active_tab', 'informasi');
    }

    public function destroyProdi($id)
    {
        ProgramStudi::destroy($id);
        return back()->with('success', 'Program studi berhasil dihapus.')->with('active_tab', 'informasi');
    }
}
