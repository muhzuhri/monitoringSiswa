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
        Schema::table('kriteria_penilaian', function (Blueprint $table) {
            $table->string('id_guru')->nullable()->after('id_pembimbing');
            $table->foreign('id_guru')->references('id_guru')->on('guru')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriteria_penilaian', function (Blueprint $table) {
            $table->dropForeign(['id_guru']);
            $table->dropColumn('id_guru');
        });
    }
};
