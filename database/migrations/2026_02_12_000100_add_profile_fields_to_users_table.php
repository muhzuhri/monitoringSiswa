<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('siswa')->after('password');

            // Data khusus siswa
            $table->string('nim', 50)->nullable()->after('role');
            $table->string('kelas', 50)->nullable()->after('nim');
            $table->string('jurusan', 100)->nullable()->after('kelas');
            $table->string('sekolah', 150)->nullable()->after('jurusan');
            $table->string('perusahaan', 150)->nullable()->after('sekolah');

            // Data khusus guru
            $table->string('nip_guru', 50)->nullable()->after('perusahaan');
            $table->string('mapel', 100)->nullable()->after('nip_guru');

            // Data khusus dosen pembimbing
            $table->string('nidn', 50)->nullable()->after('mapel');
            $table->string('prodi', 100)->nullable()->after('nidn');
            $table->string('perguruan_tinggi', 150)->nullable()->after('prodi');

            // Data khusus admin
            $table->string('instansi', 150)->nullable()->after('perguruan_tinggi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'nim',
                'kelas',
                'jurusan',
                'sekolah',
                'perusahaan',
                'nip_guru',
                'mapel',
                'nidn',
                'prodi',
                'perguruan_tinggi',
                'instansi',
            ]);
        });
    }
};

