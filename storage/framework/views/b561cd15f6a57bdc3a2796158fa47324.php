<?php $__env->startSection('title', 'Profil Saya - Monitoring Siswa'); ?>
<?php $__env->startSection('body-class', 'dosen-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/profil.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="dashboard-container mt-4">

        
        <div class="page-header">
            <div class="page-header-icon">
                <i class="fas fa-user-circle"></i>
            </div>
            <div>
                <h2 class="page-title">Pengaturan Profil</h2>
                <p class="page-subtitle">Kelola informasi pribadi, data profesional, dan keamanan akun Anda.</p>
            </div>
        </div>

        
        <?php if(session('success')): ?>
            <div class="custom-alert alert-success-soft">
                <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                <div><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="custom-alert alert-danger-soft">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <ul style="margin:0;padding-left:1rem;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="profile-grid mt-4">

            
            <div class="profile-sidebar">

                
                <div class="avatar-card">
                    <div class="avatar-card-banner"></div>
                    <div class="avatar-wrapper">
                        <div class="avatar-ring">
                            <?php echo e(strtoupper(substr($pembimbing->nama, 0, 1))); ?>

                        </div>
                    </div>
                    <div class="avatar-card-body">
                        <p class="profile-name"><?php echo e($pembimbing->nama); ?></p>
                        <p class="profile-email"><?php echo e($pembimbing->email); ?></p>
                        <span class="role-badge">
                            <i class="fas fa-user-tie"></i> Pembimbing
                        </span>

                        <?php if($pembimbing->instansi): ?>
                        <div class="profile-instansi">
                            <span class="instansi-label"><i class="fas fa-building me-1"></i>Instansi</span>
                            <span class="instansi-value"><?php echo e($pembimbing->instansi); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if($pembimbing->jabatan): ?>
                        <div class="profile-instansi" style="border-top:none; padding-top:.5rem; margin-top:0;">
                            <span class="instansi-label"><i class="fas fa-id-badge me-1"></i>Jabatan</span>
                            <span class="instansi-value"><?php echo e($pembimbing->jabatan); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="stats-card">
                    <div class="stats-card-header">
                        <i class="fas fa-info-circle"></i> Informasi Akun
                    </div>
                    <div class="stats-item">
                        <div class="stats-icon purple"><i class="fas fa-envelope"></i></div>
                        <div>
                            <span class="stats-info-label">Email</span>
                            <span class="stats-info-value"><?php echo e($pembimbing->email); ?></span>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-icon blue"><i class="fas fa-phone"></i></div>
                        <div>
                            <span class="stats-info-label">Nomor WhatsApp</span>
                            <span class="stats-info-value"><?php echo e($pembimbing->no_telp ?? '-'); ?></span>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-icon green"><i class="fas fa-building"></i></div>
                        <div>
                            <span class="stats-info-label">Instansi</span>
                            <span class="stats-info-value"><?php echo e($pembimbing->instansi ?? '-'); ?></span>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="stats-icon orange"><i class="fas fa-id-badge"></i></div>
                        <div>
                            <span class="stats-info-label">Jabatan</span>
                            <span class="stats-info-value"><?php echo e($pembimbing->jabatan ?? '-'); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <div class="security-title">Tips Keamanan</div>
                        <p class="security-text">Gunakan password yang kuat dan unik minimal 8 karakter untuk melindungi akun Anda.</p>
                    </div>
                </div>

            </div>

            
            <div class="profile-main">
                <form action="<?php echo e(route('pembimbing.profil.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-header-icon icon-purple">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="card-title-wrap">
                                <h4 class="card-title">Informasi Pribadi</h4>
                                <p class="card-desc">Nama, email, dan nomor kontak Anda</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-user"></i> Nama Lengkap</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-user"></i></span>
                                        <input type="text" name="nama" class="custom-input"
                                            value="<?php echo e(old('nama', $pembimbing->nama)); ?>"
                                            placeholder="Nama lengkap Anda" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-envelope"></i> Email Utama</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                        <input type="email" name="email" class="custom-input"
                                            value="<?php echo e(old('email', $pembimbing->email)); ?>"
                                            placeholder="email@contoh.com" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row" style="margin-top:1.25rem;">
                                <div class="form-group">
                                    <label class="form-label"><i class="fab fa-whatsapp"></i> Nomor WhatsApp</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-phone"></i></span>
                                        <input type="text" name="no_telp" class="custom-input"
                                            value="<?php echo e(old('no_telp', $pembimbing->no_telp)); ?>"
                                            placeholder="e.g. 08123456789">
                                    </div>
                                    <span class="input-hint">Digunakan untuk dihubungi oleh siswa magang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-header-icon icon-blue">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="card-title-wrap">
                                <h4 class="card-title">Data Profesional</h4>
                                <p class="card-desc">Jabatan dan informasi institusi Anda</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-id-badge"></i> Jabatan / Pangkat</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-id-badge"></i></span>
                                        <input type="text" name="jabatan" class="custom-input"
                                            value="<?php echo e(old('jabatan', $pembimbing->jabatan)); ?>"
                                            placeholder="e.g. Lektor Kepala">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-building"></i> Instansi / Unit Kerja</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-building"></i></span>
                                        <input type="text" name="instansi" class="custom-input"
                                            value="<?php echo e(old('instansi', $pembimbing->instansi)); ?>"
                                            placeholder="e.g. Politeknik Negeri Sriwijaya">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    
                    <div class="content-card">
                        <div class="card-header">
                            <div class="card-header-icon icon-yellow">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="card-title-wrap">
                                <h4 class="card-title">Keamanan &amp; Password</h4>
                                <p class="card-desc">Kosongkan jika tidak ingin mengubah password</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-key"></i> Password Baru
                                        <span class="text-muted small fw-normal">(Opsional)</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password" id="passwordInput"
                                            class="custom-input" placeholder="Minimal 8 karakter">
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="form-label"><i class="fas fa-check-double"></i> Konfirmasi Password</label>
                                    <div class="input-wrapper">
                                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                                        <input type="password" name="password_confirmation"
                                            class="custom-input" placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions-bottom">
                            
                            <button type="submit" class="btn-save-profile">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>

                    

                </form>
            </div>
        </div>
    </div>

    <script>
        // Simple password strength indicator
        const pwInput = document.getElementById('passwordInput');
        const pwFill  = document.getElementById('pwStrengthFill');

        if (pwInput && pwFill) {
            pwInput.addEventListener('input', function () {
                const val = this.value;
                let strength = 0;
                if (val.length >= 8) strength++;
                if (/[A-Z]/.test(val)) strength++;
                if (/[0-9]/.test(val)) strength++;
                if (/[^A-Za-z0-9]/.test(val)) strength++;

                const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
                const widths = ['25%', '50%', '75%', '100%'];

                if (val.length === 0) {
                    pwFill.style.width = '0';
                } else {
                    pwFill.style.width  = widths[strength - 1] || '0';
                    pwFill.style.background = colors[strength - 1] || '#ef4444';
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/profil.blade.php ENDPATH**/ ?>