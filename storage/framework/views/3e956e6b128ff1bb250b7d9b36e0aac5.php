<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Individu - <?php echo e($user->nama); ?></title>
    <link rel="stylesheet" href="<?php echo e(public_path('assets/css/siswa/rekapAbsensiIndividu.css')); ?>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><?php echo e($konfigurasi->header_1 ?? 'REKAP ABSENSI SISWA'); ?></h2>
            <h3><?php echo e($konfigurasi->header_2 ?? 'PROGRAM PRAKTIK KERJA LAPANGAN (PKL)'); ?></h3>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%"><strong>Nama Siswa</strong></td>
                <td width="35%">: <?php echo e($user->nama); ?></td>
                <td width="15%"><strong>Sekolah</strong></td>
                <td>: <?php echo e($user->sekolah); ?></td>
            </tr>
            <tr>
                <td><strong>NISN</strong></td>
                <td>: <?php echo e($user->nisn); ?></td>
                <td><strong>Perusahaan</strong></td>
                <td>: <?php echo e($user->perusahaan); ?></td>
            </tr>
            <tr>
                <td><strong>Kelas/Jurusan</strong></td>
                <td>: <?php echo e($user->kelas); ?> / <?php echo e($user->jurusan); ?></td>
                <td><strong>Pembimbing</strong></td>
                <td>: <?php echo e($user->pembimbing->nama ?? '-'); ?></td>
            </tr>
             <tr>
                <td><strong>Dicetak Pada</strong></td>
                <td>: <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Tanggal</th>
                    <th width="35%">Presensi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $absen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-center"><?php echo e($index + 1); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y')); ?></td>
                        <td class="text-center">
                            <?php
                                $statusDisplay = $absen->status;
                                if ($statusDisplay === 'terlambat') {
                                    $statusDisplay = 'hadir';
                                }
                            ?>
                            <?php echo e(ucfirst($statusDisplay)); ?>

                        </td>
                        <td class="text-center">
                            <?php echo e(ucfirst($absen->verifikasi ?? 'pending')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data absensi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 20px;">
            <table class="data-table" style="width: 50%;">
                <thead>
                    <tr>
                        <th colspan="2">Ringkasan Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Hadir</td>
                        <td class="text-center"><?php echo e($rekapAbsensi['hadir']); ?></td>
                    </tr>
                    <tr>
                        <td>Izin</td>
                        <td class="text-center"><?php echo e($rekapAbsensi['izin']); ?></td>
                    </tr>
                    <tr>
                        <td>Sakit</td>
                        <td class="text-center"><?php echo e($rekapAbsensi['sakit']); ?></td>
                    </tr>
                    <tr>
                        <td>Alpa</td>
                        <td class="text-center"><?php echo e($rekapAbsensi['alpa']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <div class="footer-content">
                <p>Palembang, <?php echo e(\Carbon\Carbon::now()->translatedFormat('d F Y')); ?></p>
                <p>Mengetahui,</p>
                <br><br><br>
                <p><strong><?php echo e($user->pembimbing->nama ?? '....................................'); ?></strong></p>
            </div>
            <div class="clr-both"></div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/rekapAbsensiIndividu.blade.php ENDPATH**/ ?>