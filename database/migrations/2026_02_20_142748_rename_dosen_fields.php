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
        Schema::table('dosen', function (Blueprint $table) {
            $table->renameColumn('nidn', 'jabatan');
            $table->renameColumn('prodi', 'instansi');
            $table->renameColumn('perguruan_tinggi', 'no_telp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->renameColumn('jabatan', 'nidn');
            $table->renameColumn('instansi', 'prodi');
            $table->renameColumn('no_telp', 'perguruan_tinggi');
        });
    }
};
