<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pimpinan;
use Illuminate\Support\Facades\Hash;

class PimpinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pimpinan::create([
            'nama' => 'Pimpinan Monitoring',
            'email' => 'pimpinan@gmail.com',
            'password' => Hash::make('password'),
            'no_hp' => '081234567890',
            'jabatan' => 'Ketua Jurusan Sistem Informasi',
        ]);
    }
}
