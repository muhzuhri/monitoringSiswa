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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id('id_sekolah');
            $table->string('npsn', 20)->unique();
            $table->string('nama_sekolah', 150);
            $table->text('alamat')->nullable();
            $table->string('jenjang', 50); // SMA/SMK/MA, dll
            $table->enum('status', ['Negeri', 'Swasta']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
