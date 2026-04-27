<?php $__env->startSection('title', 'Profil Saya - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/profil.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h4 class="page-title"><i class="fas fa-user-circle me-2 text-primary"></i>Pengaturan Profil</h4>
                <p class="page-subtitle">Kelola informasi pribadi, data akademik, dan keamanan akun Anda.</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="ui-alert ui-alert-success mt-4">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="ui-alert ui-alert-danger mt-4">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                <div class="alert-content">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-grid mt-4">
            <!-- Sidebar: Card Ringkasan -->
            <div class="profile-sidebar">
                <div class="ui-card text-center p-4">
                    <div class="profile-avatar-container mx-auto mb-3">
                        <div class="avatar-xl bg-primary-light text-primary">
                            <span><?php echo e(strtoupper(substr($user->nama, 0, 1))); ?></span>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo e($user->nama); ?></h4>
                    <p class="text-muted small mb-1"><?php echo e($user->email); ?></p>
                    <div class="badge-role mb-3">Guru Pembimbing</div>
                    
                    <div class="sidebar-stats mt-3 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-12">
                                <span class="d-block fw-bold text-dark"><?php echo e($user->sekolah ?? '-'); ?></span>
                                <span class="text-muted small">Asal Sekolah</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card mt-4 p-4 info-card">
                    <div class="d-flex align-items-center gap-2 text-primary mb-3">
                        <i class="fas fa-shield-alt"></i>
                        <span class="fw-bold small">Keamanan Akun</span>
                    </div>
                    <p class="text-muted small mb-0">Pastikan password Anda kuat untuk menjaga keamanan data bimbingan siswa Anda.</p>
                </div>
            </div>

            <!-- Main Content: Form -->
            <div class="profile-main">
                <form action="<?php echo e(route('guru.profil.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- Box 1: Data Pribadi -->
                    <div class="ui-card mb-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern text-primary"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Nama Lengkap</label>
                                        <input type="text" name="nama" class="form-control-modern"
                                            value="<?php echo e(old('nama', $user->nama)); ?>" required placeholder="Nama Lengkap Anda">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Alamat Email</label>
                                        <input type="email" name="email" class="form-control-modern"
                                            value="<?php echo e(old('email', $user->email)); ?>" required placeholder="email@contoh.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Nomor WhatsApp</label>
                                        <input type="text" name="no_hp" class="form-control-modern"
                                            value="<?php echo e(old('no_hp', $user->no_hp)); ?>" placeholder="e.g 0812XXXXXXXX">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Data Akademik / Sekolah -->
                    <div class="ui-card mb-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern text-success"><i class="fas fa-graduation-cap me-2"></i>Data Akademik</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Jabatan / Guru Mapel</label>
                                        <input type="text" name="jabatan" class="form-control-modern"
                                            value="<?php echo e(old('jabatan', $user->jabatan)); ?>" placeholder="e.g Guru Produktif">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Asal Sekolah</label>
                                        <input type="text" name="sekolah" class="form-control-modern"
                                            value="<?php echo e(old('sekolah', $user->sekolah)); ?>" placeholder="Nama Sekolah Anda">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">NPSN Sekolah</label>
                                        <input type="text" name="npsn" class="form-control-modern"
                                            value="<?php echo e(old('npsn', $user->npsn)); ?>" placeholder="NPSN Sekolah">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Keamanan -->
                    <div class="ui-card mb-4">
                        <div class="card-header-modern">
                            <h5 class="card-title-modern text-danger"><i class="fas fa-key me-2"></i>Ubah Password</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Password Baru <small class="text-muted fw-normal">(Opsional)</small></label>
                                        <input type="password" name="password" class="form-control-modern"
                                            placeholder="Minimal 8 karakter">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control-modern"
                                            placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-bottom mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn-save-profile">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/profil.blade.php ENDPATH**/ ?>