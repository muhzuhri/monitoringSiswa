<?php $__env->startSection('title', 'Dashboard Dosen - Monitoring Siswa'); ?>
<?php $__env->startSection('body-class', 'dosen-dashboard-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/style-dosen.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/pembimbing/home-pembimbing.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>

    <div class="body-style">

        <div class="container">
            <!-- Hero Greeting -->
            <div class="welcome-banner">
                <div class="welcome-text d-flex align-items-center gap-4">
                    <div class="welcome-avatar">
                        <img src="<?php echo e($user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=f6c23e&color=fff'); ?>" alt="Profile" class="rounded-circle" width="80" height="80" style="object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    </div>
                    <div>
                        <h1 class="welcome-title">Halo, <?php echo e($user->nama); ?>! 👋</h1>
                        <p class="welcome-subtitle">Selamat datang kembali di Website Magang Fasilkom Unsri.</p>
                    </div>
                </div>
                <div class="welcome-status">
                    <span class="status-indicator-light">
                        <i class="fas fa-circle"></i> Sesi Aktif
                    </span>
                </div>
            </div>
            <div class="home-premium-section">
                
                <!-- Fasilkom Banner -->
                <div class="fasilkom-banner cursor-pointer" onclick="openSejarah()">
                    <div class="banner-icon-wrapper">
                        <img src="<?php echo e(asset('images/unsri-pride.png')); ?>" alt="">
                    </div>
                    <div class="banner-content">
                        <h2 class="banner-title">Fakultas Ilmu Komputer</h2>
                        <p class="banner-description">
                            Universitas yang berkomitmen mencetak lulusan kompeten di bidang teknologi informasi dan ilmu komputer. 
                            Sistem ini membantu monitoring kegiatan magang mahasiswa secara terpadu.
                        </p>
                        <div class="banner-badge">
                            <i class="fas fa-check-circle"></i>
                            Sistem Monitoring Magang Aktif
                        </div>
                    </div>
                    <div class="banner-more"><i class="fas fa-ellipsis-h"></i></div>
                </div>

                <!-- Tentang Fasilkom -->
                <span class="section-title-sm">Tentang Fasilkom</span>
                <div class="info-grid">
                    <div class="info-card" onclick="openVisiMisi()">
                        <div class="info-icon-box"><i class="fas fa-layer-group"></i></div>
                        <h3 class="info-card-title">Visi & Misi</h3>
                        <p class="info-card-text">
                            <?php echo e(Str::limit($informasi->visi, 100)); ?>

                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-icon-box"><i class="far fa-clock"></i></div>
                        <h3 class="info-card-title">Jam Operasional</h3>
                        <p class="info-card-text">
                            <?php echo nl2br(e($informasi->jam_operasional)); ?><br>
                            <?php echo e($informasi->deskripsi_jam_operasional); ?>

                        </p>
                    </div>

                    <a href="<?php echo e($informasi->link_maps); ?>" target="_blank" class="info-card-link">
                        <div class="info-card">
                            <div class="info-icon-box"><i class="fas fa-map-marker-alt"></i></div>
                            <h3 class="info-card-title">Lokasi</h3>
                            <p class="info-card-text">
                                <?php echo e($informasi->alamat_lokasi); ?>

                            </p>
                        </div>
                    </a>

                    <div class="info-card">
                        <div class="info-icon-box"><i class="fas fa-phone-alt"></i></div>
                        <h3 class="info-card-title">Kontak</h3>
                        <p class="info-card-text">
                            Email: <?php echo e($informasi->email_kontak); ?><br>
                            Telp: <?php echo e($informasi->telp_kontak); ?><br>
                            Website: <?php echo e($informasi->website_kontak); ?>

                        </p>
                    </div>
                </div>

                <span class="section-title-sm">Program Studi</span>
                <div class="prodi-list-container">
                    <?php $__empty_1 = true; $__currentLoopData = $programStudis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="prodi-item-simple">
                            <div class="prodi-dot" style="background-color: <?php echo e($prodi->warna_dot); ?>"></div>
                            <span class="prodi-name"><?php echo e($prodi->nama); ?></span>
                            <span class="prodi-badge" style="color: <?php echo e($prodi->warna_dot); ?>; background-color: <?php echo e($prodi->warna_dot); ?>15; border-color: <?php echo e($prodi->warna_dot); ?>30">
                                <?php echo e($prodi->jenjang); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-muted small">Belum ada data program studi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi Misi Modal -->
    <div id="visiMisiModal" class="modal-overlay" onclick="closeModal('visiMisiModal')">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-close" onclick="closeModal('visiMisiModal')">&times;</div>
            <h2 class="modal-title">Visi & Misi <?php echo e($informasi->nama_fakultas); ?></h2>
            <div class="modal-body">
                <h4>Visi</h4>
                <p><?php echo e($informasi->visi); ?></p>

                <h4>Misi</h4>
                <ul>
                    <?php $__currentLoopData = $informasi->misi_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($misi); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Sejarah Modal -->
    <div id="sejarahModal" class="modal-overlay" onclick="closeModal('sejarahModal')">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-close" onclick="closeModal('sejarahModal')">&times;</div>
            <h2 class="modal-title">Sejarah <?php echo e($informasi->nama_fakultas); ?></h2>
            <div class="modal-body">
                <p>
                    <?php echo nl2br(e($informasi->sejarah)); ?>

                </p>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/pembimbing.blade.php ENDPATH**/ ?>