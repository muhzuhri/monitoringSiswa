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
            $table->string('id_pembimbing')->nullable()->after('id_kriteria');
            $table->foreign('id_pembimbing')->references('id_pembimbing')->on('pembimbing')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriteria_penilaian', function (Blueprint $table) {
            $table->dropForeign(['id_pembimbing']);
            $table->dropColumn('id_pembimbing');
        });
    }
};
