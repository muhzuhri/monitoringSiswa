

<?php $__env->startSection('title', 'Logbook ' . $siswa->nama . ' - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/logbookSiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="logbook-wrapper">
        <!-- Header -->
        <header class="header-section">
            <div>
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('guru.siswa')); ?>">Daftar Siswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logbook Siswa</li>
                    </ul>
                </nav>
                <h1 class="title-main">Logbook: <?php echo e($siswa->nama); ?></h1>
                <p class="subtitle-main"><?php echo e($siswa->perusahaan); ?> | <?php echo e($siswa->nisn); ?></p>
            </div>
            
            <div class="filter-container">
                <span class="filter-label">FILTER STATUS:</span>
                <a href="<?php echo e(route('guru.logbook', $siswa->nisn)); ?>" 
                   class="filter-btn <?php echo e(!$status ? 'filter-btn-primary' : 'filter-btn-light'); ?>">Semua</a>
                <a href="<?php echo e(route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending'])); ?>" 
                   class="filter-btn <?php echo e($status == 'pending' ? 'filter-btn-warning' : 'filter-btn-light'); ?>">Pending</a>
                <a href="<?php echo e(route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified'])); ?>" 
                   class="filter-btn <?php echo e($status == 'verified' ? 'filter-btn-success' : 'filter-btn-light'); ?>">Approved</a>
                <a href="<?php echo e(route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'rejected'])); ?>" 
                   class="filter-btn <?php echo e($status == 'rejected' ? 'filter-btn-danger' : 'filter-btn-light'); ?>">Rejected</a>
            </div>
        </header>

        <?php if(session('success')): ?>
            <div class="ui-alert ui-alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <!-- Logbook List -->
        <div class="ui-card mt-4">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="ps-4" width="25%"><i class="far fa-calendar-alt me-1"></i> Tanggal</th>
                            <th width="55%"><i class="fas fa-tasks me-1"></i> Kegiatan / Pekerjaan</th>
                            <th width="20%" class="text-center"><i class="fas fa-info-circle me-1"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?php echo e(\Carbon\Carbon::parse($log->tanggal)->translatedFormat('l')); ?></div>
                                    <div class="small text-muted"><?php echo e(\Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y')); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <?php if($log->foto): ?>
                                            <a href="<?php echo e(asset('storage/' . $log->foto)); ?>" target="_blank" class="flex-shrink-0" style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; display: block; border: 1px solid #eee;" title="Lihat Lampiran">
                                                <img src="<?php echo e(asset('storage/' . $log->foto)); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                            </a>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <div class="kegiatan-preview mb-1"><?php echo e($log->kegiatan); ?></div>
                                            <?php if($log->catatan_pembimbing): ?>
                                                <div class="catatan-badge small <?php echo e($log->status == 'rejected' ? 'text-danger' : 'text-success'); ?>" style="font-style: italic;">
                                                    <i class="fas fa-comment-dots me-1"></i> "<?php echo e($log->catatan_pembimbing); ?>"
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if($log->status == 'verified'): ?>
                                        <span class="status-badge status-approved small">Verified</span>
                                    <?php elseif($log->status == 'rejected'): ?>
                                        <span class="status-badge status-rejected small">Rejected</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending small">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="empty-state text-center p-5">
                                    <div class="empty-icon-box mb-3 opacity-25">
                                        <i class="fas fa-book-open fa-3x"></i>
                                    </div>
                                    <h2 class="empty-title h5">Belum ada Logbook</h2>
                                    <p class="empty-desc text-muted small">Siswa belum mengunggah kegiatan untuk kategori ini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/logbookSiswa.blade.php ENDPATH**/ ?>