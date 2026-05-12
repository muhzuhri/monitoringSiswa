<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Absensi;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->generateCase1();
        $this->generateCase2();
        
        echo "\nData dummy berhasil ditambahkan!\n";
    }

    private function generateCase1()
    {
        $nisns = ['1234567813', '1234567814', '1234567815'];
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::create(2026, 3, 31);

        echo "Generating Case 1 (Alpha=3, Izin=4, Sakit=5, Rejected=1-2) for NISN: " . implode(', ', $nisns) . "\n";

        foreach ($nisns as $nisn) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if (!$siswa) continue;

            $workDays = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    $workDays[] = $current->toDateString();
                }
                $current->addDay();
            }

            // Distribute special statuses non-consecutively
            $totalSpecial = 3 + 4 + 5 + rand(1, 2); // Alpha, Izin, Sakit, Rejected
            $specialDates = [];
            
            $availableIndices = array_keys($workDays);
            shuffle($availableIndices);

            $selectedIndices = [];
            foreach ($availableIndices as $idx) {
                if (count($selectedIndices) >= $totalSpecial) break;
                
                // Check if adjacent index is already selected
                $isAdjacent = false;
                foreach ($selectedIndices as $sIdx) {
                    if (abs($sIdx - $idx) <= 1) {
                        $isAdjacent = true;
                        break;
                    }
                }
                
                if (!$isAdjacent) {
                    $selectedIndices[] = $idx;
                }
            }

            // Fallback if we couldn't find enough non-adjacent (shouldn't happen with 65 days vs 14)
            while (count($selectedIndices) < $totalSpecial) {
                $idx = array_rand($workDays);
                if (!in_array($idx, $selectedIndices)) {
                    $selectedIndices[] = $idx;
                }
            }

            $specialAssignment = [];
            $types = array_merge(
                array_fill(0, 3, ['status' => 'alpa', 'verifikasi' => 'verified']),
                array_fill(0, 4, ['status' => 'izin', 'verifikasi' => 'verified']),
                array_fill(0, 5, ['status' => 'sakit', 'verifikasi' => 'verified'])
            );
            
            $rejectedCount = $totalSpecial - 12;
            for ($i = 0; $i < $rejectedCount; $i++) {
                $types[] = ['status' => 'alpa', 'verifikasi' => 'rejected'];
            }
            
            shuffle($types);
            foreach ($selectedIndices as $i => $idx) {
                $specialAssignment[$workDays[$idx]] = $types[$i];
            }

            foreach ($workDays as $date) {
                $assignment = $specialAssignment[$date] ?? ['status' => 'hadir', 'verifikasi' => 'verified'];
                
                Absensi::updateOrCreate(
                    ['nisn' => $nisn, 'tanggal' => $date],
                    [
                        'status' => $assignment['status'],
                        'verifikasi' => $assignment['verifikasi'],
                        'jam_masuk' => $assignment['status'] == 'hadir' ? '07:30:00' : null,
                        'jam_pulang' => $assignment['status'] == 'hadir' ? '16:00:00' : null,
                        'latitude' => -2.9847,
                        'longitude' => 104.7323,
                        'keterangan' => $assignment['status'] != 'hadir' ? 'Data dummy' : null
                    ]
                );

                \App\Models\Logbook::updateOrCreate(
                    ['nisn' => $nisn, 'tanggal' => $date],
                    [
                        'kegiatan' => 'Melakukan pekerjaan rutin dan dokumentasi teknis pada hari ke-' . (array_search($date, $workDays) + 1),
                        'status' => 'verified'
                    ]
                );
            }
            echo "Done for {$siswa->nama}\n";
        }
    }

    private function generateCase2()
    {
        $nisns = ['123456787', '123456788', '123456789'];
        $startDate = Carbon::create(2026, 5, 1);
        $endDate = Carbon::create(2026, 5, 7);

        echo "Generating Case 2 (All Present) for NISN: " . implode(', ', $nisns) . "\n";

        foreach ($nisns as $nisn) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if (!$siswa) continue;

            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    Absensi::updateOrCreate(
                        ['nisn' => $nisn, 'tanggal' => $current->toDateString()],
                        [
                            'status' => 'hadir',
                            'verifikasi' => 'verified',
                            'jam_masuk' => '07:45:00',
                            'jam_pulang' => '16:15:00',
                            'latitude' => -2.9847,
                            'longitude' => 104.7323
                        ]
                    );

                    \App\Models\Logbook::updateOrCreate(
                        ['nisn' => $nisn, 'tanggal' => $current->toDateString()],
                        [
                            'kegiatan' => 'Eksplorasi sistem dan pengenalan lingkungan magang pada tanggal ' . $current->toDateString(),
                            'status' => 'verified'
                        ]
                    );
                }
                $current->addDay();
            }
            echo "Done for {$siswa->nama}\n";
        }
    }
}
