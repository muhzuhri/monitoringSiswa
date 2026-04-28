<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
$tables = ['siswa', 'guru', 'pembimbing', 'penilaian', 'penilaian_detail', 'laporan_akhir', 'logbook', 'absensi', 'pengajuan_siswa'];
foreach ($tables as $t) {
    try {
        echo "$t: " . DB::table($t)->count() . "\n";
    } catch (\Exception $e) {
        echo "$t: NOT FOUND\n";
    }
}
