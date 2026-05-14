<?php

namespace App\Http\Controllers;

use App\Models\LaporanAkhir;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruVerifikasiController extends GuruBaseController
{
    /**
     * Daftar Laporan Akhir yang perlu diverifikasi.
     */
    public function verifikasiLaporan(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');
        $siswaNisns = $user->siswas->pluck('nisn')->toArray();

        $query = LaporanAkhir::whereIn('nisn', $siswaNisns)->with('siswa');

        if ($search) {
            $query->whereHas('siswa', fn($q) => $q->where('nama', 'like', "%{$search}%")->orWhere('nisn', 'like', "%{$search}%"));
        }

        $laporanPending = (clone $query)->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        
        $historyQuery = (clone $query)->where('status', '!=', 'pending');
        if ($periodeId) $historyQuery->whereHas('siswa', fn($q) => $q->where('id_tahun_ajaran', $periodeId));
        $historyLaporan = $historyQuery->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $periodeOptions = TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_guru', $user->id_guru))
            ->orderBy('tgl_mulai', 'desc')->get();

        return view('guru.verifikasi', compact('user', 'laporanPending', 'historyLaporan', 'search', 'periodeId', 'periodeOptions'));
    }

    /**
     * Show detail verifikasi.
     */
    public function showVerifikasiLaporan($id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $laporan = LaporanAkhir::with('siswa')->findOrFail($id);

        if ($laporan->siswa->id_guru != $user->id_guru) {
            return redirect()->route('guru.verifikasi')->with('error', 'Akses ditolak.');
        }

        $history = $laporan->siswa->laporanAkhirs()->where('id_laporan', '!=', $id)->get();
        return view('guru.verifikasi', compact('user', 'laporan', 'history'));
    }

    /**
     * Update verifikasi laporan akhir.
     */
    public function updateVerifikasiLaporan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'catatan' => 'nullable|string',
        ]);

        $laporan = LaporanAkhir::with('siswa')->findOrFail($id);
        if ($laporan->siswa->id_guru != Auth::user()->id_guru) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $laporan->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        if ($request->status == 'approved') {
            $laporan->siswa->update(['status' => 'selesai']);
        }

        return redirect()->route('guru.verifikasi')->with('success', 'Verifikasi laporan berhasil diperbarui.');
    }
}
