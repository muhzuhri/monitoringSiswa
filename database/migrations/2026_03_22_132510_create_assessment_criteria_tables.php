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
        Schema::create('kriteria_penilaian', function (Blueprint $table) {
            $table->id('id_kriteria');
            $table->string('nama_kriteria');
            $table->enum('tipe', ['sikap_kerja', 'kompetensi_keahlian']);
            $table->string('jurusan')->nullable(); // null means applicable to all majors
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('penilaian_detail', function (Blueprint $table) {
            $table->id('id_penilaian_detail');
            $table->unsignedBigInteger('id_penilaian');
            $table->unsignedBigInteger('id_kriteria');
            $table->decimal('skor', 5, 2);
            $table->timestamps();

            $table->foreign('id_penilaian')->references('id_penilaian')->on('penilaian')->onDelete('cascade');
            $table->foreign('id_kriteria')->references('id_kriteria')->on('kriteria_penilaian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_detail');
        Schema::dropIfExists('kriteria_penilaian');
    }
};
