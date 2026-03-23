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
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->renameColumn('id', 'id_kegiatan');
            $table->renameColumn('siswa_nisn', 'nisn');
        });

        Schema::table('penilaian', function (Blueprint $table) {
            $table->renameColumn('id', 'id_penilaian');
            $table->renameColumn('siswa_nisn', 'nisn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->renameColumn('id_kegiatan', 'id');
            $table->renameColumn('nisn', 'siswa_nisn');
        });

        Schema::table('penilaian', function (Blueprint $table) {
            $table->renameColumn('id_penilaian', 'id');
            $table->renameColumn('nisn', 'siswa_nisn');
        });
    }
};
