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
        $nisns = ['12345678', '123456782', '123456783'];

        echo "Membersihkan data dummy untuk NISN: " . implode(', ', $nisns) . "...\n";

        Absensi::whereIn('nisn', $nisns)->delete();

        echo "Data dummy berhasil dibersihkan!\n";
    }
}
