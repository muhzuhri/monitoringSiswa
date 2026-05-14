<?php

namespace App\Http\Controllers;

use App\Models\KriteriaPenilaian;
use Carbon\Carbon;

class PembimbingBaseController extends Controller
{
    /**
     * Helper to calculate student internship progress percentage
     */
    protected function calculateProgress($siswa)
    {
        if (!$siswa->tgl_mulai_magang || !$siswa->tgl_selesai_magang) {
            return 0;
        }

        $start = Carbon::parse($siswa->tgl_mulai_magang);
        $end = Carbon::parse($siswa->tgl_selesai_magang);
        $now = Carbon::now();

        if ($now->lt($start)) return 0;
        if ($now->gt($end)) return 100;

        $totalDays = max(1, $start->diffInDays($end));
        $daysPassed = (int) $start->diffInDays($now);

        return min(100, round(($daysPassed / $totalDays) * 100));
    }

    /**
     * Helper to ensure assessment criteria exist for supervisor
     */
    protected function ensureKriteriaExists($supervisorId)
    {
        $kriteria = KriteriaPenilaian::where('id_pembimbing', $supervisorId)
            ->orderBy('tipe')
            ->orderBy('urutan')
            ->get();

        if ($kriteria->isEmpty()) {
            $allowedTypes = ['sikap_kerja', 'kompetensi_keahlian'];
            $forbiddenWords = ['Teori', 'Praktek', 'Inisiatif', 'Kreativitas', 'Kesehatan dan Keselamatan Kerja'];
            
            $defaults = KriteriaPenilaian::whereNull('id_pembimbing')
                ->whereIn('tipe', $allowedTypes)
                ->get();

            foreach ($defaults as $d) {
                $isForbidden = false;
                foreach ($forbiddenWords as $word) {
                    if (mb_stripos($d->nama_kriteria, $word) !== false) {
                        $isForbidden = true;
                        break;
                    }
                }
                if ($isForbidden) continue;
                if (mb_strtolower($d->nama_kriteria) == 'disiplin' && $d->tipe == 'kompetensi_keahlian') continue;

                KriteriaPenilaian::create([
                    'nama_kriteria' => $d->nama_kriteria,
                    'tipe' => $d->tipe,
                    'jurusan' => $d->jurusan,
                    'urutan' => $d->urutan,
                    'id_pembimbing' => $supervisorId,
                ]);
            }
            
            return KriteriaPenilaian::where('id_pembimbing', $supervisorId)
                ->orderBy('tipe')
                ->orderBy('urutan')
                ->get();
        }
        
        return $kriteria;
    }
}
