

<?php $__env->startSection('title', 'Profil Saya - SIM Magang Fasilkom Unsri'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/siswa/profil-siswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="profil-wrapper">
        <div class="profil-layout">

            
            <div class="profil-sidebar">
                <div class="profile-card">
                    <div class="profile-header-gradient"></div>
                    <div class="profile-img-container">
                        <div class="profile-img-wrapper">
                            <img src="<?php echo e($user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=4e73df&color=fff'); ?>"
                                alt="Avatar" id="profile-preview">
                            <label for="foto_profil" class="btn-edit-photo" title="Ubah Foto">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                    </div>
                    <div class="profile-body">
                        <h4 class="profile-name"><?php echo e($user->nama); ?></h4>
                        <p class="profile-nisn"><?php echo e($user->nisn); ?></p>
                        <div class="role-badge">
                            <i class="fas fa-user-graduate"></i> SISWA MAGANG
                        </div>

                        <hr class="profile-divider">

                        <div class="profile-info-list">
                            <div class="profile-info-item">
                                <div class="icon-box icon-blue">
                                    <i class="fas fa-school"></i>
                                </div>
                                <div>
                                    <small class="info-label">Sekolah</small>
                                    <span class="info-value"><?php echo e($user->sekolah); ?></span>
                                </div>
                            </div>
                            <div class="profile-info-item">
                                <div class="icon-box icon-green">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <small class="info-label">Perusahaan</small>
                                    <span class="info-value"><?php echo e($user->perusahaan); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="profil-main">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <nav class="profile-nav" id="profileTab" role="tablist">
                            <button class="profile-nav-link active" id="data-diri-tab" data-bs-toggle="tab"
                                data-bs-target="#data-diri" type="button" role="tab" aria-selected="true">
                                <i class="fas fa-id-card"></i>
                                <span>DATA DIRI</span>
                            </button>
                            <button class="profile-nav-link" id="pembimbing-tab" data-bs-toggle="tab"
                                data-bs-target="#pembimbing" type="button" role="tab" aria-selected="false">
                                <i class="fas fa-user-tie"></i>
                                <span>PEMBIMBING</span>
                            </button>
                            <button class="profile-nav-link" id="security-tab" data-bs-toggle="tab"
                                data-bs-target="#security" type="button" role="tab" aria-selected="false">
                                <i class="fas fa-shield-alt"></i>
                                <span>KEAMANAN</span>
                            </button>
                        </nav>
                    </div>

                    <div class="profile-card-body">
                        <?php if(session('success')): ?>
                            <div class="ui-alert ui-alert-success">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo e(session('success')); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if($errors->any()): ?>
                            <div class="ui-alert ui-alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <ul class="alert-list">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="tab-content" id="profileTabContent">
                            
                            <div class="tab-pane fade show active" id="data-diri" role="tabpanel"
                                aria-labelledby="data-diri-tab">
                                <form action="<?php echo e(route('siswa.profil.update')); ?>" method="POST"
                                    enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <input type="file" name="foto_profil" id="foto_profil" class="hidden"
                                        accept="image/*">

                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label class="form-label">Nama Lengkap</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-user"></i></span>
                                                <input type="text" name="nama" class="form-field"
                                                    value="<?php echo e(old('nama', $user->nama)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                                <input type="email" name="email" class="form-field"
                                                    value="<?php echo e(old('email', $user->email)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">NISN</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i
                                                        class="fas fa-address-card"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e($user->nisn); ?>" readonly>
                                            </div>
                                            <small class="form-help">NISN tidak dapat diubah.</small>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-venus-mars"></i></span>
                                                <select name="jenis_kelamin" class="form-field" required>
                                                    <option value="L" <?php echo e(old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : ''); ?>>Laki-laki</option>
                                                    <option value="P" <?php echo e(old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : ''); ?>>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Kelas</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-chalkboard"></i></span>
                                                <input type="text" name="kelas" class="form-field"
                                                    value="<?php echo e(old('kelas', $user->kelas)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Jurusan</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-graduation-cap"></i></span>
                                                <input type="text" name="jurusan" class="form-field"
                                                    value="<?php echo e(old('jurusan', $user->jurusan)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-full">
                                            <label class="form-label">Sekolah / Instansi</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-school"></i></span>
                                                <input type="text" name="sekolah" class="form-field"
                                                    value="<?php echo e(old('sekolah', $user->sekolah)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-full">
                                            <label class="form-label">Perusahaan Tempat Magang</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-building"></i></span>
                                                <input type="text" name="perusahaan" class="form-field"
                                                    value="<?php echo e(old('perusahaan', $user->perusahaan)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">No. WhatsApp / HP</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fab fa-whatsapp"></i></span>
                                                <input type="text" name="no_hp" class="form-field"
                                                    value="<?php echo e(old('no_hp', $user->no_hp)); ?>" placeholder="Contoh: 08123456789">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">NPSN Sekolah</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e($user->npsn); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tipe Magang</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-briefcase"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e(ucfirst($user->tipe_magang)); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tahun Ajaran</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e($user->tahunAjaran->tahun_ajaran ?? '-'); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-clock"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e($user->tgl_mulai_magang ? \Carbon\Carbon::parse($user->tgl_mulai_magang)->format('d M Y') : '-'); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Selesai</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon input-icon-muted"><i class="fas fa-calendar-check"></i></span>
                                                <input type="text" class="form-field form-field-readonly"
                                                    value="<?php echo e($user->tgl_selesai_magang ? \Carbon\Carbon::parse($user->tgl_selesai_magang)->format('d M Y') : '-'); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-save">
                                            <i class="fas fa-save"></i> SIMPAN PERUBAHAN
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            
                            <div class="tab-pane fade" id="pembimbing" role="tabpanel" aria-labelledby="pembimbing-tab">
                                <div class="supervisor-grid">
                                    
                                    <div class="supervisor-card">
                                        <div class="supervisor-badge">GURU PEMBIMBING</div>
                                        <?php if($user->guru): ?>
                                            <div class="supervisor-avatar">
                                                <img src="<?php echo e(asset('assets/img/default-avatar.png')); ?>" alt="Guru">
                                            </div>
                                            <h5 class="supervisor-name"><?php echo e($user->guru->nama); ?></h5>
                                            <p class="supervisor-id">ID: <?php echo e($user->guru->id_guru); ?></p>
                                            
                                            <div class="supervisor-contacts">
                                                <div class="s-contact-item">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span><?php echo e($user->guru->no_hp ?? '-'); ?></span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <span><?php echo e($user->guru->email); ?></span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span><?php echo e($user->guru->jabatan); ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-supervisor">
                                                <i class="fas fa-user-slash"></i>
                                                <p>Belum ada Guru Pembimbing yang ditugaskan.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    
                                    <div class="supervisor-card field-supervisor">
                                        <div class="supervisor-badge">PEMBIMBING LAPANGAN</div>
                                        <?php if($user->pembimbing): ?>
                                            <div class="supervisor-avatar">
                                                <img src="<?php echo e(asset('assets/img/default-avatar.png')); ?>" alt="Dosen">
                                            </div>
                                            <h5 class="supervisor-name"><?php echo e($user->pembimbing->nama); ?></h5>
                                            <p class="supervisor-id">ID: <?php echo e($user->pembimbing->id_pembimbing); ?></p>
                                            
                                            <div class="supervisor-contacts">
                                                <div class="s-contact-item">
                                                    <i class="fab fa-whatsapp"></i>
                                                    <span><?php echo e($user->pembimbing->no_telp ?? '-'); ?></span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-envelope"></i>
                                                    <span><?php echo e($user->pembimbing->email); ?></span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-building"></i>
                                                    <span><?php echo e($user->pembimbing->instansi); ?></span>
                                                </div>
                                                <div class="s-contact-item">
                                                    <i class="fas fa-briefcase"></i>
                                                    <span><?php echo e($user->pembimbing->jabatan); ?></span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-supervisor">
                                                <i class="fas fa-user-slash"></i>
                                                <p>Belum ada Pembimbing Lapangan yang ditugaskan.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                <form action="<?php echo e(route('siswa.profil.password')); ?>" method="POST" class="security-section">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>

                                    <h6 class="section-title">UBAH PASSWORD</h6>

                                    <div class="form-group form-group-full">
                                        <label class="form-label">Password Saat Ini</label>
                                        <div class="input-wrapper">
                                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="current_password" class="form-field"
                                                placeholder="Masukkan password sekarang" required>
                                        </div>
                                    </div>

                                    <div class="form-grid mt-1">
                                        <div class="form-group">
                                            <label class="form-label">Password Baru</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-key"></i></span>
                                                <input type="password" name="new_password" class="form-field"
                                                    placeholder="Minimal 6 karakter" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Konfirmasi Password Baru</label>
                                            <div class="input-wrapper">
                                                <span class="input-icon"><i class="fas fa-check-double"></i></span>
                                                <input type="password" name="new_password_confirmation" class="form-field"
                                                    placeholder="Ulangi password baru" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn-save btn-warning-save">
                                            <i class="fas fa-shield-alt"></i> PERBARUI PASSWORD
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/siswa/profil-siswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.nav.siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/siswa/profil.blade.php ENDPATH**/ ?>