<?php $__env->startSection('title', 'Data Master - Monitoring Siswa Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page admin-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/master-data.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container" data-active-tab="<?php echo e(session('active_tab')); ?>">
        <div class="admin-content-wrapper">

            <div class="management-header mb-4">
                <div class="header-title">
                    <h5 class="fw-bold"><i class="fas fa-database me-2 text-primary"></i> Data Master</h5>
                    <p class="text-muted">Kelola basis data sekolah dan periode tahun ajaran sistem.</p>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px; background: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1"
                    style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center" id="sekolah-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-sekolah" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-school"></i>
                        <span>Master Sekolah</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="periode-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-periode" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Tahun Ajaran</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="informasi-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-informasi" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-info-circle"></i>
                        <span>Informasi Dashboard</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="laporan-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-laporan" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-file-pdf"></i>
                        <span>Konfigurasi Laporan</span>
                    </button>
                </div>
            </div>

            <div class="tab-content" id="masterDataTabsContent">
                
                <div class="tab-pane fade show active" id="pane-sekolah" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Daftar Sekolah Terdaftar</h6>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSekolah">
                            <i class="fas fa-plus me-2"></i> Tambah Sekolah
                        </button>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">NPSN</th>
                                    <th>Nama Sekolah</th>
                                    <th>Jenjang</th>
                                    <th>Status</th>
                                    <th>Alamat</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sekolahs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary"><?php echo e($s->npsn); ?></td>
                                        <td><?php echo e($s->nama_sekolah); ?></td>
                                        <td><span class="badge bg-info-subtle text-info px-3"><?php echo e($s->jenjang); ?></span></td>
                                        <td>
                                            <?php if($s->status == 'Negeri'): ?>
                                                <span class="badge bg-success-subtle text-success px-3">Negeri</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning px-3">Swasta</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small"><?php echo e(Str::limit($s->alamat, 50)); ?></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border-0 rounded-circle btn-edit-sekolah" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditSekolah"
                                                data-id="<?php echo e($s->id_sekolah); ?>"
                                                data-npsn="<?php echo e($s->npsn); ?>"
                                                data-nama="<?php echo e($s->nama_sekolah); ?>"
                                                data-jenjang="<?php echo e($s->jenjang); ?>"
                                                data-status="<?php echo e($s->status); ?>"
                                                data-alamat="<?php echo e($s->alamat); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="<?php echo e(route('admin.destroySekolah', $s->id_sekolah)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-circle" onclick="return confirm('Hapus sekolah ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data sekolah.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-periode" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Daftar Tahun Ajaran</h6>
                        <button class="btn btn-primary px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">
                            <i class="fas fa-plus me-2"></i> Tambah Periode
                        </button>
                    </div>

                    <div class="data-table-wrapper">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Tahun Ajaran</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $tahunAjarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4 fw-bold"><?php echo e($ta->tahun_ajaran); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($ta->tgl_mulai)->format('d M Y')); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($ta->tgl_selesai)->format('d M Y')); ?></td>
                                        <td>
                                            <?php if($ta->status == 'aktif'): ?>
                                                <span class="badge bg-success px-3">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary px-3">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary border-0 rounded-circle btn-edit-periode"
                                                data-bs-toggle="modal" data-bs-target="#modalEditPeriode"
                                                data-id="<?php echo e($ta->id_tahun_ajaran); ?>"
                                                data-tahun="<?php echo e($ta->tahun_ajaran); ?>"
                                                data-mulai="<?php echo e($ta->tgl_mulai); ?>"
                                                data-selesai="<?php echo e($ta->tgl_selesai); ?>"
                                                data-status="<?php echo e($ta->status); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="<?php echo e(route('admin.destroyPeriode', $ta->id_tahun_ajaran)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-circle" onclick="return confirm('Hapus periode ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data periode.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <!-- REMOVED PREMATURE CLOSING DIV HERE -->

                
                <div class="tab-pane fade" id="pane-informasi" role="tabpanel">
                    <div class="row g-4">
                        <!-- Form Informasi Dashboard -->
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    <h6 class="info-section-title">
                                        <i><i class="fas fa-edit"></i></i>
                                        Konfigurasi Dashboard Utama
                                    </h6>
                                    
                                    <form action="<?php echo e(route('admin.updateInformasi')); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        
                                        <div class="mb-4">
                                            <label class="label-premium">Nama Fakultas / Lembaga</label>
                                            <input type="text" name="nama_fakultas" class="form-control input-premium" value="<?php echo e($informasi->nama_fakultas); ?>" placeholder="Masukkan nama lembaga">
                                        </div>

                                        <div class="mb-4">
                                            <label class="label-premium">Deskripsi Banner</label>
                                            <textarea name="deskripsi_banner" class="form-control input-premium" rows="2" placeholder="Deskripsi singkat yang tampil di hero section"><?php echo e($informasi->deskripsi_banner); ?></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="label-premium">Visi Lembaga</label>
                                            <textarea name="visi" class="form-control input-premium" rows="4" placeholder="Masukkan visi lembaga secara lengkap"><?php echo e($informasi->visi); ?></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="label-premium d-flex justify-content-between align-items-center mb-3">
                                                <span class="d-flex align-items-center gap-2">
                                                    <i class="fas fa-list-ol text-primary"></i> Misi Lembaga
                                                </span>
                                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" onclick="addMisiField()">
                                                    <i class="fas fa-plus me-1"></i> Tambah Misi
                                                </button>
                                            </label>
                                            <div id="misi-container" class="pe-2">
                                                <?php $__empty_1 = true; $__currentLoopData = $informasi->misi_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <div class="d-flex misi-item-premium mb-3">
                                                        <div class="misi-number"><?php echo e($index + 1); ?></div>
                                                        <textarea name="misi[]" class="misi-textarea" rows="2" placeholder="Tuliskan misi..."><?php echo e($misi); ?></textarea>
                                                        <div class="d-flex align-items-center px-2 border-start">
                                                            <button type="button" class="btn-remove-misi" onclick="this.closest('.misi-item-premium').remove(); updateMisiNumbers();" title="Hapus Misi">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <div class="d-flex misi-item-premium mb-3">
                                                        <div class="misi-number">1</div>
                                                        <textarea name="misi[]" class="misi-textarea" rows="2" placeholder="Masukkan misi pertama..."></textarea>
                                                        <div class="d-flex align-items-center px-2 border-start">
                                                            <button type="button" class="btn-remove-misi" onclick="this.closest('.misi-item-premium').remove(); updateMisiNumbers();">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="label-premium">Sejarah Singkat</label>
                                            <textarea name="sejarah" class="form-control input-premium" rows="5" placeholder="Sejarah singkat fakultas/lembaga"><?php echo e($informasi->sejarah); ?></textarea>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="label-premium"><i class="fas fa-clock me-1"></i> Jam Operasional</label>
                                                <input type="text" name="jam_operasional" class="form-control input-premium" value="<?php echo e($informasi->jam_operasional); ?>" placeholder="Contoh: 08:00 - 16:00">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="label-premium"><i class="fas fa-info-circle me-1"></i> Keterangan Jam</label>
                                                <input type="text" name="deskripsi_jam_operasional" class="form-control input-premium" value="<?php echo e($informasi->deskripsi_jam_operasional); ?>" placeholder="Contoh: Senin - Jumat">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="label-premium"><i class="fas fa-map-marker-alt me-1"></i> Alamat Lokasi</label>
                                                <input type="text" name="alamat_lokasi" class="form-control input-premium" value="<?php echo e($informasi->alamat_lokasi); ?>" placeholder="Alamat lengkap">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="label-premium"><i class="fas fa-link me-1"></i> Link Google Maps</label>
                                                <input type="text" name="link_maps" class="form-control input-premium" value="<?php echo e($informasi->link_maps); ?>" placeholder="https://goo.gl/maps/...">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-4">
                                                <label class="label-premium"><i class="fas fa-envelope me-1"></i> Email</label>
                                                <input type="email" name="email_kontak" class="form-control input-premium" value="<?php echo e($informasi->email_kontak); ?>" placeholder="email@domain.com">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="label-premium"><i class="fas fa-phone-alt me-1"></i> Telepon</label>
                                                <input type="text" name="telp_kontak" class="form-control input-premium" value="<?php echo e($informasi->telp_kontak); ?>" placeholder="0812...">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="label-premium"><i class="fas fa-globe me-1"></i> Website</label>
                                                <input type="text" name="website_kontak" class="form-control input-premium" value="<?php echo e($informasi->website_kontak); ?>" placeholder="www.domain.com">
                                            </div>
                                        </div>

                                        <div class="pt-3 border-top d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                                <i class="fas fa-save me-2"></i> Simpan Konfigurasi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Kelola Program Studi -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h6 class="info-section-title mb-0">
                                            <i><i class="fas fa-graduation-cap"></i></i>
                                            Program Studi
                                        </h6>
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahProdi">
                                            <i class="fas fa-plus me-1"></i> Tambah
                                        </button>
                                    </div>

                                    <div class="prodi-container" style="max-height: 800px; overflow-y: auto; padding-right: 5px;">
                                        <?php $__empty_1 = true; $__currentLoopData = $programStudis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <div class="prodi-list-item">
                                                <div class="prodi-info">
                                                    <span class="prodi-dot" style="background-color: <?php echo e($prodi->warna_dot); ?>;"></span>
                                                    <div>
                                                        <div class="prodi-name"><?php echo e($prodi->nama); ?></div>
                                                        <div class="prodi-meta">
                                                            <span class="text-primary fw-bold"><?php echo e($prodi->jenjang); ?></span>
                                                            <span class="mx-2 text-muted">|</span>
                                                            <span>Urutan: <?php echo e($prodi->urutan); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="prodi-status-badge <?php echo e($prodi->aktif ? 'aktif' : 'nonaktif'); ?>">
                                                        <?php echo e($prodi->aktif ? 'Aktif' : 'Nonaktif'); ?>

                                                    </span>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                                            <li>
                                                                <a class="dropdown-item btn-edit-prodi" href="#"
                                                                    data-id="<?php echo e($prodi->id); ?>"
                                                                    data-nama="<?php echo e($prodi->nama); ?>"
                                                                    data-jenjang="<?php echo e($prodi->jenjang); ?>"
                                                                    data-warna="<?php echo e($prodi->warna_dot); ?>"
                                                                    data-urutan="<?php echo e($prodi->urutan); ?>"
                                                                    data-aktif="<?php echo e($prodi->aktif ? '1' : '0'); ?>"
                                                                    data-bs-toggle="modal" data-bs-target="#modalEditProdi">
                                                                    <i class="fas fa-edit me-2 text-primary"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="<?php echo e(route('admin.destroyProdi', $prodi->id)); ?>" method="POST" class="d-inline">
                                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus program studi ini?')">
                                                                        <i class="fas fa-trash-alt me-2"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <div class="text-center py-5">
                                                <img src="<?php echo e(asset('assets/images/empty-state.png')); ?>" class="mb-3" style="width: 120px; opacity: 0.5;">
                                                <p class="text-muted">Data program studi tidak ditemukan.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-laporan" role="tabpanel">
                    <form action="<?php echo e(route('admin.updateKonfigurasiLaporan')); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        
                        <div class="alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" style="background: rgba(59, 130, 246, 0.1); color: #1e40af;">
                            <i class="fas fa-info-circle fs-4 me-3"></i>
                            <div>
                                <small>Gunakan placeholder untuk data dinamis: <strong>{sekolah}</strong> untuk nama sekolah, <strong>{tahun}</strong> untuk tahun ajaran, <strong>{tgl_mulai}</strong> dan <strong>{tgl_selesai}</strong> untuk periode magang.</small>
                            </div>
                        </div>

                        <!-- Sub Tabs Navigation (Unified Design) -->
                        <div class="tabs-wrapper mb-4 px-lg-5">
                            <div class="tabs-nav d-flex w-100 gap-2 p-1" style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                                <button class="tab-button active flex-fill justify-content-center" id="sub-rekap-tab" data-bs-toggle="pill" data-bs-target="#sub-rekap" type="button" role="tab" style="border-radius: 12px;">
                                    <i class="fas fa-file-invoice"></i>
                                    <span>Laporan & Absensi</span>
                                </button>
                                <button class="tab-button flex-fill justify-content-center" id="sub-penilaian-tab" data-bs-toggle="pill" data-bs-target="#sub-penilaian" type="button" role="tab" style="border-radius: 12px;">
                                    <i class="fas fa-star"></i>
                                    <span>Penilaian</span>
                                </button>
                                <button class="tab-button flex-fill justify-content-center" id="sub-sertifikat-tab" data-bs-toggle="pill" data-bs-target="#sub-sertifikat" type="button" role="tab" style="border-radius: 12px;">
                                    <i class="fas fa-certificate"></i>
                                    <span>Sertifikat Magang</span>
                                </button>
                            </div>
                        </div>

                        <div class="tab-content border-0" id="laporanSubTabsContent">
                            <!-- TAB 1: LAPORAN & ABSENSI -->
                            <div class="tab-pane fade show active" id="sub-rekap" role="tabpanel">
                                <div class="row g-4">
                                    <?php $__currentLoopData = $laporans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(in_array($lap->tipe_laporan, ['absensi_individu', 'absensi_kelompok', 'kegiatan_mingguan'])): ?>
                                            <div class="col-lg-6">
                                                <?php echo $__env->make('admin.partials.card_laporan', ['index' => $index, 'lap' => $lap], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <!-- TAB 2: PENILAIAN -->
                            <div class="tab-pane fade" id="sub-penilaian" role="tabpanel">
                                <div class="row g-4">
                                    <?php $__currentLoopData = $laporans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(in_array($lap->tipe_laporan, ['penilaian_guru', 'penilaian_pembimbing'])): ?>
                                            <div class="col-lg-6">
                                                <?php echo $__env->make('admin.partials.card_laporan', ['index' => $index, 'lap' => $lap], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>

                            <!-- TAB 3: SERTIFIKAT -->
                            <div class="tab-pane fade" id="sub-sertifikat" role="tabpanel">
                                <?php $__currentLoopData = $laporans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($lap->tipe_laporan == 'sertifikat'): ?>
                                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="icon-box-premium" style="background: rgba(15, 23, 42, 0.05); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #1e40af;">
                                                        <i class="fas fa-certificate"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0">Konfigurasi Sertifikat Magang</h6>
                                                        <small class="text-muted">Desain, warna, dan konten sertifikat</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body p-4">
                                                <input type="hidden" name="konfigurasi[<?php echo e($index); ?>][id]" value="<?php echo e($lap->id); ?>">
                                                
                                                <div class="row g-4">
                                                    <div class="col-md-7">
                                                        <div class="mb-3">
                                                            <label class="label-premium">Judul Sertifikat</label>
                                                            <input type="text" name="konfigurasi[<?php echo e($index); ?>][header_1]" class="form-control input-premium" value="<?php echo e($lap->header_1); ?>" placeholder="Contoh: SERTIFIKAT">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="label-premium">Sub-judul (Diberikan Kepada)</label>
                                                            <input type="text" name="konfigurasi[<?php echo e($index); ?>][header_2]" class="form-control input-premium" value="<?php echo e($lap->header_2); ?>" placeholder="Contoh: DIBERIKAN KEPADA :">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="label-premium">Template Kalimat Isi</label>
                                                            <textarea name="konfigurasi[<?php echo e($index); ?>][template_isi]" class="form-control input-premium" rows="6"><?php echo e($lap->template_isi); ?></textarea>
                                                            <small class="text-muted mt-1 d-block">
                                                                Dapat menggunakan <strong>{tgl_mulai}</strong> dan <strong>{tgl_selesai}</strong>.
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="card bg-light border-0 rounded-4 p-3 mb-4">
                                                            <h6 class="fw-bold mb-3">Tanda Tangan (Footer)</h6>
                                                            <div class="mb-2">
                                                                <label class="small fw-semibold text-muted">Jabatan Penyetuju</label>
                                                                <input type="text" name="konfigurasi[<?php echo e($index); ?>][header_3]" class="form-control form-control-sm" value="<?php echo e($lap->header_3); ?>" placeholder="Contoh: Dekan,">
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="small fw-semibold text-muted">Nama Penandatangan</label>
                                                                <input type="text" name="konfigurasi[<?php echo e($index); ?>][header_4]" class="form-control form-control-sm" value="<?php echo e($lap->header_4); ?>" placeholder="Contoh: Prof. Dr. Erwin...">
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="small fw-semibold text-muted">NIP Penandatangan</label>
                                                                <input type="text" name="konfigurasi[<?php echo e($index); ?>][header_5]" class="form-control form-control-sm" value="<?php echo e($lap->header_5); ?>" placeholder="Contoh: NIP. 1974...">
                                                            </div>
                                                        </div>

                                                        <h6 class="fw-bold mb-3">Palet Warna</h6>
                                                        <div class="row g-2">
                                                            <div class="col-4">
                                                                <label class="small fw-semibold text-muted d-block mb-1">Utama</label>
                                                                <input type="color" name="konfigurasi[<?php echo e($index); ?>][color_primary]" class="form-control form-control-color w-100 border-0 p-0 rounded-3" value="<?php echo e($lap->color_primary ?: '#1a56db'); ?>" style="height: 40px;">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="small fw-semibold text-muted d-block mb-1">Aksen</label>
                                                                <input type="color" name="konfigurasi[<?php echo e($index); ?>][color_secondary]" class="form-control form-control-color w-100 border-0 p-0 rounded-3" value="<?php echo e($lap->color_secondary ?: '#fbc02d'); ?>" style="height: 40px;">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="small fw-semibold text-muted d-block mb-1">Background</label>
                                                                <input type="color" name="konfigurasi[<?php echo e($index); ?>][background_color]" class="form-control form-control-color w-100 border-0 p-0 rounded-3" value="<?php echo e($lap->background_color ?: '#fdfaf2'); ?>" style="height: 40px;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                                <div class="mt-4 pt-4 border-top">
                                                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-eye me-2"></i> Real-time Preview</h6>
                                                    <div class="bg-light rounded-4 p-1 shadow-sm" style="height: 500px; border: 1px solid #e2e8f0; overflow: hidden;">
                                                        <iframe src="<?php echo e(route('admin.konfigurasiLaporan.previewSertifikat')); ?>#toolbar=0" width="100%" height="100%" style="border: none; border-radius: 12px; background: #fff;"></iframe>
                                                    </div>
                                                    <div class="mt-2 d-flex align-items-center gap-2 text-muted small">
                                                        <div class="spinner-grow spinner-grow-sm text-primary" role="status" style="width: 10px; height: 10px;"></div>
                                                        <span>Preview akan diperbarui otomatis setelah Anda menekan <strong>Simpan Perubahan</strong>.</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end mb-5">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm py-2">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Modal Tambah Sekolah -->
    <div class="modal fade" id="modalTambahSekolah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Sekolah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.storeSekolah')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NPSN</label>
                            <input type="text" name="npsn" class="form-control rounded-3" placeholder="Masukkan 8 digit NPSN" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" class="form-control rounded-3" placeholder="Contoh: SMK Negeri 1 Palembang" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <select name="jenjang" class="form-select rounded-3" required>
                                    <option value="SMK">SMK</option>
                                    <option value="SMA">SMA</option>
                                    <option value="MA">MA</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3" placeholder="Alamat lengkap sekolah"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Sekolah -->
    <div class="modal fade" id="modalEditSekolah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Data Sekolah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditSekolah" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NPSN</label>
                            <input type="text" name="npsn" id="edit_sekolah_npsn" class="form-control rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" id="edit_sekolah_nama" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <select name="jenjang" id="edit_sekolah_jenjang" class="form-select rounded-3" required>
                                    <option value="SMK">SMK</option>
                                    <option value="SMA">SMA</option>
                                    <option value="MA">MA</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" id="edit_sekolah_status" class="form-select rounded-3" required>
                                    <option value="Negeri">Negeri</option>
                                    <option value="Swasta">Swasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" id="edit_sekolah_alamat" class="form-control rounded-3" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Periode -->
    <div class="modal fade" id="modalTambahPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.storePeriode')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" class="form-control rounded-3" placeholder="Contoh: 2023/2024" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select rounded-3" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Periode -->
    <div class="modal fade" id="modalEditPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPeriode" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="edit_ta_tahun" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" id="edit_ta_mulai" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" id="edit_ta_selesai" class="form-control rounded-3" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_ta_status" class="form-select rounded-3" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Periode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Prodi -->
    <div class="modal fade" id="modalTambahProdi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo e(route('admin.storeProdi')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi</label>
                            <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Sistem Informasi" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <input type="text" name="jenjang" class="form-control rounded-3" placeholder="Contoh: S1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Warna Label</label>
                                <input type="color" name="warna_dot" class="form-control rounded-3 form-control-color w-100" value="#4e73df" required>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Urutan</label>
                            <input type="number" name="urutan" class="form-control rounded-3" value="0">
                            <small class="text-muted">Semakin kecil, semakin atas.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Prodi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Prodi -->
    <div class="modal fade" id="modalEditProdi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Program Studi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditProdi" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Program Studi</label>
                            <input type="text" name="nama" id="edit_prodi_nama" class="form-control rounded-3" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenjang</label>
                                <input type="text" name="jenjang" id="edit_prodi_jenjang" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Warna Label</label>
                                <input type="color" name="warna_dot" id="edit_prodi_warna" class="form-control rounded-3 form-control-color w-100" required>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Urutan</label>
                                <input type="number" name="urutan" id="edit_prodi_urutan" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="aktif" id="edit_prodi_aktif" class="form-select rounded-3">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Update Prodi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/admin/masterData.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/admin/master_data.blade.php ENDPATH**/ ?>