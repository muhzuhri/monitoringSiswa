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
        Schema::create('konfigurasi_laporans', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_laporan')->unique();
            $table->string('header_1')->nullable();
            $table->string('header_2')->nullable();
            $table->string('header_3')->nullable();
            $table->string('header_4')->nullable();
            $table->string('header_5')->nullable(); // Extra line just in case
            $table->string('color_primary')->nullable(); // For certificate
            $table->text('template_isi')->nullable(); // For certificate content
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigurasi_laporans');
    }
};
