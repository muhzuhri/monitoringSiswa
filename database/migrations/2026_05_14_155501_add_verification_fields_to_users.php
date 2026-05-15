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
            if (!Schema::hasColumn('siswa', 'tanda_pengenal')) {
                $table->string('tanda_pengenal')->nullable()->after('surat_balasan');
            }
            $table->string('status')->default('pending')->change();
        });

        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasColumn('guru', 'tanda_pengenal')) {
                $table->string('tanda_pengenal')->nullable()->after('npsn');
            }
            if (!Schema::hasColumn('guru', 'status')) {
                $table->string('status')->default('pending')->after('tanda_pengenal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('tanda_pengenal');
            $table->string('status')->default('aktif')->change();
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn(['tanda_pengenal', 'status']);
        });
    }
};
