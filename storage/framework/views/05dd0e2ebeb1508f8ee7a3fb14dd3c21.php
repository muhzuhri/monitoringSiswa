<?php $__env->startSection('title', 'Profil Pimpinan - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'laporan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/profil.css')); ?>?v=<?php echo e(time()); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">
        <div class="page-header">
            <div>
                <h2 class="page-title">Profil Pengguna</h2>
                <p class="page-subtitle">Kelola informasi akun dan pengaturan keamanan (Pimpinan).</p>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="ui-alert ui-alert-danger">
                <div>
                    <h6 class="m-0 mb-2 fs-0-8 fw-700 text-danger"><i class="fas fa-exclamation-triangle"></i> Terdapat Kesalahan:</h6>
                    <ul class="m-0 pl-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="fs-0-85"><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- Custom Tabs Navigation -->
        <div class="tabs-wrapper">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="tab-biodata" data-bs-toggle="pill" data-bs-target="#pills-biodata" type="button" role="tab" aria-selected="true">
                    <i class="fas fa-user-tie"></i>
                    <span>Informasi Akun</span>
                </button>

                <button class="tab-button" id="tab-keamanan" data-bs-toggle="pill" data-bs-target="#pills-keamanan" type="button" role="tab" aria-selected="false">
                    <i class="fas fa-lock"></i>
                    <span>Keamanan</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- TAB INFORMASI AKUN -->
            <div class="tab-pane fade show active" id="pills-biodata" role="tabpanel">
                <div class="laporan-grid max-w-800 mx-auto">
                    <div class="ui-card">
                        <h5 class="page-title mb-1-5">Informasi Akun Pimpinan</h5>

                        <form action="<?php echo e(route('pimpinan.profil.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
                            <div class="form-group mb-3">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                                    <input type="text" name="nama" class="form-control" value="<?php echo e(old('nama', $user->nama)); ?>" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="fw-700 fs-0-85 mb-2 d-block">No. HP / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="no_hp" class="form-control" value="<?php echo e(old('no_hp', $user->no_hp)); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Jabatan</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                    <input type="text" name="jabatan" class="form-control" value="<?php echo e(old('jabatan', $user->jabatan)); ?>" required>
                                </div>
                            </div>

                            <hr class="my-4 opacity-25">

                            <button type="submit" class="btn-upload-submit w-100">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan Profil
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TAB KEAMANAN -->
            <div class="tab-pane fade" id="pills-keamanan" role="tabpanel">
                <div class="laporan-grid max-w-800 mx-auto">
                    <div class="ui-card">
                        <h5 class="page-title mb-1-5">Keamanan & Sandi</h5>

                        <form action="<?php echo e(route('pimpinan.profil.password')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            
                            <div class="form-group mb-3">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Kata Sandi Saat Ini</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input type="password" name="current_password" class="form-control" placeholder="Masukkan sandi saat ini" required>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="fw-700 fs-0-85 mb-2 d-block">Konfirmasi Sandi Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock text-success"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi sandi baru" required>
                                </div>
                            </div>
                            
                            <hr class="my-4 opacity-25">

                            <button type="submit" class="btn-upload-submit w-100 bg-dark">
                                <i class="fas fa-shield-alt mr-2"></i> Ganti Kata Sandi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/profil.blade.php ENDPATH**/ ?>