<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LokasiAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\LokasiAbsensi::create([
            'nama_lokasi' => 'Fasilkom Kampus Indralaya',
            'latitude'    => -3.2185244,
            'longitude'   => 104.6496464,
            'radius'      => 500,
        ]);

        \App\Models\LokasiAbsensi::create([
            'nama_lokasi' => 'Fasilkom Kampus Palembang',
            'latitude'    => -2.98472005,
            'longitude'   => 104.73225951,
            'radius'      => 500,
        ]);
    }
}
