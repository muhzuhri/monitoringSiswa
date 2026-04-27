<?php $__env->startSection('title', 'Data Master - Monitoring Siswa Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page admin-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/master-data.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container">
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

            <ul class="nav nav-pills mb-4" id="masterDataTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="sekolah-tab" data-bs-toggle="pill" data-bs-target="#pane-sekolah" type="button" role="tab">
                        <i class="fas fa-school"></i> Master Sekolah
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="periode-tab" data-bs-toggle="pill" data-bs-target="#pane-periode" type="button" role="tab">
                        <i class="fas fa-calendar-alt"></i> Tahun Ajaran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="informasi-tab" data-bs-toggle="pill" data-bs-target="#pane-informasi" type="button" role="tab">
                        <i class="fas fa-info-circle"></i> Informasi Dashboard
                    </button>
                </li>
            </ul>

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
            </div>

                
                <div class="tab-pane fade" id="pane-informasi" role="tabpanel">
                    <div class="row g-4">
                        <!-- Form Informasi Dashboard -->
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                                    <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-edit me-2"></i> Edit Informasi Dashboard</h6>
                                </div>
                                <div class="card-body p-4">
                                    <form action="<?php echo e(route('admin.updateInformasi')); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Nama Fakultas / Lembaga</label>
                                            <input type="text" name="nama_fakultas" class="form-control rounded-3" value="<?php echo e($informasi->nama_fakultas); ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Deskripsi Banner</label>
                                            <textarea name="deskripsi_banner" class="form-control rounded-3" rows="2"><?php echo e($informasi->deskripsi_banner); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Visi</label>
                                            <textarea name="visi" class="form-control rounded-3" rows="2"><?php echo e($informasi->visi); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small d-flex justify-content-between">
                                                <span>Misi</span>
                                                <button type="button" class="btn btn-sm btn-light py-0" onclick="addMisiField()"><i class="fas fa-plus"></i></button>
                                            </label>
                                            <div id="misi-container">
                                                <?php $__empty_1 = true; $__currentLoopData = $informasi->misi_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <div class="input-group mb-2 misi-item">
                                                        <span class="input-group-text bg-light border-end-0"><?php echo e($index + 1); ?>.</span>
                                                        <textarea name="misi[]" class="form-control border-start-0" rows="1"><?php echo e($misi); ?></textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove()"><i class="fas fa-times"></i></button>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <div class="input-group mb-2 misi-item">
                                                        <span class="input-group-text bg-light border-end-0">1.</span>
                                                        <textarea name="misi[]" class="form-control border-start-0" rows="1"></textarea>
                                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove()"><i class="fas fa-times"></i></button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold text-muted small">Sejarah Singkat</label>
                                            <textarea name="sejarah" class="form-control rounded-3" rows="3"><?php echo e($informasi->sejarah); ?></textarea>
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-clock me-1"></i> Jam Operasional</label>
                                                <input type="text" name="jam_operasional" class="form-control rounded-3" value="<?php echo e($informasi->jam_operasional); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-info-circle me-1"></i> Ket. Jam</label>
                                                <input type="text" name="deskripsi_jam_operasional" class="form-control rounded-3" value="<?php echo e($informasi->deskripsi_jam_operasional); ?>">
                                            </div>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-map-marker-alt me-1"></i> Alamat Lokasi</label>
                                                <input type="text" name="alamat_lokasi" class="form-control rounded-3" value="<?php echo e($informasi->alamat_lokasi); ?>">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-link me-1"></i> Link Maps</label>
                                                <input type="text" name="link_maps" class="form-control rounded-3" value="<?php echo e($informasi->link_maps); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-envelope me-1"></i> Email</label>
                                                <input type="email" name="email_kontak" class="form-control rounded-3" value="<?php echo e($informasi->email_kontak); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-phone-alt me-1"></i> Telepon</label>
                                                <input type="text" name="telp_kontak" class="form-control rounded-3" value="<?php echo e($informasi->telp_kontak); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-muted small"><i class="fas fa-globe me-1"></i> Website</label>
                                                <input type="text" name="website_kontak" class="form-control rounded-3" value="<?php echo e($informasi->website_kontak); ?>">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-save me-2"></i> Simpan Perubahan Dasar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Kelola Program Studi -->
                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-graduation-cap me-2"></i> Program Studi</h6>
                                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahProdi">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                                <div class="card-body p-4">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $programStudis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prodi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="rounded-circle d-inline-block me-3 shadow-sm flex-shrink-0" style="width: 12px; height: 12px; background-color: <?php echo e($prodi->warna_dot); ?>; border: 2px solid white; outline: 1px solid #e2e8f0;"></span>
                                                                <div>
                                                                    <div class="fw-bold text-dark"><?php echo e($prodi->nama); ?></div>
                                                                    <div class="small text-muted"><?php echo e($prodi->jenjang); ?> <span class="mx-1">•</span> Urutan: <?php echo e($prodi->urutan); ?></div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if($prodi->aktif): ?>
                                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Aktif</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Nonaktif</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end" style="white-space: nowrap;">
                                                            <button class="btn btn-sm btn-light text-primary rounded-circle me-1 border-0 btn-edit-prodi" 
                                                                    data-id="<?php echo e($prodi->id); ?>"
                                                                    data-nama="<?php echo e($prodi->nama); ?>"
                                                                    data-jenjang="<?php echo e($prodi->jenjang); ?>"
                                                                    data-warna="<?php echo e($prodi->warna_dot); ?>"
                                                                    data-urutan="<?php echo e($prodi->urutan); ?>"
                                                                    data-aktif="<?php echo e($prodi->aktif ? '1' : '0'); ?>"
                                                                    data-bs-toggle="modal" data-bs-target="#modalEditProdi">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="<?php echo e(route('admin.destroyProdi', $prodi->id)); ?>" method="POST" class="d-inline">
                                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle border-0" onclick="return confirm('Hapus program studi ini?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data program studi.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ... (Existing Event Listeners for Sekolah and Periode) ...
                
                // Edit Sekolah Modal
                document.querySelectorAll('.btn-edit-sekolah').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        document.getElementById('formEditSekolah').action = `/admin/sekolah/${id}`;
                        document.getElementById('edit_sekolah_npsn').value = this.getAttribute('data-npsn');
                        document.getElementById('edit_sekolah_nama').value = this.getAttribute('data-nama');
                        document.getElementById('edit_sekolah_jenjang').value = this.getAttribute('data-jenjang');
                        document.getElementById('edit_sekolah_status').value = this.getAttribute('data-status');
                        document.getElementById('edit_sekolah_alamat').value = this.getAttribute('data-alamat');
                    });
                });

                // Edit Periode Modal
                document.querySelectorAll('.btn-edit-periode').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        document.getElementById('formEditPeriode').action = `/admin/periode/${id}`;
                        document.getElementById('edit_ta_tahun').value = this.getAttribute('data-tahun');
                        document.getElementById('edit_ta_mulai').value = this.getAttribute('data-mulai');
                        document.getElementById('edit_ta_selesai').value = this.getAttribute('data-selesai');
                        document.getElementById('edit_ta_status').value = this.getAttribute('data-status');
                    });
                });

                // Edit Prodi Modal
                document.querySelectorAll('.btn-edit-prodi').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        document.getElementById('formEditProdi').action = `/admin/prodi/${id}`;
                        document.getElementById('edit_prodi_nama').value = this.getAttribute('data-nama');
                        document.getElementById('edit_prodi_jenjang').value = this.getAttribute('data-jenjang');
                        document.getElementById('edit_prodi_warna').value = this.getAttribute('data-warna');
                        document.getElementById('edit_prodi_urutan').value = this.getAttribute('data-urutan');
                        document.getElementById('edit_prodi_aktif').value = this.getAttribute('data-aktif');
                    });
                });

                // Handle active tab from session or local storage
                <?php if(session('active_tab')): ?>
                    const tabId = "<?php echo e(session('active_tab')); ?>-tab";
                    const tabTrigger = document.getElementById(tabId);
                    if(tabTrigger) {
                        const tab = new bootstrap.Tab(tabTrigger);
                        tab.show();
                    }
                <?php endif; ?>
            });

            // Misi Field Logic
            function addMisiField() {
                const container = document.getElementById('misi-container');
                const itemCount = container.querySelectorAll('.misi-item').length + 1;
                
                const html = `
                    <div class="input-group mb-2 misi-item">
                        <span class="input-group-text bg-light border-end-0">${itemCount}.</span>
                        <textarea name="misi[]" class="form-control border-start-0" rows="1"></textarea>
                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.misi-item').remove()"><i class="fas fa-times"></i></button>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', html);
                updateMisiNumbers();
            }

            function updateMisiNumbers() {
                const container = document.getElementById('misi-container');
                const items = container.querySelectorAll('.misi-item');
                items.forEach((item, index) => {
                    item.querySelector('.input-group-text').innerText = (index + 1) + '.';
                });
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/admin/master_data.blade.php ENDPATH**/ ?>