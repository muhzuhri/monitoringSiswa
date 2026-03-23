<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ubah auto_increment dan tipe data id menjadi varchar(50)
        DB::statement('ALTER TABLE dosen MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE dosen MODIFY id VARCHAR(50) NOT NULL');
    }

    public function down(): void
    {
        // Revert ke auto_increment (agak sulit dikembalikan tepat murni, tapi ini best effort)
        DB::statement('ALTER TABLE dosen MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE dosen MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
    }
};
