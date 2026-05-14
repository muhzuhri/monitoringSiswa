<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\KriteriaPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembimbingEvaluasiController extends PembimbingBaseController
{
    /**
     * Menampilkan daftar penilaian dan form evaluasi siswa
     */
    public function evaluasiSiswa(Request $request)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $search = $request->input('search');

        $query = Siswa::where('id_pembimbing', $pembimbing->id_pembimbing)
            ->with(['penilaians' => function ($q) {
                $q->where('pemberi_nilai', 'Dosen Pembimbing')
                  ->with('penilaianDetails.kriteria')
                  ->orderBy('created_at', 'desc');
            }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('sekolah', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->orderBy('nama', 'asc')->get();

        $siswasPending = $allSiswas->filter(function ($s) {
            return !$s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        $siswasDone = $allSiswas->filter(function ($s) {
            return $s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
        });

        $kriteriaKustom = $this->ensureKriteriaExists($pembimbing->id_pembimbing);
        $kriteria = $kriteriaKustom;
            
        return view('pembimbing.penilaianSiswa', compact(
            'pembimbing', 'siswasPending', 'siswasDone', 
            'kriteria', 'kriteriaKustom', 'search'
        ));
    }

    /**
     * Menampilkan form input evaluasi (mode halaman penuh)
     */
    public function inputEvaluasi($nisn)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        $siswa = Siswa::where('nisn', $nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->firstOrFail();

        $penilaian = Penilaian::where('nisn', $nisn)
            ->where('pemberi_nilai', 'Dosen Pembimbing')
            ->with('penilaianDetails.kriteria')
            ->orderBy('created_at', 'desc')
            ->first();

        $kriteriaKustom = $this->ensureKriteriaExists($pembimbing->id_pembimbing);
        $kriteria = $kriteriaKustom;

        return view('pembimbing.penilaianSiswa', compact('pembimbing', 'siswa', 'penilaian', 'kriteria', 'kriteriaKustom'));
    }

    /**
     * Menyimpan data evaluasi/penilaian baru ke database
     */
    public function storeEvaluasi(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn',
            'kategori' => 'required|string',
            'komentar' => 'nullable|string',
            'saran' => 'nullable|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
        ]);

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();

        $isMySiswa = Siswa::where('nisn', $request->nisn)
            ->where('id_pembimbing', $pembimbing->id_pembimbing)
            ->exists();

        if (!$isMySiswa) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $allScores = collect($request->scores);
        $avg = $allScores->avg();

        $penilaian = Penilaian::create([
            'nisn' => $request->nisn,
            'pemberi_nilai' => 'Dosen Pembimbing',
            'rata_rata' => $avg,
            'kategori' => $request->kategori,
            'komentar' => $request->komentar,
            'saran' => $request->saran,
        ]);

        foreach ($request->scores as $kriteriaId => $score) {
            PenilaianDetail::create([
                'id_penilaian' => $penilaian->id_penilaian,
                'id_kriteria' => $kriteriaId,
                'skor' => $score
            ]);
        }

        return redirect()->route('pembimbing.evaluasi')->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Menampilkan halaman manajemen kriteria penilaian.
     */
    public function kriteriaPenilaian()
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        
        $kriteriaKustom = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        $kriteriaDefault = KriteriaPenilaian::whereNull('id_pembimbing')
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        return view('pembimbing.kriteria', compact('pembimbing', 'kriteriaKustom', 'kriteriaDefault'));
    }

    /**
     * Menyimpan kriteria penilaian baru.
     */
    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;

        KriteriaPenilaian::create([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
            'id_pembimbing' => $supervisorId,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil ditambahkan.');
    }

    /**
     * Memperbarui kriteria penilaian.
     */
    public function updateKriteria(Request $request, $id)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:sikap_kerja,kompetensi_keahlian',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
        ]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus kriteria penilaian.
     */
    public function destroyKriteria($id)
    {
        /** @var \App\Models\Pembimbing $pembimbing */
        $pembimbing = Auth::user();
        $supervisorId = (string) $pembimbing->id_pembimbing;
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)
            ->where('id_pembimbing', $supervisorId)
            ->firstOrFail();

        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
