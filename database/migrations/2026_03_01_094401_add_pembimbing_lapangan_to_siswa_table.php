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
            $table->string('pembimbing_lapangan_nama')->nullable()->after('guru_nip');
            $table->string('pembimbing_lapangan_nip', 50)->nullable()->after('pembimbing_lapangan_nama');
            $table->string('pembimbing_lapangan_no_hp', 20)->nullable()->after('pembimbing_lapangan_nip');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn(['pembimbing_lapangan_nama', 'pembimbing_lapangan_nip', 'pembimbing_lapangan_no_hp']);
        });
    }
};
