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
        Schema::table('penilaian', function (Blueprint $table) {
            $table->integer('nilai_teknis')->nullable()->change();
            $table->integer('nilai_non_teknis')->nullable()->change();
            $table->integer('kedisiplinan')->nullable()->change();
            $table->integer('keterampilan')->nullable()->change();
            $table->integer('sikap')->nullable()->change();
            $table->integer('kerjasama')->nullable()->change();
            $table->integer('nilai_instansi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->integer('nilai_teknis')->nullable(false)->change();
            $table->integer('nilai_non_teknis')->nullable(false)->change();
            $table->integer('kedisiplinan')->nullable(false)->change();
            $table->integer('keterampilan')->nullable(false)->change();
            $table->integer('sikap')->nullable(false)->change();
            $table->integer('kerjasama')->nullable(false)->change();
            $table->integer('nilai_instansi')->nullable(false)->change();
        });
    }
};
