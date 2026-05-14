<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class GuruBaseController extends Controller
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
     * Helper to group students by leader and prepare for view
     */
    protected function mapGroupedSiswa($siswas)
    {
        return $siswas->groupBy('nisn_ketua')->map(function ($group) {
            $leader = $group->where('nisn', $group->first()->nisn_ketua)->first() ?: $group->first();
            return [
                'leader' => $leader,
                'members' => $group,
                'is_group' => $group->count() > 1 || $leader->tipe_magang === 'kelompok',
            ];
        });
    }
}
