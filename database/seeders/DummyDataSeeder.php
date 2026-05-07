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
        $nisns = ['12345678', '123456782', '123456783'];
        $endDate = Carbon::create(2026, 5, 15);

        echo "Memulai pembuatan data dummy absensi...\n";

        foreach ($nisns as $nisn) {
            $siswa = Siswa::where('nisn', $nisn)->first();
            if (!$siswa) {
                echo "Siswa dengan NISN $nisn tidak ditemukan, skip...\n";
                continue;
            }

            // Gunakan 1 April jika tgl_mulai_magang kosong
            $startDate = $siswa->tgl_mulai_magang ? Carbon::parse($siswa->tgl_mulai_magang) : Carbon::create(2026, 4, 1);
            
            if ($startDate->gt($endDate)) {
                $startDate = Carbon::create(2026, 4, 1);
            }

            echo "Gnerating data untuk {$siswa->nama} ($nisn) dari {$startDate->toDateString()} s/d {$endDate->toDateString()}\n";

            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    Absensi::updateOrCreate(
                        ['nisn' => $nisn, 'tanggal' => $current->toDateString()],
                        [
                            'status' => 'hadir',
                            'jam_masuk' => '07:30:00',
                            'jam_pulang' => '16:00:00',
                            'latitude' => -2.9847,
                            'longitude' => 104.7323
                        ]
                    );
                }
                $current->addDay();
            }
        }

        echo "Data dummy berhasil ditambahkan!\n";
    }
}
