

<?php $__env->startSection('title', 'Rekap Absensi ' . $siswa->nama . ' - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dosen-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/absensisiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="dashboard-container mt-4 mb-5">

        
        <div class="page-header">
            <div class="header-content">
                <nav class="breadcrumb-container mb-2" aria-label="breadcrumb">
                    <a href="<?php echo e(route('pembimbing.siswa')); ?>" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h2 class="page-title"><i class="fas fa-calendar-check text-primary me-2"></i>Rekap Absensi: <?php echo e($siswa->nama); ?></h2>
                <p class="page-subtitle"><?php echo e($siswa->perusahaan); ?> &nbsp;|&nbsp; NISN: <?php echo e($siswa->nisn); ?></p>
            </div>

            <div class="header-actions">
                <div class="filter-group gap-2">
                    <span class="text-muted small fw-bold me-2">VERIFIKASI:</span>
                    <a href="<?php echo e(route('pembimbing.absensi', $siswa->nisn)); ?>" 
                       class="btn-filter-submit <?php echo e(!$statusVerifikasi ? '' : 'btn-outline'); ?>" style="<?php echo e(!$statusVerifikasi ? '' : 'background:transparent; color:var(--color-primary); border:1px solid var(--color-primary);'); ?>">Semua</a>
                    <a href="<?php echo e(route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'rejected'])); ?>" 
                       class="btn-filter-submit <?php echo e($statusVerifikasi == 'rejected' ? '' : 'btn-outline'); ?>" style="<?php echo e($statusVerifikasi == 'rejected' ? 'background:var(--color-warning);' : 'background:transparent; color:var(--color-warning); border:1px solid var(--color-warning);'); ?>">rejected</a>
                    <a href="<?php echo e(route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'verified'])); ?>" 
                       class="btn-filter-submit <?php echo e($statusVerifikasi == 'verified' ? '' : 'btn-outline'); ?>" style="<?php echo e($statusVerifikasi == 'verified' ? 'background:var(--color-green);' : 'background:transparent; color:var(--color-green); border:1px solid var(--color-green);'); ?>">Verified</a>
                    
                    <?php if($absensis->where('verifikasi', 'pending')->count() > 0): ?>
                        <form action="<?php echo e(route('pembimbing.absensi.validasi-semua', $siswa->nisn)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA absensi pending untuk siswa ini?')">
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

        <?php if(session('info')): ?>
            <div class="custom-alert alert-info-soft mb-4" style="background:var(--color-blue-lt); color:var(--color-blue); border-left:4px solid var(--color-blue); padding:1rem 1.25rem; border-radius:var(--radius-sm); display:flex; align-items:flex-start; gap:.9rem;">
                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                <div class="alert-content"><?php echo e(session('info')); ?></div>
            </div>
        <?php endif; ?>
        
        
        <div class="content-card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
                <h4 class="card-title"><i class="fas fa-history text-primary me-2"></i>Riwayat Presensi</h4>
            </div>
            <div class="custom-table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="20%">Tanggal</th>
                            <th width="15%">Status</th>
                            <th width="15%">Jam Masuk</th>
                            <th width="15%">Jam Pulang</th>
                            <th width="15%">Bukti Foto</th>
                            <th width="20%" class="text-center">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="date-badge">
                                        <span class="day"><?php echo e(\Carbon\Carbon::parse($a->tanggal)->format('d')); ?></span>
                                        <div class="month-year">
                                            <span class="month"><?php echo e(\Carbon\Carbon::parse($a->tanggal)->translatedFormat('M')); ?></span>
                                            <span class="year"><?php echo e(\Carbon\Carbon::parse($a->tanggal)->format('Y')); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($a->status == 'hadir'): ?>
                                        <span class="status-badge status-approved">Hadir</span>
                                    <?php elseif($a->status == 'terlambat'): ?>
                                        <span class="status-badge status-pending" style="background:var(--color-orange-lt); color:var(--color-orange);">Terlambat</span>
                                    <?php elseif($a->status == 'izin' || $a->status == 'sakit'): ?>
                                        <span class="status-badge status-pending"><?php echo e(ucfirst($a->status)); ?></span>
                                    <?php else: ?>
                                        <span class="status-badge status-rejected">Alpa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-'); ?></td>
                                <td><?php echo e($a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-'); ?></td>
                                 <td>
                                    <div class="action-buttons">
                                        <?php if($a->foto_masuk): ?>
                                            <a href="<?php echo e(asset('storage/' . $a->foto_masuk)); ?>" target="_blank" class="attachment-badge" title="Foto Masuk">
                                                <i class="fas fa-image"></i> Masuk
                                            </a>
                                        <?php endif; ?>
                                        <?php if($a->foto_pulang): ?>
                                            <a href="<?php echo e(asset('storage/' . $a->foto_pulang)); ?>" target="_blank" class="attachment-badge" title="Foto Pulang">
                                                <i class="fas fa-image"></i> Pulang
                                            </a>
                                        <?php endif; ?>
                                        <?php if(!$a->foto_masuk && !$a->foto_pulang): ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if($a->verifikasi == 'verified'): ?>
                                        <span class="status-badge status-approved cursor-pointer btn-open-modal" data-modal="modalVer<?php echo e($a->id_absensi); ?>">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    <?php elseif($a->verifikasi == 'rejected'): ?>
                                        <span class="status-badge status-rejected cursor-pointer btn-open-modal" data-modal="modalVer<?php echo e($a->id_absensi); ?>">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    <?php else: ?>
                                        <button class="btn-action-icon btn-review btn-open-modal" data-modal="modalVer<?php echo e($a->id_absensi); ?>">
                                            <i class="fas fa-clock"></i> Verifikasi
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times empty-icon"></i>
                                        <h4>Tidak Ada Data</h4>
                                        <p class="text-muted">Belum ada riwayat presensi yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>

        
        <?php $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $isDynamic = isset($a->is_dynamic) && $a->is_dynamic;
            ?>
            <div class="custom-modal-overlay" id="modalVer<?php echo e($a->id_absensi); ?>">
                <div class="custom-modal modal-sm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-calendar-check text-primary"></i> Verifikasi Absensi</h5>
                        <button type="button" class="modal-close btn-close-modal" data-modal="modalVer<?php echo e($a->id_absensi); ?>">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="<?php echo e(route('pembimbing.absensi.validasi', $a->id_absensi)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="is_dynamic" value="<?php echo e($isDynamic ? 1 : 0); ?>">
                        <input type="hidden" name="siswa_nisn" value="<?php echo e($siswa->nisn); ?>">
                        <div class="modal-body">
                            <div class="detail-section mb-4">
                                <div class="detail-content" style="background:var(--color-primary-lt); border-color:var(--color-primary); display:flex; align-items:center; gap:1rem;">
                                    <i class="fas fa-user-clock fs-3 text-primary"></i>
                                    <div>
                                        <div class="small fw-bold"><?php echo e(\Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y')); ?></div>
                                        <div class="small text-muted">Status: <?php echo e(ucfirst($a->status)); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="detail-label">Keputusan Verifikasi</label>
                                <div class="validation-radios">
                                    <label class="radio-card btn-radio-approved">
                                        <input type="radio" name="status" value="verified" <?php echo e($a->verifikasi == 'verified' ? 'checked' : ''); ?> required>
                                        <div class="radio-content">
                                            <i class="fas fa-check-circle"></i> SETUJUI
                                        </div>
                                    </label>
                                    <label class="radio-card btn-radio-rejected">
                                        <input type="radio" name="status" value="rejected" <?php echo e($a->verifikasi == 'rejected' ? 'checked' : ''); ?>>
                                        <div class="radio-content">
                                            <i class="fas fa-times-circle"></i> TOLAK
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="keterangan" class="custom-textarea" rows="3" placeholder="Contoh: Bukti foto kurang jelas."><?php echo e($a->keterangan); ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel btn-close-modal" data-modal="modalVer<?php echo e($a->id_absensi); ?>">Batal</button>
                            <button type="submit" class="btn-submit">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/pembimbing/absensiSiswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/absensiSiswa.blade.php ENDPATH**/ ?>