

<?php $__env->startSection('title', 'Absensi & Kegiatan - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'absensi-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/siswa/absensiKegiatan-siswa.css')); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/siswa/absensiKegiatan-siswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">
        <div class="page-header">
            <div>
                <h2 class="page-title">Absensi & Kegiatan Harian</h2>
                <p class="page-subtitle">
                    Kelola kehadiran dan laporan aktivitas magang kamu di sini.
                </p>
            </div>

            <div class="current-date-badge">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDateDisplay">
                    <?php echo e(\Carbon\Carbon::now()->translatedFormat('l, d F Y')); ?>

                </span>
            </div>
        </div>


        <!-- Notifications (Moved to Global Area) -->
        <div class="notifications-area">
            <?php if(session('success')): ?>
                <div class="ui-alert ui-alert-success mb-4 p-3 rounded-3 bg-success bg-opacity-10 text-success border border-success border-opacity-20 d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo e(session('success')); ?></span>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="ui-alert ui-alert-danger mb-4 p-3 rounded-3 bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?php echo e(session('error')); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Custom Tabs -->
        <div class="tabs-wrapper">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button <?php echo e(session('active_tab') == 'logbook' ? '' : 'active'); ?>" id="tab-absensi"
                    data-bs-toggle="pill" data-bs-target="#pills-absensi" type="button" role="tab"
                    aria-selected="<?php echo e(session('active_tab') == 'logbook' ? 'false' : 'true'); ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Absensi</span>
                </button>

                <button class="tab-button <?php echo e(session('active_tab') == 'logbook' ? 'active' : ''); ?>" id="tab-logbook"
                    data-bs-toggle="pill" data-bs-target="#pills-logbook" type="button" role="tab"
                    aria-selected="<?php echo e(session('active_tab') == 'logbook' ? 'true' : 'false'); ?>">
                    <i class="fas fa-edit"></i>
                    <span>Logbook</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- TAB ABSENSI -->
            <div class="tab-pane fade <?php echo e(session('active_tab') == 'logbook' ? '' : 'show active'); ?>" id="pills-absensi"
                role="tabpanel">
                <div class="content-grid">
                    <!-- Left: Action Card -->
                    <div class="action-section">
                        <div class="ui-card">

                            <div class="clock-section">
                                <span class="clock-label">Waktu Saat Ini</span>
                                <h1 class="real-time-clock" id="realtimeClock">--:--</h1>
                                <div class="location-badge">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                    <span><?php echo e($user->perusahaan); ?></span>
                                </div>
                            </div>

                            <div class="action-forms mt-4">
                                <?php if($isFinished): ?>
                                    <div class="bg-primary bg-opacity-10 p-4 rounded-4 text-center border border-primary border-opacity-20">
                                        <div class="text-primary mb-2">
                                            <i class="fas fa-graduation-cap fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Masa Magang Berakhir</h6>
                                        <p class="text-muted small mb-0">Terima kasih telah menyelesaikan program magang. Fitur absensi telah dinonaktifkan.</p>
                                    </div>
                                <?php elseif(!$absensiHariIni): ?>
                                    <!-- Check In Form -->
                                    <form id="formAbsensiMasuk" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="type" value="masuk">
                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                Status Kehadiran
                                            </label>
                                            <select name="status_pilihan" class="form-control-custom" required
                                                onchange="togglePhotoLabel(this.value)">
                                                <option value="hadir" selected>Hadir (Sesuai Jadwal)</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                <span id="photoLabel">Foto Selfie di Lokasi</span>
                                            </label>

                                            <input type="file" name="foto" class="form-control-custom" required
                                                accept="image/*" capture="camera">
                                            <small class="text-muted mt-2 d-block" id="photoHelp">
                                                <i class="fas fa-info-circle me-1"></i> Ambil foto sesuai status kehadiran.
                                            </small>
                                        </div>

                                        <button class="btn-action-main btn-checkin" type="submit">
                                            <div class="icon-box-white">
                                                <i class="fas fa-fingerprint"></i>
                                            </div>
                                            <div class="btn-text-content">
                                                <span class="btn-title">ABSEN MASUK</span>
                                                <span class="btn-subtitle">Jadwal: 07:00 - 10:00</span>
                                            </div>
                                        </button>
                                    </form>
                                <?php elseif(!$absensiHariIni->jam_pulang): ?>
                                    <!-- Check Out Form -->
                                    <form id="formAbsensiPulang" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="type" value="pulang">

                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                Foto Bukti Pulang
                                            </label>
                                            <input type="file" name="foto" class="form-control-custom" required
                                                accept="image/*" capture="camera">
                                            <small class="text-muted mt-2 d-block">
                                                <i class="fas fa-info-circle me-1"></i> Ambil foto bukti selesai kegiatan.
                                            </small>
                                        </div>

                                        <button class="btn-action-main btn-checkout" type="submit">
                                            <div class="icon-box-white">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </div>
                                            <div class="btn-text-content">
                                                <span class="btn-title">ABSEN PULANG</span>
                                                <span class="btn-subtitle">Jadwal: 15:00 - 16:30</span>
                                            </div>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="bg-light p-4 rounded-4 text-center border">
                                        <div class="text-success mb-2">
                                            <i class="fas fa-check-double fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Absensi Selesai</h6>
                                        <p class="text-muted small mb-0">Kamu sudah melakukan absen masuk & pulang hari
                                            ini.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right: History & Status -->
                    <div class="history-section">
                        <div class="ui-card">
                            <div class="section-header">
                                <i class="fas fa-history"></i>
                                <h5>Riwayat Absensi</h5>
                            </div>
                            <div class="table-container">
                                <table class="ui-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Masuk</th>
                                            <th>Pulang</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $absen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td data-label="Tanggal" class="fw-bold">
                                                    <?php echo e(\Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y')); ?>

                                                </td>
                                                <td data-label="Masuk">
                                                    <div class="time-cell">
                                                        <span
                                                            class="time-val in"><?php echo e($absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--'); ?></span>
                                                        <?php if($absen->foto_masuk): ?>
                                                            <a href="<?php echo e(asset('storage/' . $absen->foto_masuk)); ?>"
                                                                target="_blank" class="img-link">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td data-label="Pulang">
                                                    <div class="time-cell">
                                                        <span
                                                            class="time-val out"><?php echo e($absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '--:--'); ?></span>
                                                        <?php if($absen->foto_pulang): ?>
                                                            <a href="<?php echo e(asset('storage/' . $absen->foto_pulang)); ?>"
                                                                target="_blank" class="img-link">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    <?php if($absen->verifikasi == 'verified'): ?>
                                                        <span class="badge-ui badge-success">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    <?php elseif($absen->verifikasi == 'rejected'): ?>
                                                        <span class="badge-ui badge-danger">
                                                            <i class="fas fa-times"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge-ui badge-warning">
                                                            <i class="fas fa-clock"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat
                                                    absensi.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="history-footer">
                                <button class="btn-history-more" data-bs-toggle="modal"
                                    data-bs-target="#modalHistoryDetail">
                                    <i class="fas fa-external-link-alt"></i> Detail Lengkap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB LOGBOOK -->
            <div class="tab-pane fade <?php echo e(session('active_tab') == 'logbook' ? 'show active' : ''); ?>" id="pills-logbook"
                role="tabpanel">
                <div class="content-grid">
                    <!-- Form Input Logbook -->
                    <div class="logbook-form-section">
                        <div class="ui-card">
                            <?php if($isFinished): ?>
                                <div class="text-center py-4">
                                    <div class="bg-primary bg-opacity-10 p-4 rounded-4 text-center border border-primary border-opacity-20">
                                        <div class="text-primary mb-2">
                                            <i class="fas fa-book fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Logbook Selesai</h6>
                                        <p class="text-muted small mb-0">Masa magang Anda telah berakhir. Anda tetap dapat melihat seluruh riwayat kegiatan.</p>
                                    </div>
                                </div>
                            <?php elseif(!$logbookHariIni): ?>
                                <form action="<?php echo e(route('siswa.logbook.store')); ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="tanggal" value="<?php echo e(date('Y-m-d')); ?>">

                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger p-2 mb-3 small">
                                            <ul class="mb-0">
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    <div class="form-group text-center mb-4">
                                        <div class="clock-section">
                                            <span class="clock-label">Waktu Saat Ini</span>
                                            <h1 class="real-time-clock" id="logbookRealtimeClock">--:--</h1>
                                            <div class="location-badge">
                                                <i class="fas fa-map-marker-alt fa-lg"></i>
                                                <span><?php echo e($user->perusahaan); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label-custom">Deskripsi Kegiatan</label>
                                        <textarea name="kegiatan" class="form-control-custom" rows="5"
                                            placeholder="Tuliskan detail pekerjaan, kendala, atau hasil hari ini..." required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label-custom">Foto Bukti (Opsional)</label>
                                        <input type="file" name="foto" class="form-control-custom" accept="image/*">
                                    </div>
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-paper-plane me-2"></i> Simpan Kegiatan
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <div class="bg-light p-4 rounded-4 text-center border">
                                        <div class="text-success mb-2">
                                            <i class="fas fa-check-double fa-3x"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Kegiatan Selesai</h6>
                                        <p class="text-muted small mb-0">Kamu sudah mengisi laporan kegiatan hari ini.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- History Logbook List -->
                    <div class="logbook-history-section">
                        <div class="ui-card">
                            <div class="section-header">
                                <i class="fas fa-tasks"></i>
                                <h5>Riwayat Kegiatan</h5>
                            </div>

                            <div class="table-container">
                                <table class="ui-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jam</th>
                                            <th>Detail Pekerjaan</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $logbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td data-label="Tanggal" class="w-nowrap fw-bold">
                                                    <?php echo e(\Carbon\Carbon::parse($log->tanggal)->translatedFormat('d M Y')); ?>

                                                </td>
                                                <td data-label="Jam">
                                                    <span class="badge bg-light text-dark border">
                                                        <?php echo e($log->created_at ? $log->created_at->format('H:i') : '--:--'); ?>

                                                    </span>
                                                </td>
                                                <td data-label="Detail">
                                                    <div class="log-content-wrapper">
                                                        <div class="text-dark">
                                                            <?php echo e(Str::limit($log->kegiatan, 120)); ?>

                                                        </div>
                                                        <?php if($log->catatan_pembimbing): ?>
                                                            <div
                                                                class="mt-2 p-2 rounded-3 bg-light border-start border-3 border-primary small">
                                                                <span class="text-primary fw-bold">Catatan:</span>
                                                                "<?php echo e($log->catatan_pembimbing); ?>"
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    <?php if($log->status == 'verified'): ?>
                                                        <span class="badge-ui badge-success">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    <?php elseif($log->status == 'rejected'): ?>
                                                        <span class="badge-ui badge-danger">
                                                            <i class="fas fa-times"></i>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge-ui badge-warning">
                                                            <i class="fas fa-clock"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat
                                                    kegiatan.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Detai (Floating Above) -->
    <div class="modal fade" id="modalHistoryDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-content-premium">
                <div class="modal-header-premium">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0">Riwayat Lengkap</h5>
                            <p class="modal-subtitle text-white-50">Lihat semua data absensi dan kegiatan kamu.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <!-- Tab Switcher inside Modal -->
                    <div class="modal-tab-nav">
                        <button class="modal-tab-btn active" onclick="switchModalTab('absensi')">
                            <i class="fas fa-calendar-check me-1"></i> Absensi
                        </button>
                        <button class="modal-tab-btn" onclick="switchModalTab('kegiatan')">
                            <i class="fas fa-edit me-1"></i> Kegiatan
                        </button>
                    </div>

                    <!-- Navigation Bar -->
                    <div class="pagination-nav-bar px-4 py-3">
                        <button id="btnPrevPage" class="btn-nav-page" onclick="changePage(1)">
                            <i class="fas fa-chevron-left me-1"></i> Sebelumnya
                        </button>
                        <div class="page-indicator fw-bold text-primary">
                            Halaman <span id="currentPageNum">1</span>
                        </div>
                        <button id="btnNextPage" class="btn-nav-page" onclick="changePage(-1)">
                            Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>

                    <!-- Content Area -->
                    <div class="history-table-wrapper px-3 pb-3">
                        <div id="absensiTableArea">
                            <table class="history-detail-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Detail Waktu</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="historyAbsensiBody">
                                    <!-- AJAX Content -->
                                </tbody>
                            </table>
                        </div>

                        <div id="kegiatanTableArea" class="hidden">
                            <table class="history-detail-table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th class="min-w-250">Kegiatan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="historyKegiatanBody">
                                    <!-- AJAX Content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Check In (Refactored) -->
    <div class="modal fade" id="modalCheckIn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title">Konfirmasi Check-In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form>
                        <div class="form-group">
                            <label class="form-label-custom">Kondisi Kesehatan</label>
                            <select class="form-control-custom">
                                <option>Sehat</option>
                                <option>Kurang Fit</option>
                                <option>Sakit (Izin)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label-custom">Bukti Foto (Selfie di Lokasi)</label>
                            <input type="file" class="form-control-custom" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label class="form-label-custom">Catatan Tambahan (Opsional)</label>
                            <textarea class="form-control-custom" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn-pill btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn-pill btn-primary-custom">Lakukan Check-In</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.absensiConfig = {
            absensiDetailRoute: "<?php echo e(route('siswa.absensi.detail')); ?>",
            logbookDetailRoute: "<?php echo e(route('siswa.logbook.detail')); ?>",
            absensiStoreRoute: "<?php echo e(route('siswa.absensi.store')); ?>",
            csrfToken: "<?php echo e(csrf_token()); ?>",
            storageUrl: "<?php echo e(asset('storage')); ?>"
        };
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/absensiKegiatan.blade.php ENDPATH**/ ?>