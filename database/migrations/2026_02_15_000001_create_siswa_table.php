<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('nisn', 50)->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('kelas', 50);
            $table->string('jurusan', 100);
            $table->string('sekolah', 150);
            $table->string('perusahaan', 150);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tgl_mulai_magang')->nullable();
            $table->date('tgl_selesai_magang')->nullable();
            $table->string('guru_nip', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
