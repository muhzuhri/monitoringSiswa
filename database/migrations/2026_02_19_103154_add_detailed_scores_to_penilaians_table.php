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
        Schema::table('penilaians', function (Blueprint $table) {
            $table->integer('kedisiplinan')->nullable()->after('kategori');
            $table->integer('keterampilan')->nullable()->after('kedisiplinan');
            $table->integer('sikap')->nullable()->after('keterampilan');
            $table->integer('kerjasama')->nullable()->after('sikap');
            $table->integer('nilai_instansi')->nullable()->after('kerjasama');
            $table->text('saran')->nullable()->after('nilai_instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penilaians', function (Blueprint $table) {
            //
        });
    }
};
