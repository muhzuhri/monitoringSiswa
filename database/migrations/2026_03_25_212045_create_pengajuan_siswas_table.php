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
        Schema::create('pengajuan_siswas', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->string('nisn');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('jenis', ['absensi', 'kegiatan']);
            $table->text('deskripsi')->nullable();
            $table->text('alasan_terlambat');
            $table->string('bukti')->nullable();
            $table->enum('status', ['pending', 'valid', 'ditolak'])->default('pending');
            $table->timestamps();

            $table->foreign('nisn')->references('nisn')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_siswas');
    }
};
