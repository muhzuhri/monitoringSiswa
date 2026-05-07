<?php $__env->startSection('title', 'Daftar Pembimbing Lapangan - Pimpinan Dashboard'); ?>
<?php $__env->startSection('body-class', 'dashboard-page pimpinan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/pembimbing.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/pimpinan/pembimbing.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container">
        <!-- Global Navigation Tabs: Admin, Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav d-flex w-100 gap-3">
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

        <div class="admin-content-wrapper shadow-sm" style="border-radius: 24px; background: #fff; overflow: hidden;">
            <!-- Header -->
            <div class="management-header p-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: #fdfdfd;">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon" style="background: linear-gradient(135deg, #059669 0%, #065f46 100%); color: #fff; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.5rem;">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Daftar Pembimbing Lapangan</h5>
                        <p class="text-muted small mb-0">Total <strong><?php echo e($pembimbing->total()); ?></strong> pembimbing dari berbagai instansi mitra.</p>
                    </div>
                </div>                
            </div>

            <!-- Data Table Area -->
            <div class="data-table-wrapper px-4 pb-4">
                <table class="main-table mt-3">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>Jabatan</th>
                            <th class="text-center">Siswa Bimbingan</th>
                            <th style="width: 100px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $pembimbing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td data-label="#"><?php echo e($pembimbing->firstItem() + $index); ?></td>
                                <td data-label="Nama" class="fw-bold text-dark"><?php echo e($item->nama); ?></td>
                                <td data-label="Email" class="text-primary"><?php echo e($item->email); ?></td>
                                <td data-label="Jabatan"><?php echo e($item->jabatan); ?></td>
                                <td data-label="Siswa Bimbingan" class="text-center">
                                    <span
                                        class="badge <?php echo e($item->siswas->count() > 0 ? 'bg-success-soft text-success' : 'bg-secondary-soft text-muted'); ?>"
                                        style="border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 700;">
                                        <?php echo e($item->siswas->count()); ?> Siswa
                                    </span>
                                </td>
                                <td data-label="Aksi" class="text-end">
                                    <div class="action-group justify-content-end">
                                        <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailDosen" data-nama="<?php echo e($item->nama); ?>"
                                            data-email="<?php echo e($item->email); ?>" data-jabatan="<?php echo e($item->jabatan); ?>"
                                            data-instansi="<?php echo e($item->instansi); ?>" data-telp="<?php echo e($item->no_telp); ?>"
                                            data-siswas="<?php echo e(json_encode(
                                                $item->siswas->map(function ($s) {
                                                    return [
                                                        'nama' => $s->nama, 
                                                        'nisn' => $s->nisn,
                                                        'id_tahun_ajaran' => $s->id_tahun_ajaran,
                                                        'tahun_ajaran' => $s->tahunAjaran->tahun_ajaran ?? '-'
                                                    ];
                                                }),
                                            )); ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                        <p>Belum ada data pembimbing lapangan.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($pembimbing->hasPages()): ?>
                <div class="pagination-container px-4 pb-4">
                    <?php echo e($pembimbing->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('pimpinan.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/pembimbing.blade.php ENDPATH**/ ?>