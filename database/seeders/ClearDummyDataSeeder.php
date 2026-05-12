<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;

class ClearDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nisns = [
            '12345678', '123456782', '123456783',
            '1234567813', '1234567814', '1234567815',
            '123456787', '123456788', '123456789'
        ];

        echo "Membersihkan data dummy absensi dan kegiatan untuk NISN target...\n";

        \App\Models\Absensi::whereIn('nisn', $nisns)->delete();
        \App\Models\Logbook::whereIn('nisn', $nisns)->delete();

        echo "Data dummy berhasil dibersihkan!\n";
    }
}
