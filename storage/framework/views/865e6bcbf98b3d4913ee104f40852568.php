

<?php $__env->startSection('title', 'Rekap Absensi ' . $siswa->nama . ' - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/absensiSiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">

        
        <div class="page-header">
            <div class="header-text">
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <a href="<?php echo e(route('guru.siswa')); ?>" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h4 class="page-title">Rekap Absensi: <?php echo e($siswa->nama); ?></h4>
                <p class="page-subtitle"><i class="fas fa-building me-1"></i> <?php echo e($siswa->perusahaan); ?> &nbsp;|&nbsp; <i class="fas fa-id-card me-1"></i> NISN: <?php echo e($siswa->nisn); ?></p>
            </div>
            
        </div>

        
        <div class="stat-grid">
            <div class="stat-card stat-hadir">
                <h6 class="stat-label">Hadir</h6>
                <div class="stat-number"><?php echo e($rekap['hadir']); ?></div>
            </div>
            <div class="stat-card stat-izin">
                <h6 class="stat-label">Izin / Sakit</h6>
                <div class="stat-number"><?php echo e($rekap['izin'] + $rekap['sakit']); ?></div>
            </div>
            <div class="stat-card stat-alpa">
                <h6 class="stat-label">Alpa</h6>
                <div class="stat-number"><?php echo e($rekap['alpa']); ?></div>
            </div>
            <div class="stat-card stat-total">
                <h6 class="stat-label">Total Hari</h6>
                <div class="stat-number"><?php echo e($rekap['total']); ?></div>
            </div>
        </div>

        
        <div class="ui-card">
            <div class="card-head">
                <h6 class="card-title">Riwayat Presensi</h6>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-day me-1"></i> Hari, Tanggal</th>
                            <th><i class="fas fa-info-circle me-1"></i> Status</th>
                            <th class="text-center"><i class="fas fa-clock me-1"></i> Masuk</th>
                            <th class="text-center"><i class="fas fa-clock me-1"></i> Pulang</th>
                            <th class="text-end pe-4"><i class="fas fa-camera me-1"></i> Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="td-date">
                                    <?php echo e(\Carbon\Carbon::parse($a->tanggal)->translatedFormat('l, d F Y')); ?>

                                </td>
                                <td>
                                    <?php if($a->status == 'hadir'): ?>
                                        <span class="status-badge status-hadir">Hadir</span>
                                    <?php elseif($a->status == 'terlambat'): ?>
                                        <span class="status-badge status-terlambat">Terlambat</span>
                                    <?php elseif($a->status == 'izin' || $a->status == 'sakit'): ?>
                                        <span class="status-badge status-izin"><?php echo e(ucfirst($a->status)); ?></span>
                                    <?php else: ?>
                                        <span class="status-badge status-alpa">Alpa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center td-jam">
                                    <?php echo e($a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-'); ?>

                                </td>
                                <td class="text-center td-jam">
                                    <?php echo e($a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-'); ?>

                                </td>
                                <td class="text-end pe-4">
                                    <div class="photo-group justify-content-end">
                                        <?php if($a->foto_masuk): ?>
                                            <a href="<?php echo e(asset('storage/' . $a->foto_masuk)); ?>" target="_blank" title="Foto Masuk" class="photo-link">
                                                <img src="<?php echo e(asset('storage/' . $a->foto_masuk)); ?>" class="photo-thumbnail rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #eee;" alt="M">
                                            </a>
                                        <?php endif; ?>
                                        <?php if($a->foto_pulang): ?>
                                            <a href="<?php echo e(asset('storage/' . $a->foto_pulang)); ?>" target="_blank" title="Foto Pulang" class="photo-link">
                                                <img src="<?php echo e(asset('storage/' . $a->foto_pulang)); ?>" class="photo-thumbnail rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #eee;" alt="P">
                                            </a>
                                        <?php endif; ?>
                                        <?php if(!$a->foto_masuk && !$a->foto_pulang): ?>
                                            <span class="no-photo opacity-50 small">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="empty-row">Belum ada riwayat presensi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($absensis->hasPages()): ?>
                <div class="table-pagination">
                    <?php echo e($absensis->links()); ?>

                </div>
            <?php endif; ?>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/absensiSiswa.blade.php ENDPATH**/ ?>