<?php $__env->startSection('title', 'Pengajuan Lupa Absensi / Kegiatan - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'pengajuan-page siswa-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/siswa/pengajuan.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
<div class="page-body">
    <div class="main-container">
        
        <div class="page-header animate-fade-in">
            <div class="header-content">
                <h3 class="header-title">Pengajuan Lupa Isi</h3>
                <p class="header-subtitle">Laporkan absensi atau kegiatan yang terlewat untuk diverifikasi pembimbing.</p>
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


        <?php if($errors->any()): ?>
            <div class="status-alert alert-danger animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <div class="alert-message">
                    <div class="error-title">Terdapat Kesalahan!</div>
                    <ul class="error-list">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="content-layout">
            <!-- Form Pengajuan -->
            <div class="form-column">
                <div class="premium-card animate-fade-in">
                    <div class="card-header-premium">
                        <div class="header-title-wrapper">
                            <div class="icon-circle-premium">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h5 class="card-title-premium">Form Pengajuan</h5>
                        </div>
                    </div>
                    <div class="card-body-premium">
                        <?php if($isFinished): ?>
                            <div class="text-center py-5">
                                <div class="icon-circle-premium mb-4 mx-auto" style="width: 80px; height: 80px; font-size: 30px;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Fitur Dinonaktifkan</h5>
                                <p class="text-muted">Masa magang Anda telah berakhir. Anda tidak dapat melakukan pengajuan absensi atau kegiatan baru.</p>
                            </div>
                        <?php else: ?>
                            <form action="<?php echo e(route('siswa.pengajuan.store')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <div class="form-field">
                                <label class="field-label">Jenis Lupa <span class="required-mark">*</span></label>
                                <select class="custom-select" name="jenis" id="jenis_pengajuan" required>
                                    <option value="" disabled <?php echo e(old('jenis') == '' ? 'selected' : ''); ?>>-- Pilih Jenis --</option>
                                    <option value="absensi" <?php echo e(old('jenis') == 'absensi' ? 'selected' : ''); ?>>Lupa Absensi</option>
                                    <option value="kegiatan" <?php echo e(old('jenis') == 'kegiatan' ? 'selected' : ''); ?>>Lupa Logbook / Kegiatan</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Tanggal Lupa <span class="required-mark">*</span></label>
                                <input type="date" class="custom-input" name="tanggal" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>" max="<?php echo e(date('Y-m-d')); ?>" required>
                            </div>

                             <!-- Fields for Absensi -->
                             <div id="fields_absensi" class="nested-fields <?php echo e(old('jenis') != 'absensi' ? 'hidden' : ''); ?>">
                                 <div class="field-row">
                                     <div class="field-half">
                                         <label class="field-label">Jam Masuk</label>
                                         <input type="time" class="custom-input" name="jam_masuk" value="<?php echo e(old('jam_masuk')); ?>">
                                         <small class="field-hint">Kosongkan jika hanya lupa pulang</small>
                                     </div>
                                     <div class="field-half">
                                         <label class="field-label">Jam Pulang</label>
                                         <input type="time" class="custom-input" name="jam_pulang" value="<?php echo e(old('jam_pulang')); ?>">
                                         <small class="field-hint">Kosongkan jika hanya lupa masuk</small>
                                     </div>
                                 </div>
                             </div>

                             <!-- Fields for Kegiatan -->
                             <div id="fields_kegiatan" class="nested-fields-kegiatan <?php echo e(old('jenis') != 'kegiatan' ? 'hidden' : ''); ?>">
                                 <div class="form-field">
                                     <label class="field-label">Deskripsi Kegiatan <span class="required-mark">*</span></label>
                                     <textarea class="custom-textarea" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat kegiatan yang Anda lakukan hari itu..."><?php echo e(old('deskripsi')); ?></textarea>
                                 </div>
                             </div>

                             <div class="form-field">
                                 <label class="field-label">Alasan Keterlambatan Pengisian <span class="required-mark">*</span></label>
                                 <textarea class="custom-textarea" name="alasan_terlambat" rows="2" required placeholder="Mengapa Anda baru mengisi sekarang?"><?php echo e(old('alasan_terlambat')); ?></textarea>
                             </div>

                             <div class="form-field">
                                 <label class="field-label">Foto / Bukti Lampiran <span class="hint-mark">(Opsional)</span></label>
                                 <div class="custom-upload-area">
                                     <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                     <p class="upload-text">Klik atau Drag file kesini</p>
                                     <p class="upload-detail">Maksimal 2MB (JPG, PNG, PDF)</p>
                                     <input type="file" class="hidden-file-input" name="bukti" accept=".jpg,.jpeg,.png,.pdf">
                                 </div>
                                 <div id="file-name-display" class="file-chosen-info hidden"></div>
                             </div>

                             <div class="form-actions">
                                 <button type="submit" class="premium-submit-btn" id="btn-kirim-pengajuan">
                                     <i class="fas fa-paper-plane"></i>
                                     Kirim Pengajuan
                                 </button>
                             </div>

                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <!-- Riwayat Pengajuan -->
            <div class="history-column">
                <div class="premium-card">
                    <div class="card-header-premium">
                        <div class="header-title-wrapper-spread">
                            <div class="title-with-icon">
                                <div class="icon-circle-secondary">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h5 class="card-title-premium">Riwayat Pengajuan</h5>
                            </div>
                            <span class="count-badge"><?php echo e($pengajuans->count()); ?> Pengajuan</span>
                        </div>
                    </div>
                    <div class="card-body-history">
                        <?php if($pengajuans->isEmpty()): ?>
                            <div class="empty-state">
                                <div class="empty-icon-wrapper">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h6 class="empty-title">Belum ada riwayat</h6>
                                <p class="empty-subtitle">Anda belum pernah melakukan pengajuan lupa absensi atau kegiatan.</p>
                            </div>
                        <?php else: ?>
                            <div class="scrollable-table">
                                <table class="premium-table">
                                    <thead>
                                        <tr>
                                            <th class="col-info">Informasi Lupa</th>
                                            <th class="col-detail">Detail Isi</th>
                                            <th class="col-status">Status Approval</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pengajuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="table-row">
                                                <td class="cell-info">
                                                    <div class="info-content">
                                                        <div class="type-icon-wrapper type-<?php echo e($p->jenis); ?>">
                                                            <i class="fas fa-<?php echo e($p->jenis == 'absensi' ? 'clock' : 'clipboard-list'); ?>"></i>
                                                        </div>
                                                        <div class="date-wrapper">
                                                            <div class="main-date"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y')); ?></div>
                                                            <span class="type-tag tag-<?php echo e($p->jenis); ?>">
                                                                <?php echo e(ucfirst($p->jenis)); ?>

                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cell-detail">
                                                    <div class="diajukan-time">
                                                        <i class="far fa-calendar-alt"></i>
                                                        Diajukan: <?php echo e($p->created_at->format('d/m/Y H:i')); ?>

                                                    </div>
                                                    <?php if($p->jenis == 'absensi'): ?>
                                                        <div class="time-block-wrapper">
                                                            <div class="time-item border-right">
                                                                <div class="time-label">MASUK</div>
                                                                <div class="time-value"><?php echo e($p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '--:--'); ?></div>
                                                            </div>
                                                            <div class="time-item">
                                                                <div class="time-label">PULANG</div>
                                                                <div class="time-value"><?php echo e($p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '--:--'); ?></div>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="text-description" title="<?php echo e($p->deskripsi); ?>">
                                                            "<?php echo e($p->deskripsi); ?>"
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
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
                                                        <div class="status-pill state-rejected" title="<?php echo e($p->catatan_pembimbing); ?>">
                                                            <i class="fas fa-times-circle"></i>Ditolak
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/siswa/pengajuan-siswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.nav.siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/pengajuan.blade.php ENDPATH**/ ?>