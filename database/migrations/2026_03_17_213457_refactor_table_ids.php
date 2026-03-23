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
        // 1. Rename 'admin' column
        Schema::table('admin', function (Blueprint $table) {
            $table->renameColumn('id', 'id_admin');
            $table->renameColumn('instansi', 'role');
            $table->renameColumn('name', 'nama');
        });

        // 2. Rename 'guru' column
        Schema::table('guru', function (Blueprint $table) {
            $table->renameColumn('nip', 'id_guru');
            $table->renameColumn('tahun_ajaran_id', 'id_tahun_ajaran');
            $table->renameColumn('name', 'nama');
        });

        // 3. Rename columns in 'siswa'
        Schema::table('siswa', function (Blueprint $table) {
            $table->renameColumn('guru_nip', 'id_guru');
            $table->renameColumn('pembimbing_lapangan_nip', 'id_pembimbing');
            $table->renameColumn('tahun_ajaran_id', 'id_tahun_ajaran');
            $table->renameColumn('magang_type', 'tipe_magang');
            $table->renameColumn('name', 'nama');
        });

        // 4. Rename 'dosen' table to 'pembimbing'
        Schema::rename('dosen', 'pembimbing');

        // 5. Rename column in newly renamed 'pembimbing' table
        Schema::table('pembimbing', function (Blueprint $table) {
            $table->renameColumn('id', 'id_pembimbing');
            $table->renameColumn('name', 'nama');
        });

        // 6. Rename 'absensi' column
        Schema::table('absensi', function (Blueprint $table) {
            $table->renameColumn('id', 'id_absensi');
            $table->renameColumn('siswa_nisn', 'nisn');
        });
        
        // 7. Rename 'laporan_akhirs' column
        Schema::table('laporan_akhirs', function (Blueprint $table) {
            $table->renameColumn('id', 'id_laporan');
            $table->renameColumn('file_path', 'file');
            $table->renameColumn('siswa_nisn', 'nisn');
        });

        Schema::table('laporan_akhirs', function (Blueprint $table) {
            $table->dropColumn('versi');
            $table->dropColumn('keterangan_revisi');
        });

        Schema::rename('laporan_akhirs', 'laporan');
        Schema::rename('penilaians', 'penilaian');
        Schema::rename('logbooks', 'kegiatan');

        // 8. Rename 'tahun_ajaran' column
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->renameColumn('id_tahunAjaran', 'id_tahun_ajaran');
        });

        // 9. Drop pengajuan_gurus table
        Schema::dropIfExists('pengajuan_gurus');

        
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    // 1. Rename tabel kembali dulu
    Schema::rename('laporan', 'laporan_akhirs');
    Schema::rename('penilaian', 'penilaians');
    Schema::rename('kegiatan', 'logbooks');
    Schema::rename('pembimbing', 'dosen');

    // 2. Restore kolom laporan_akhirs
    Schema::table('laporan_akhirs', function (Blueprint $table) {
        $table->renameColumn('id_laporan', 'id');
        $table->renameColumn('file', 'file_path');
        $table->renameColumn('nisn', 'siswa_nisn');

        $table->string('versi')->nullable();
        $table->string('keterangan_revisi')->nullable();
    });

    // 3. Restore absensi
    Schema::table('absensi', function (Blueprint $table) {
        $table->renameColumn('nisn', 'siswa_nisn');
        $table->renameColumn('id_absensi', 'id');
    });

    // 4. Restore dosen (sebelumnya pembimbing)
    Schema::table('dosen', function (Blueprint $table) {
        $table->renameColumn('nama', 'name');
        $table->renameColumn('id_pembimbing', 'id');
    });

    // 5. Restore siswa
    Schema::table('siswa', function (Blueprint $table) {
        $table->renameColumn('nama', 'name');
        $table->renameColumn('tipe_magang', 'magang_type');
        $table->renameColumn('id_tahun_ajaran', 'tahun_ajaran_id');
        $table->renameColumn('id_pembimbing', 'pembimbing_lapangan_nip');
        $table->renameColumn('id_guru', 'guru_nip');
    });

    // 6. Restore guru
    Schema::table('guru', function (Blueprint $table) {
        $table->renameColumn('nama', 'name');
        $table->renameColumn('id_tahun_ajaran', 'tahun_ajaran_id');
        $table->renameColumn('id_guru', 'nip');
    });

    // 7. Restore admin
    Schema::table('admin', function (Blueprint $table) {
        $table->renameColumn('nama', 'name');
        $table->renameColumn('role', 'instansi');
        $table->renameColumn('id_admin', 'id');
    });

    // 8. Restore tahun_ajaran
    Schema::table('tahun_ajaran', function (Blueprint $table) {
        $table->renameColumn('id_tahun_ajaran', 'id_tahunAjaran');
    });

}
};
