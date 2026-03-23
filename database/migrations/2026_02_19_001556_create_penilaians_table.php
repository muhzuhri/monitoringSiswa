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
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('siswa_nisn');
            $table->string('pemberi_nilai');
            $table->integer('nilai_teknis');
            $table->integer('nilai_non_teknis');
            $table->float('rata_rata');
            $table->text('komentar')->nullable();
            $table->string('kategori'); // harian, mingguan, akhir
            $table->timestamps();

            $table->foreign('siswa_nisn')->references('nisn')->on('siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
