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
        Schema::table('guru', function (Blueprint $table) {
            $table->renameColumn('mapel', 'jabatan');
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('email'); // Adjusted after field
            $table->foreign('tahun_ajaran_id')->references('id_tahunAjaran')->on('tahun_ajaran')->onDelete('set null');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['tgl_mulai_magang', 'tgl_selesai_magang']);
            $table->unsignedBigInteger('tahun_ajaran_id')->nullable()->after('perusahaan');
            $table->foreign('tahun_ajaran_id')->references('id_tahunAjaran')->on('tahun_ajaran')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
            $table->renameColumn('jabatan', 'mapel');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
            $table->date('tgl_mulai_magang')->nullable();
            $table->date('tgl_selesai_magang')->nullable();
        });
    }
};
