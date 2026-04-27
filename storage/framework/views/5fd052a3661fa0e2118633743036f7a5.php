<?php $__env->startSection('title', 'Daftar Guru Pembimbing - Pimpinan Dashboard'); ?>
<?php $__env->startSection('body-class', 'dashboard-page pimpinan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/guru.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/siswa-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/pimpinan/guru.js')); ?>"></script>
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
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Guru Pembimbing</h5>
                    <small>Total <?php echo e($guru->total()); ?> guru terdaftar.</small>
                </div>                
            </div>

            <!-- Data Table Area -->
            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>NIP</th>
                            <th>Siswa Bimbingan</th>
                            <th style="width: 100px;" class="text-end">Aksi</th>
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
                                        class="badge <?php echo e($item->siswas->count() > 0 ? 'bg-success-soft text-success' : 'bg-secondary-soft text-muted'); ?>"
                                        style="border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 700;">
                                        <?php echo e($item->siswas->count()); ?> Siswa
                                    </span>
                                </td>
                                <td data-label="Aksi" class="text-end">
                                    <div class="action-group justify-content-end">
                                        <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailGuru" data-nama="<?php echo e($item->nama); ?>"
                                            data-email="<?php echo e($item->email); ?>" data-id_guru="<?php echo e($item->id_guru); ?>"
                                            data-jabatan="<?php echo e($item->jabatan); ?>" data-sekolah="<?php echo e($item->sekolah); ?>" 
                                            data-siswas="<?php echo e(json_encode($item->siswas->map(function ($s) {
                                                return ['nama' => $s->nama, 'nisn' => $s->nisn]; 
                                            }))); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p>Belum ada data guru pembimbing.</p>
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

    <?php echo $__env->make('pimpinan.guru_modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/guru.blade.php ENDPATH**/ ?>