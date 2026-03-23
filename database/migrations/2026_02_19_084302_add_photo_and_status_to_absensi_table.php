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
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('foto_masuk')->nullable()->after('jam_masuk');
            $table->string('foto_pulang')->nullable()->after('jam_pulang');
            // We use DB statement because SQLite/MySQL enum modification varies, 
            // but for Laravel migration on MySQL/Postgre we usually drop and re-add or use change().
            // Since this is a fresh migration we just created, I'll define it here.
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa', 'terlambat'])->default('hadir')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            //
        });
    }
};
