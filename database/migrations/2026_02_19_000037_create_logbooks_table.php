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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->string('siswa_nisn', 50);
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan_pembimbing')->nullable();
            $table->timestamps();

            $table->foreign('siswa_nisn')->references('nisn')->on('siswa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
