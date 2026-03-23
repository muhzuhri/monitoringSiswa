<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('npsn')->nullable()->after('sekolah');
            $table->string('surat_balasan')->nullable()->after('npsn');
            $table->dropColumn('guru_nip');
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->string('npsn')->nullable()->after('sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['npsn', 'surat_balasan']);
            $table->string('guru_nip')->nullable();
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn('npsn');
        });
    }
};
