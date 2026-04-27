<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasi_dashboard', function (Blueprint $table) {
            $table->id();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();           // Simpan sebagai JSON array string
            $table->text('sejarah')->nullable();
            $table->string('jam_operasional')->nullable()->default('Senin – Jumat: 08.00 – 16.00 WIB');
            $table->text('deskripsi_jam_operasional')->nullable();
            $table->string('alamat_lokasi')->nullable();
            $table->string('link_maps')->nullable()->default('https://maps.google.com/?q=Fasilkom+Unsri');
            $table->string('email_kontak')->nullable();
            $table->string('telp_kontak')->nullable();
            $table->string('website_kontak')->nullable();
            $table->string('nama_fakultas')->nullable()->default('Fakultas Ilmu Komputer');
            $table->text('deskripsi_banner')->nullable();
            $table->timestamps();
        });

        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jenjang', 10);  // S1, D3, S2, dll
            $table->string('warna_dot', 20)->nullable(); // warna CSS
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_studi');
        Schema::dropIfExists('informasi_dashboard');
    }
};
