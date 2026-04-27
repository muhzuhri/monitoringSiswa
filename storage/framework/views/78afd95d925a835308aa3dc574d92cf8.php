<?php $__env->startSection('title', 'Manajemen Admin - Pimpinan Dashboard'); ?>
<?php $__env->startSection('body-class', 'dashboard-page pimpinan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/admin.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/siswa-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/pimpinan/admin.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container">
        
        <!-- Global Navigation Tabs: Admin, Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper">
            <div class="tabs-nav">
                <a href="<?php echo e(route('pimpinan.admin')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('pimpinan.admin') ? 'active' : ''); ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
                <a href="<?php echo e(route('pimpinan.siswa')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('pimpinan.siswa') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="<?php echo e(route('pimpinan.guru')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('pimpinan.guru') ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="<?php echo e(route('pimpinan.pembimbing')); ?>" class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('pimpinan.pembimbing') ? 'active' : ''); ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">

            
            <div class="management-header">
                <div class="header-title">
                    <h5>Manajemen Admin</h5>
                    <p>Kelola data akun admin sistem monitoring.</p>
                </div>
                <div class="header-actions d-flex gap-3 align-items-center">
                    <form action="<?php echo e(route('pimpinan.admin')); ?>" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input
                                type="text"
                                name="search"
                                value="<?php echo e($search); ?>"
                                class="p-input with-icon"
                                placeholder="Cari Nama / Email..."
                                onchange="this.form.submit()"
                            >
                        </div>
                    </form>
                    <button class="btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
                        <i class="fas fa-plus"></i> Tambah Admin
                    </button>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 12px; border: none; background: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Identitas Admin</th>
                            <th>Role</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="cell-name"><?php echo e($adm->nama); ?></div>
                                    <div class="cell-sub"><i class="fas fa-envelope text-primary me-1 opacity-50"></i> <?php echo e($adm->email); ?></div>
                                </td>
                                <td>
                                    <span class="status-badge" style="background:#e0e7ff; color:#4338ca; padding: 5px 12px; border-radius:50px; font-weight:700; font-size:0.75rem;">
                                        <i class="fas fa-shield-alt me-1"></i> Admin
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="action-group justify-content-end">
                                        <button class="btn-icon btn-edit-soft btn-edit"
                                            data-bs-toggle="modal" data-bs-target="#modalEditAdmin"
                                            data-id="<?php echo e($adm->id_admin); ?>"
                                            data-nama="<?php echo e($adm->nama); ?>"
                                            data-email="<?php echo e($adm->email); ?>"
                                            title="Edit Admin">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo e(route('pimpinan.destroyAdmin', $adm->id_admin)); ?>" method="POST" class="d-inline form-delete">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="button" class="btn-icon btn-delete-soft btn-delete-trigger" title="Hapus Admin">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    Tidak ada data admin ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <?php echo e($admins->links()); ?>

            </div>

        </div>
    </div>

    
    <div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-primary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5 class="mb-0">Tambah Akun Admin</h5>
                            <p class="mb-0">Daftarkan akses admin sistem baru.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('pimpinan.storeAdmin')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-form-body">
                        <div class="detail-card p-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Nama Lengkap</label>
                                <input type="text" name="nama" class="p-input w-100" required placeholder="Masukkan nama admin">
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Email</label>
                                <input type="email" name="email" class="p-input w-100" required placeholder="Masukkan email aktif">
                            </div>
                            <div class="form-group mb-0">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Password</label>
                                <input type="password" name="password" class="p-input w-100" required minlength="6" placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-dark">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-dark">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5 class="mb-0">Edit Akun Admin</h5>
                            <p class="mb-0">Perbarui informasi kredensial admin.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditAdmin" method="POST" data-update-url="<?php echo e(route('pimpinan.updateAdmin', ['id' => ':id'])); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-form-body">
                        <div class="detail-card p-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Nama Lengkap</label>
                                <input type="text" name="nama" id="edit_nama" class="p-input w-100" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Email</label>
                                <input type="email" name="email" id="edit_email" class="p-input w-100" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Password <small class="text-muted fw-normal">(Opsional)</small></label>
                                <input type="password" name="password" class="p-input w-100" minlength="6" placeholder="Kosongkan jika tidak diubah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-sync-alt me-2"></i>Update Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/admin.blade.php ENDPATH**/ ?>