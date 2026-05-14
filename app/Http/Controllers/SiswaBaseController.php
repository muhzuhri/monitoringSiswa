<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SiswaBaseController extends Controller
{
    /**
     * Helper to calculate student internship progress percentage
     */
    protected function calculateProgress($user)
    {
        if (!$user->tgl_mulai_magang || !$user->tgl_selesai_magang) {
            return 0;
        }

        $start = Carbon::parse($user->tgl_mulai_magang);
        $end = Carbon::parse($user->tgl_selesai_magang);
        $now = Carbon::now();

        if ($now->lt($start)) return 0;
        if ($now->gt($end)) return 100;

        $totalDays = max(1, $start->diffInDays($end));
        $daysPassed = (int) $start->diffInDays($now);

        return min(100, round(($daysPassed / $totalDays) * 100));
    }

    /**
     * Helper to check if student's internship has ended.
     */
    protected function isMagangSelesai($user)
    {
        if ($user->status === 'selesai') {
            return true;
        }

        if ($user->tgl_selesai_magang) {
            // Selesai jika hari ini sudah melewati tanggal selesai (akhir hari)
            return Carbon::parse($user->tgl_selesai_magang)->endOfDay()->isPast();
        }

        return false;
    }

    /**
     * Helper to get full attendance history including "Alpha" for missing workdays.
     */
    protected function getFullAttendanceData($user, $startDate = null, $endDate = null)
    {
        // Internship bound dates
        $internStart = $user->tgl_mulai_magang ? Carbon::parse($user->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $user->tgl_selesai_magang ? Carbon::parse($user->tgl_selesai_magang)->startOfDay() : null;

        // Use student's internship start date or current month's start
        $start = $startDate ? Carbon::parse($startDate) : ($internStart ?: Carbon::now()->startOfMonth());
        $end   = $endDate   ? Carbon::parse($endDate)   : Carbon::now();

        // Cap the end to internship end date so post-internship dates are never included
        if ($internEnd && $end->gt($internEnd)) {
            $end = $internEnd->copy();
        }

        // Cap calculations to today
        $today = Carbon::now()->startOfDay();

        $absensis = $user->absensis()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('tanggal');

        $fullHistory = [];
        $current = $start->copy();

        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isWeekend();

            if (isset($absensis[$dateStr])) {
                $fullHistory[] = $absensis[$dateStr];
            } else {
                // No attendance record found in DB
                // CHECK: Must be a workday, must be in the past, within internship period
                $afterStart  = $internStart && $current->gte($internStart);
                $beforeEnd   = !$internEnd   || $current->lte($internEnd);
                if (!$isWeekend && $current->lt($today) && $afterStart && $beforeEnd) {
                    // It's a past workday during internship period -> Alpha
                    $fullHistory[] = (object)[
                        'tanggal'    => $dateStr,
                        'jam_masuk'  => null,
                        'jam_pulang' => null,
                        'status'     => 'alpa',
                        'verifikasi' => 'pending',
                        'foto_masuk' => null,
                        'foto_pulang'=> null,
                    ];
                }
            }
            $current->addDay();
        }

        return collect($fullHistory);
    }

    /**
     * Helper to get full logbook history including "Alpha" for missing entries.
     */
    protected function getFullLogbookData($user, $startDate = null, $endDate = null)
    {
        $internStart = $user->tgl_mulai_magang ? Carbon::parse($user->tgl_mulai_magang)->startOfDay() : null;
        $internEnd   = $user->tgl_selesai_magang ? Carbon::parse($user->tgl_selesai_magang)->startOfDay() : null;

        $start = $startDate ? Carbon::parse($startDate) : ($internStart ?: Carbon::now()->startOfMonth());
        $end   = $endDate   ? Carbon::parse($endDate)   : Carbon::now();

        // Cap the end to internship end date so post-internship dates are never included
        if ($internEnd && $end->gt($internEnd)) {
            $end = $internEnd->copy();
        }

        $today = Carbon::now()->startOfDay();

        $logbooks = $user->logbooks()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy('tanggal');

        $fullHistory = [];
        $current = $start->copy();

        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $isWeekend = $current->isWeekend();

            if (isset($logbooks[$dateStr])) {
                $fullHistory[] = $logbooks[$dateStr];
            } else {
                $afterStart = $internStart && $current->gte($internStart);
                $beforeEnd  = !$internEnd   || $current->lte($internEnd);
                if (!$isWeekend && $current->lt($today) && $afterStart && $beforeEnd) {
                    $fullHistory[] = (object)[
                        'tanggal'            => $dateStr,
                        'kegiatan'           => 'Alpha',
                        'status'             => 'pending',
                        'catatan_pembimbing' => '-',
                        'created_at'         => null,
                    ];
                }
            }
            $current->addDay();
        }

        return collect($fullHistory);
    }

    /**
     * Hitung jarak antara dua koordinat (Haversine Formula).
     * Hasil dalam satuan Meter.
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
