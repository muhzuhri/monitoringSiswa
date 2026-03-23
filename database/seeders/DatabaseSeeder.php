<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun admin default
        Admin::create([
            'name'     => 'Administrator',
            'email'    => 'zuhrimubarak@gmail.com',
            'password' => 'zuhri123', // akan otomatis di-hash oleh casts di model
            'instansi' => 'Admin',
        ]);
    }
}