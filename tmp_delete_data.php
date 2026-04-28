<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dbName = DB::getDatabaseName();
$tablesInDb = array_map('current', DB::select('SHOW TABLES'));
echo "Tables in database $dbName: " . implode(', ', $tablesInDb) . "\n\n";

$targetTables = [
    'penilaian_detail',
    'penilaian',
    'laporan',
    'kegiatan',
    'absensi',
    'pengajuan_siswas',
    'siswa',
    'guru',
    'pembimbing'
];

echo "Checking counts for target tables:\n";
foreach ($targetTables as $table) {
    if (in_array($table, $tablesInDb)) {
        $count = DB::table($table)->count();
        echo "Table $table: $count records\n";
    } else {
        echo "Table $table: NOT FOUND in database\n";
    }
}

echo "\nStarting deletion...\n";
Schema::disableForeignKeyConstraints();

foreach ($targetTables as $table) {
    if (in_array($table, $tablesInDb)) {
        DB::table($table)->truncate();
        echo "Truncated $table\n";
    }
}

Schema::enableForeignKeyConstraints();
echo "\nDone!\n";
