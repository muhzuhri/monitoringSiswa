<?php $__env->startSection('title', 'Persetujuan Pengajuan Siswa - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'pengajuan-page pembimbing-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/pengajuanSiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
<div class="page-body">
    <div class="main-container">
        
        <div class="page-header animate-fade-in">
            <div class="header-content">
                <h3 class="header-title">Persetujuan Pengajuan<span class="dot-primary">.</span></h3>
                <p class="header-subtitle">Kelola request lupa absensi atau kegiatan dari siswa binaan Anda.</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="status-alert alert-success animate-fade-in" role="alert">
                <i class="fas fa-check-circle alert-icon"></i>
                <span class="alert-message"><?php echo e(session('success')); ?></span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="status-alert alert-danger animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <span class="alert-message"><?php echo e(session('error')); ?></span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="filter-card animate-fade-in">
            <div class="filter-body">
                <form action="<?php echo e(route('pembimbing.pengajuan')); ?>" method="GET" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Filter Status</label>
                        <select name="status" class="premium-select filter-glow" onchange="this.form.submit()">
                            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Menunggu Persetujuan</option>
                            <option value="valid" <?php echo e(request('status') == 'valid' ? 'selected' : ''); ?>>Disetujui</option>
                            <option value="ditolak" <?php echo e(request('status') == 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
                            <option value="semua" <?php echo e(request('status') == 'semua' ? 'selected' : ''); ?>>Semua Pengajuan</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-card animate-fade-in">
            <div class="table-header">
                <div class="header-title-wrapper">
                    <div class="icon-circle-premium">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="card-title-premium">Daftar Pengajuan</h5>
                </div>
            </div>
            <div class="table-body">
                <?php if($pengajuans->isEmpty()): ?>
                    <div class="empty-state">
                        <div class="empty-icon-wrapper">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h6 class="empty-title">Tidak ada data</h6>
                        <p class="empty-subtitle">Belum ada pengajuan dengan filter yang dipilih.</p>
                    </div>
                <?php else: ?>
                    <div class="scrollable-table">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th class="col-student">Siswa & Waktu</th>
                                    <th class="col-info">Informasi Lupa</th>
                                    <th class="col-reason">Alasan Keterlambatan</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-action">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $pengajuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="table-row">
                                        <!-- Siswa & Waktu -->
                                        <td class="cell-student">
                                            <div class="student-profile">
                                                <div class="avatar-wrapper">
                                                    <?php echo e(strtoupper(substr($p->siswa->nama, 0, 1))); ?>

                                                </div>
                                                <div class="student-info">
                                                    <div class="student-name"><?php echo e($p->siswa->nama); ?></div>
                                                    <div class="student-nisn"><?php echo e($p->siswa->nisn); ?></div>
                                                </div>
                                            </div>
                                            <div class="timestamp-info">
                                                <i class="fas fa-clock"></i> Diajukan: <?php echo e($p->created_at->translatedFormat('d M, H:i')); ?>

                                            </div>
                                        </td>

                                        <!-- Informasi Lupa -->
                                        <td class="cell-info">
                                            <div class="lupa-summary">
                                                <div class="main-date"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y')); ?></div>
                                                <span class="badge-jenis badge-<?php echo e($p->jenis); ?>">
                                                    <?php echo e(ucfirst($p->jenis)); ?>

                                                </span>
                                            </div>
                                            <?php if($p->jenis == 'absensi'): ?>
                                                <div class="time-mini-card">
                                                    <div class="time-mini-item border-right-white">
                                                        <div class="time-mini-label">MASUK</div>
                                                        <div class="time-mini-value"><?php echo e($p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '--:--'); ?></div>
                                                    </div>
                                                    <div class="time-mini-item">
                                                        <div class="time-mini-label">PULANG</div>
                                                        <div class="time-mini-value"><?php echo e($p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '--:--'); ?></div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="detail-text" title="<?php echo e($p->deskripsi); ?>">
                                                    "<?php echo e($p->deskripsi); ?>"
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if($p->bukti): ?>
                                                <a href="<?php echo e(asset('storage/'.$p->bukti)); ?>" target="_blank" class="attachment-link">
                                                    <i class="fas fa-paperclip"></i> Lihat Bukti
                                                </a>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Alasan -->
                                        <td class="cell-reason">
                                            <div class="reason-box">
                                                <?php echo e($p->alasan_terlambat); ?>

                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="cell-status">
                                            <?php if($p->status == 'pending'): ?>
                                                <div class="status-pill state-pending">
                                                    <i class="fas fa-hourglass-half"></i>Pending
                                                </div>
                                            <?php elseif($p->status == 'valid'): ?>
                                                <div class="status-pill state-valid">
                                                    <i class="fas fa-check-circle"></i>Valid
                                                </div>
                                            <?php else: ?>
                                                <div class="status-pill state-rejected">
                                                    <i class="fas fa-times-circle"></i>Ditolak
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="cell-action">
                                            <?php if($p->status == 'pending'): ?>
                                                <div class="action-group">
                                                    <form action="<?php echo e(route('pembimbing.pengajuan.update', $p->id_pengajuan)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn-icon-success" onclick="return confirm('Setujui pengajuan ini? Data akan otomatis masuk ke sistem.')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="<?php echo e(route('pembimbing.pengajuan.update', $p->id_pengajuan)); ?>" method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn-icon-danger" onclick="return confirm('Tolak pengajuan ini?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-completed">Selesai</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="table-footer">
                <?php if(method_exists($pengajuans, 'links')): ?>
                    <?php echo e($pengajuans->links()); ?>

                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/pengajuanSiswa.blade.php ENDPATH**/ ?>