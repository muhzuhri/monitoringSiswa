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
        Schema::table('siswa', function (Blueprint $table) {
            if (!Schema::hasColumn('siswa', 'tgl_mulai_magang')) {
                $table->date('tgl_mulai_magang')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('siswa', 'tgl_selesai_magang')) {
                $table->date('tgl_selesai_magang')->nullable()->after('tgl_mulai_magang');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            //
        });
    }
};
