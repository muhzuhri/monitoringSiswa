

<?php $__env->startSection('title', 'Logbook ' . $siswa->nama . ' - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dosen-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/style-dosen.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/absensiKegiatan.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="dashboard-container mt-4 mb-5">
        
        <div class="page-header">
            <div class="header-content">
                <nav class="breadcrumb-container mb-2" aria-label="breadcrumb">
                    <a href="<?php echo e(route('pembimbing.siswa')); ?>" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Logbook Siswa</span>
                </nav>
                <h2 class="page-title"><i class="fas fa-book-open text-primary me-2"></i>Logbook: <?php echo e($siswa->nama); ?></h2>
                <p class="page-subtitle"><?php echo e($siswa->perusahaan); ?> &nbsp;|&nbsp; NISN: <?php echo e($siswa->nisn); ?></p>
            </div>

            <div class="header-actions">
                <div class="filter-group gap-2">
                    <span class="text-muted small fw-bold me-2">FILTER STATUS:</span>
                    <a href="<?php echo e(route('pembimbing.logbook', $siswa->nisn)); ?>" 
                       class="btn-filter-submit <?php echo e(!$status ? '' : 'btn-outline'); ?>" <?php if($status): ?> style="background:transparent; color:var(--color-primary); border:1px solid var(--color-primary);" <?php endif; ?>>Semua</a>
                    <a href="<?php echo e(route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending'])); ?>" 
                       class="btn-filter-submit <?php echo e($status == 'pending' ? '' : 'btn-outline'); ?>" <?php if($status == 'pending'): ?> style="background:var(--color-warning);" <?php else: ?> style="background:transparent; color:var(--color-warning); border:1px solid var(--color-warning);" <?php endif; ?>>Pending</a>
                    <a href="<?php echo e(route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified'])); ?>" 
                       class="btn-filter-submit <?php echo e($status == 'verified' ? '' : 'btn-outline'); ?>" <?php if($status == 'verified'): ?> style="background:var(--color-green);" <?php else: ?> style="background:transparent; color:var(--color-green); border:1px solid var(--color-green);" <?php endif; ?>>Approved</a>
                    
                    <?php if($logbooks->where('status', 'pending')->count() > 0): ?>
                        <form action="<?php echo e(route('pembimbing.logbook.validasi-semua', $siswa->nisn)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA logbook pending untuk siswa ini?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-filter-submit" style="background: var(--color-green);">
                                <i class="fas fa-check-double me-1"></i> Setujui Semua
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="custom-alert alert-success-soft mb-4">
                <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                <div class="alert-content"><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?>

        
        <div class="content-card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
                <h4 class="card-title"><i class="fas fa-list-alt text-primary me-2"></i>Daftar Kegiatan Siswa</h4>
            </div>
            <div class="custom-table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="20%">Tanggal</th>
                            <th width="40%">Kegiatan / Pekerjaan</th>
                            <th width="15%">Bukti</th>
                            <th width="10%">Status</th>
                            <th width="15%" class="text-center">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="date-badge">
                                        <span class="day"><?php echo e(\Carbon\Carbon::parse($log->tanggal)->format('d')); ?></span>
                                        <div class="month-year">
                                            <span class="month"><?php echo e(\Carbon\Carbon::parse($log->tanggal)->translatedFormat('M')); ?></span>
                                            <span class="year"><?php echo e(\Carbon\Carbon::parse($log->tanggal)->format('Y')); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="activity-excerpt">
                                        <div class="fw-bold text-dark mb-1"><?php echo e($log->kegiatan); ?></div>
                                        <?php if($log->catatan_pembimbing): ?>
                                            <div class="note-bubble mt-2 <?php echo e($log->status == 'rejected' ? 'alert-danger-soft' : ''); ?>" 
                                                <?php if($log->status == 'rejected'): ?>
                                                    style="padding: 0.5rem 0.75rem; font-size: 0.8rem; background:var(--color-red-lt); border-left:3px solid var(--color-red);"
                                                <?php else: ?>
                                                    style="padding: 0.5rem 0.75rem; font-size: 0.8rem; background:var(--color-green-lt); border-left:3px solid var(--color-green);"
                                                <?php endif; ?>>
                                                <i class="fas fa-comment-dots me-1"></i> <?php echo e($log->catatan_pembimbing); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($log->foto): ?>
                                        <a href="<?php echo e(asset('storage/' . $log->foto)); ?>" target="_blank" class="attachment-badge">
                                            <i class="fas fa-image"></i> Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($log->status == 'verified'): ?>
                                        <span class="status-badge status-approved">Approved</span>
                                    <?php elseif($log->status == 'rejected'): ?>
                                        <span class="status-badge status-rejected">Rejected</span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($log->status == 'verified'): ?>
                                        <span class="status-badge status-approved cursor-pointer btn-open-modal" data-modal="modalLog<?php echo e($log->id_kegiatan); ?>">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    <?php elseif($log->status == 'rejected'): ?>
                                        <span class="status-badge status-rejected cursor-pointer btn-open-modal" data-modal="modalLog<?php echo e($log->id_kegiatan); ?>">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    <?php else: ?>
                                        <button class="btn-action-icon btn-review btn-open-modal" data-modal="modalLog<?php echo e($log->id_kegiatan); ?>">
                                            <i class="fas fa-clock"></i> Verifikasi
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-book-open empty-icon"></i>
                                        <h4>Belum Ada Kegiatan</h4>
                                        <p class="text-muted">Siswa belum mengisi logbook atau tidak ada data yang sesuai.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($logbooks->hasPages()): ?>
                <div class="pagination-wrapper">
                    <?php echo e($logbooks->links()); ?>

                </div>
            <?php endif; ?>
        </div>

        
        <?php $__currentLoopData = $logbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="custom-modal-overlay" id="modalLog<?php echo e($log->id_kegiatan); ?>">
                <div class="custom-modal modal-sm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clipboard-check text-primary"></i> Verifikasi Logbook</h5>
                        <button type="button" class="modal-close btn-close-modal" data-modal="modalLog<?php echo e($log->id_kegiatan); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="<?php echo e(route('pembimbing.logbook.validasi', $log->id_kegiatan)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <div class="detail-section mb-4">
                                <label class="detail-label">Kegiatan Siswa</label>
                                <div class="detail-content">
                                    <?php echo e($log->kegiatan); ?>

                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="detail-label">Keputusan Verifikasi</label>
                                <div class="validation-radios">
                                    <label class="radio-card btn-radio-approved">
                                        <input type="radio" name="status" value="verified" <?php echo e($log->status == 'verified' ? 'checked' : ''); ?> required>
                                        <div class="radio-content">
                                            <i class="fas fa-check-circle"></i> SETUJUI
                                        </div>
                                    </label>
                                    <label class="radio-card btn-radio-rejected">
                                        <input type="radio" name="status" value="rejected" <?php echo e($log->status == 'rejected' ? 'checked' : ''); ?>>
                                        <div class="radio-content">
                                            <i class="fas fa-times-circle"></i> TOLAK
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan / Feedback (Opsional)</label>
                                <textarea name="catatan_pembimbing" class="custom-textarea" rows="3" placeholder="Contoh: Deskripsi sudah bagus."><?php echo e($log->catatan_pembimbing); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel btn-close-modal" data-modal="modalLog<?php echo e($log->id_kegiatan); ?>">Batal</button>
                            <button type="submit" class="btn-submit">Simpan Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Logic
            const openBtns = document.querySelectorAll('.btn-open-modal');
            const closeBtns = document.querySelectorAll('.btn-close-modal');
            const overlays = document.querySelectorAll('.custom-modal-overlay');

            openBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            closeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });

            overlays.forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/logbookSiswa.blade.php ENDPATH**/ ?>