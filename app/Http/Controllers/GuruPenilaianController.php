<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\KriteriaPenilaian;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruPenilaianController extends GuruBaseController
{
    /**
     * Menampilkan daftar siswa untuk diberikan penilaian.
     */
    public function daftarPenilaian(Request $request)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $search = $request->input('search');
        $periodeId = $request->input('periode');

        $query = $user->siswas()->with('penilaians');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            });
        }

        $allSiswas = $query->get();
        $siswasPending = $allSiswas->filter(fn($s) => !$s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first());
        $siswasDone = $allSiswas->filter(function ($s) use ($periodeId) {
            $hasNilai = $s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
            if (!$hasNilai) return false;
            if ($periodeId && (string)$s->id_tahun_ajaran !== (string)$periodeId) return false;
            return true;
        });

        $periodeOptions = TahunAjaran::whereHas('siswas', fn($q) => $q->where('id_guru', $user->id_guru))
            ->orderBy('tgl_mulai', 'desc')->get();

        $kriteriaKustom = KriteriaPenilaian::where('id_guru', $user->id_guru)
            ->orderBy('tipe')->orderBy('urutan')->get();

        if ($kriteriaKustom->count() === 0) {
            $defaults = KriteriaPenilaian::whereNull('id_guru')->whereNull('id_pembimbing')
                ->whereIn('tipe', ['guru_kepribadian', 'guru_kemampuan'])->get();

            foreach ($defaults as $d) {
                KriteriaPenilaian::create([
                    'nama_kriteria' => $d->nama_kriteria,
                    'tipe' => $d->tipe,
                    'jurusan' => $d->jurusan,
                    'urutan' => $d->urutan,
                    'id_guru' => $user->id_guru,
                ]);
            }
            $kriteriaKustom = KriteriaPenilaian::where('id_guru', $user->id_guru)
                ->orderBy('tipe')->orderBy('urutan')->get();
        }

        $kriteria = $kriteriaKustom;
        return view('guru.penilaian', compact('user', 'siswasPending', 'siswasDone', 'search', 'periodeId', 'periodeOptions', 'kriteria', 'kriteriaKustom'));
    }

    /**
     * Menampilkan form input penilaian untuk siswa tertentu.
     */
    public function inputPenilaian($nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();
        
        $penilaian = $siswa->penilaians()->where('pemberi_nilai', 'Guru Pembimbing')
            ->with('penilaianDetails.kriteria')->first();

        $kriteria = KriteriaPenilaian::whereIn('tipe', ['guru_kepribadian', 'guru_kemampuan'])
            ->where('id_guru', $user->id_guru)->orderBy('tipe')->orderBy('urutan')->get();

        return view('guru.penilaian', compact('user', 'siswa', 'penilaian', 'kriteria'));
    }

    /**
     * Menyimpan hasil penilaian siswa.
     */
    public function storePenilaian(Request $request, $nisn)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $siswa = $user->siswas()->where('nisn', $nisn)->firstOrFail();

        $request->validate([
            'kategori' => 'required|string',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:0|max:100',
            'saran' => 'nullable|string',
        ]);

        $allScores = collect($request->scores);
        $avg = $allScores->avg();

        $penilaian = Penilaian::updateOrCreate(
            ['nisn' => $nisn, 'pemberi_nilai' => 'Guru Pembimbing'],
            ['rata_rata' => $avg, 'kategori' => $request->kategori, 'saran' => $request->saran]
        );

        PenilaianDetail::where('id_penilaian', $penilaian->id_penilaian)->delete();
        foreach ($request->scores as $kriteriaId => $score) {
            PenilaianDetail::create([
                'id_penilaian' => $penilaian->id_penilaian,
                'id_kriteria' => $kriteriaId,
                'skor' => $score
            ]);
        }

        return redirect()->route('guru.penilaian')->with('success', 'Penilaian untuk ' . $siswa->nama . ' berhasil disimpan.');
    }

    /**
     * Menyimpan kriteria penilaian baru.
     */
    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
            'tipe' => 'required|in:guru_kepribadian,guru_kemampuan',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Guru $user */
        $user = Auth::user();

        KriteriaPenilaian::create([
            'nama_kriteria' => $request->nama_kriteria,
            'tipe' => $request->tipe,
            'urutan' => $request->urutan ?? 0,
            'id_guru' => $user->id_guru,
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
            'tipe' => 'required|in:guru_kepribadian,guru_kemampuan',
            'urutan' => 'nullable|integer',
        ]);

        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)->where('id_guru', $user->id_guru)->firstOrFail();

        $kriteria->update(['nama_kriteria' => $request->nama_kriteria, 'tipe' => $request->tipe, 'urutan' => $request->urutan ?? 0]);

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil diperbarui.');
    }

    /**
     * Menghapus kriteria penilaian.
     */
    public function destroyKriteria($id)
    {
        /** @var \App\Models\Guru $user */
        $user = Auth::user();
        $kriteria = KriteriaPenilaian::where('id_kriteria', $id)->where('id_guru', $user->id_guru)->firstOrFail();
        $kriteria->delete();

        return redirect()->back()->with('success', 'Kriteria penilaian berhasil dihapus.');
    }
}
