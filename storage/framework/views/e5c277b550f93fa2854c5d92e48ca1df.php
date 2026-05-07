<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Berkelompok - <?php echo e($user->nama); ?></title>
    <link rel="stylesheet" href="<?php echo e(public_path('assets/css/siswa/rekapAbsensiKelompok.css')); ?>">
</head>
<body>
<?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $month = $monthData['month'];
        $year = $monthData['year'];
        $anggota = $monthData['anggota'];
        $daysInMonth = $monthData['daysInMonth'];
        $monthName = $monthData['monthName'];
    ?>
    <div class="container <?php echo e(!$loop->last ? 'page-break' : ''); ?>">
        <div class="header">
            <h3><?php echo e($konfigurasi->header_1 ?? 'ABSENSI SISWA MAGANG / PRAKERIN'); ?></h3>
            <h3><?php echo e($konfigurasi->header_2 ?? 'PROGRAM STUDI KEAHLIAN TEKNIK JARINGAN KOMPUTER DAN TELEKOMUNIKASI'); ?></h3>
            <?php if(isset($konfigurasi->header_3)): ?>
                <h2><?php echo e(str_replace('{sekolah}', $user->sekolah, $konfigurasi->header_3)); ?></h2>
            <?php endif; ?>
            <?php if(isset($konfigurasi->header_4)): ?>
                <p><?php echo e(str_replace('{tahun}', "$year/" . ($year + 1), $konfigurasi->header_4)); ?></p>
            <?php endif; ?>
        </div>

        <div class="info-section">
            <div class="info-left">
                <strong>Bulan : <?php echo e($monthName); ?> <?php echo e($year); ?></strong>
            </div>
            <div class="info-right">
                <strong>Nama Instansi / DUDI : <?php echo e($user->perusahaan); ?></strong>
            </div>
        </div>

        <div class="rekap-bulan-title">
            REKAP ABSENSI BULAN: <?php echo e($monthName); ?> <?php echo e($year); ?>

        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2">NO</th>
                    <th rowspan="2">NAMA SISWA</th>
                    <th rowspan="2">L/P</th>
                    <th rowspan="2">Kelas</th>
                    <th colspan="<?php echo e($daysInMonth); ?>">TANGGAL</th>
                </tr>
                <tr>
                    <?php for($i = 1; $i <= $daysInMonth; $i++): ?>
                        <th class="day-cell"><?php echo e($i); ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $sis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="text-center"><?php echo e($idx + 1); ?></td>
                        <td class="name-cell"><?php echo e($sis->nama); ?></td>
                        <td class="text-center"><?php echo e($sis->jenis_kelamin); ?></td>
                        <td class="text-center"><?php echo e($sis->kelas); ?></td>
                        <?php for($d = 1; $d <= $daysInMonth; $d++): ?>
                            <?php
                                $date = \Carbon\Carbon::createFromDate($year, $month, $d);
                                $isWeekend = $date->isWeekend();
                                $absen = $sis->absensis->firstWhere('tanggal', $date->toDateString());
                                $statusChar = '';
                                $class = $isWeekend ? 'status-holiday' : '';

                                if ($absen) {
                                    $statusMapping = [
                                        'hadir' => 'H',
                                        'terlambat' => 'H',
                                        'sakit' => 'S',
                                        'izin' => 'I',
                                        'alpa' => 'A'
                                    ];
                                    $statusChar = $statusMapping[$absen->status] ?? strtoupper(substr($absen->status, 0, 1));
                                    
                                    if (!$isWeekend) {
                                        $class = 'status-' . strtolower(substr($absen->status, 0, 1));
                                    }
                                } elseif (!$isWeekend && $date->lte(\Carbon\Carbon::today())) {
                                    // CHECK: Internship start date
                                    $internStart = $sis->tgl_mulai_magang ? \Carbon\Carbon::parse($sis->tgl_mulai_magang)->startOfDay() : null;
                                    
                                    if ($internStart && $date->gte($internStart) && $date->lt(\Carbon\Carbon::today())) {
                                        $statusChar = 'A';
                                        $class = 'status-a';
                                    }
                                }
                            ?>
                            <td class="day-cell <?php echo e($class); ?>"><?php echo e($statusChar); ?></td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="footer">
            <div class="footer-content">
                <p>Palembang, <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></p>
                <p>Pembimbing Lapangan</p>
                <br><br><br><br>
                <p><strong><?php echo e($user->pembimbing->nama ?? '....................................................................'); ?></strong></p>
            </div>
            <div class="clr-both"></div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/rekapAbsensiKelompok.blade.php ENDPATH**/ ?>