

<?php $__env->startSection('title', 'Manajemen Guru - Monitoring Siswa Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page admin-page'); ?>

<?php $__env->startSection('body'); ?>
    <?php $__env->startPush('styles'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-guru.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-modals.css')); ?>">
    <?php $__env->stopPush(); ?>

    <div class="management-container">
        
        <!-- Global Navigation Tabs: Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav d-flex w-100 gap-3">
                <a href="<?php echo e(route('admin.kelolaSiswa')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaSiswa') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="<?php echo e(route('admin.kelolaGuru')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaGuru') ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="<?php echo e(route('admin.kelolaPembimbing')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaPembimbing') ? 'active' : ''); ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Guru Pembimbing</h5>
                    <small>Total <?php echo e($guru->total()); ?> guru terdaftar.</small>
                </div>
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>

            <!-- Notifications -->
            <?php if(session('success')): ?>
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Data Table Area -->
            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th class="col-w-50">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>NIP</th>
                            <th>Siswa Bimbingan</th>
                            <th class="col-w-160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $guru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td data-label="#"><?php echo e($guru->firstItem() + $index); ?></td>
                                            <td data-label="Nama"><?php echo e($item->nama); ?></td>
                                            <td data-label="Email"><?php echo e($item->email); ?></td>
                                            <td data-label="NIP"><?php echo e($item->id_guru); ?></td>
                                            <td data-label="Siswa Bimbingan">
                                                <span
                                                    class="badge-custom <?php echo e($item->siswas->count() > 0 ? 'badge-success-soft' : 'badge-secondary-soft'); ?>">
                                                    <?php echo e($item->siswas->count()); ?> Siswa
                                                </span>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="action-group">
                                                    <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetailGuru" data-nama="<?php echo e($item->nama); ?>"
                                                        data-email="<?php echo e($item->email); ?>" data-id_guru="<?php echo e($item->id_guru); ?>"
                                                        data-jabatan="<?php echo e($item->jabatan); ?>" data-sekolah="<?php echo e($item->sekolah); ?>"
                                                        data-no_hp="<?php echo e($item->no_hp); ?>" data-npsn="<?php echo e($item->npsn); ?>"
                                                        data-siswas="<?php echo e(json_encode($item->siswas->map(function ($s) {
                            return [
                                'nama' => $s->nama, 
                                'nisn' => $s->nisn, 
                                'id_periode' => $s->id_tahun_ajaran,
                                'periode' => $s->tahunAjaran->tahun_ajaran ?? 'N/A'
                            ]; }))); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-icon btn-edit-soft btn-edit" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditGuru" data-id="<?php echo e($item->id_guru); ?>"
                                                        data-nama="<?php echo e($item->nama); ?>" data-email="<?php echo e($item->email); ?>"
                                                        data-id_guru="<?php echo e($item->id_guru); ?>" data-jabatan="<?php echo e($item->jabatan); ?>"
                                                        data-sekolah="<?php echo e($item->sekolah); ?>" data-no_hp="<?php echo e($item->no_hp); ?>"
                                                        data-npsn="<?php echo e($item->npsn); ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-icon btn-delete-soft btn-delete-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#modalHapus"
                                                        data-url="<?php echo e(route('admin.destroyGuru', $item->id_guru)); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-muted text-center p-12">
                                    Belum ada data guru. Klik tombol <strong>Tambah Guru</strong> untuk membuat akun baru.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($guru->hasPages()): ?>
                <div class="pagination-container">
                    <?php echo e($guru->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Tambah Guru -->
    <div class="modal fade" id="modalTambahGuru" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="<?php echo e(route('admin.storeGuru')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header-primary">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-header-icon on-primary">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="modal-header-title">
                                <h5>Registrasi Guru Pembimbing</h5>
                                <p>Lengkapi formulir untuk mendaftarkan guru baru.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-form-body">
                        <div class="row g-4">
                            
                            <div class="col-md-6">
                                <span class="form-section-label">Informasi Akun & Personal</span>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="nama" class="p-input with-icon" required placeholder="Nama Lengkap & Gelar">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Email Resmi <span class="text-danger">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" class="p-input with-icon" required placeholder="email@sekolah.sch.id">
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Kata Sandi <span class="text-danger">*</span></label>
                                        <div class="p-input-wrapper">
                                            <div class="input-group">
                                                <input type="password" name="password" class="p-input" required placeholder="Sandi">
                                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted mb-1">Konfirmasi <span class="text-danger">*</span></label>
                                        <div class="p-input-wrapper">
                                            <div class="input-group">
                                                <input type="password" name="password_confirmation" class="p-input" required placeholder="Ulangi">
                                                <button class="btn btn-outline-secondary toggle-password" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted mb-1">No. WhatsApp</label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="text" name="no_hp" class="p-input with-icon" placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                            </div>

                            
                            <div class="col-md-6">
                                <span class="form-section-label">Identitas & Penugasan</span>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">NIP Resmi <span class="text-danger">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-id-card input-icon"></i>
                                        <input type="text" name="id_guru" class="p-input with-icon" required placeholder="Nomor Induk Pegawai">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Jabatan / Mata Pelajaran <span class="text-danger">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-briefcase input-icon"></i>
                                        <input type="text" name="jabatan" class="p-input with-icon" required placeholder="Contoh: Guru Informatika">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Cari NPSN Sekolah</label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-fingerprint input-icon"></i>
                                        <input type="text" name="npsn" id="reg_npsn_guru" class="p-input with-icon" placeholder="Ketik NPSN...">
                                    </div>
                                    <small id="reg_npsn_guru_msg" class="text-muted"></small>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted mb-1">Asal Instansi Sekolah <span class="text-danger">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-school input-icon"></i>
                                        <input type="text" name="sekolah" id="reg_sekolah_guru" class="p-input with-icon" required placeholder="Nama Sekolah">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-form-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom rounded-pill px-5">Daftarkan Guru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Guru -->
    <div class="modal fade" id="modalEditGuru" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditGuru" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header-warning">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-header-icon on-warning">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="modal-header-title">
                                <h5>Edit Profil Guru</h5>
                                <p>Perbarui informasi data guru pembimbing.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-form-body">
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nama" id="edit_nama" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Email Resmi</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" id="edit_email" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Ganti Kata Sandi (Opsional)</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password" class="p-input with-icon" placeholder="Isi jika ingin diubah">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-shield-alt input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" class="p-input with-icon"
                                            placeholder="Isi jika ingin diubah">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Jabatan</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-briefcase input-icon"></i>
                                    <input type="text" name="jabatan" id="edit_jabatan" class="p-input with-icon" required>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>NIP</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="id_guru" id="edit_id_guru" class="p-input with-icon" required>
                                </div>
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Sekolah</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-school input-icon"></i>
                                    <input type="text" name="sekolah" id="edit_sekolah" class="p-input with-icon" required>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>NPSN Sekolah</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-fingerprint input-icon"></i>
                                    <input type="text" name="npsn" id="edit_npsn" class="p-input with-icon">
                                </div>
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>No. HP / WhatsApp</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-phone-alt input-icon"></i>
                                <input type="text" name="no_hp" id="edit_hp" class="p-input with-icon">
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn-light-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-warning-custom">Perbarui Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Detail -->
    <div class="modal fade" id="modalDetailGuru" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-address-card"></i> Preview Profil Guru Pembimbing</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <div class="detail-grid">
                        <!-- Left Column: Personal -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-user-circle"></i> Informasi Personal</h6>
                            <div class="detail-p-item">
                                <label>Nama Lengkap</label>
                                <span id="det_nama">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>NIP Resmi</label>
                                <span id="det_id_guru">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Email Korespondensi</label>
                                <span id="det_email">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>No. WhatsApp</label>
                                <span id="det_no_hp">-</span>
                            </div>
                        </div>

                        <!-- Right Column: Academy -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-school"></i> Instansi & Bidang</h6>
                            <div class="detail-p-item">
                                <label>Asal Sekolah</label>
                                <span id="det_sekolah">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>NPSN Sekolah</label>
                                <span id="det_npsn">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Jabatan Guru</label>
                                <span id="det_jabatan">-</span>
                            </div>
                        </div>

                        <!-- Bottom Column: Supervised Students List -->
                        <div class="detail-section-card full-width">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="section-label mb-0"><i class="fas fa-users"></i> Daftar Siswa Bimbingan</h6>
                                <div class="filter-wrapper d-flex align-items-center gap-2">
                                    <label class="small text-muted mb-0">Filter Periode:</label>
                                    <select id="filter_periode" class="form-select form-select-sm" style="width: auto; border-radius: 8px;">
                                        <option value="all">Semua Periode</option>
                                        <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($p->id_tahun_ajaran); ?>"><?php echo e($p->tahun_ajaran); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div id="supervised_students_list" class="supervised-list">
                                <!-- Will be populated by JS -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-body-custom text-center pt-14">
                    <div class="delete-confirm-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h4 class="fw-800 mb-4-custom">Konfirmasi Hapus</h4>
                    <p class="text-muted mb-8-custom">Apakah Anda yakin ingin menghapus data ini?
                        Tindakan ini tidak dapat dibatalkan.</p>

                    <form id="formHapus" method="POST" class="flex-center-gap">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-danger-custom">Ya, Hapus Data</button>
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/admin/kelola-guru.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.nav.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/admin/kelolaGuru.blade.php ENDPATH**/ ?>