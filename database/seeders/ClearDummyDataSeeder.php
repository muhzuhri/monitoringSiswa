<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClearDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nisns = ['123456789', '234567890', '345678901'];

        echo "Membersihkan data dummy untuk NISN: " . implode(', ', $nisns) . "...\n";

        \App\Models\Absensi::whereIn('nisn', $nisns)->delete();
        \App\Models\Logbook::whereIn('nisn', $nisns)->delete();

        echo "Data dummy berhasil dibersihkan!\n";
    }
}
