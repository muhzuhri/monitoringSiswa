<?php $__env->startSection('title', 'Manajemen Siswa - Monitoring Siswa Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page admin-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-siswa.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/admin/kelola-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container" 
        data-jurnal-url="<?php echo e(route('admin.rekap.jurnal', ['nisn' => ':nisn'])); ?>"
        data-absensi-url="<?php echo e(route('admin.rekap.absensi', ['nisn' => ':nisn'])); ?>"
        data-kelompok-url="<?php echo e(route('admin.rekap.kelompok', ['nisn' => ':nisn'])); ?>"
        data-nilai-guru-url="<?php echo e(route('admin.rekap.nilaiGuru', ['nisn' => ':nisn'])); ?>"
        data-nilai-pembimbing-url="<?php echo e(route('admin.rekap.nilaiPembimbing', ['nisn' => ':nisn'])); ?>"
        data-laporan-akhir-url="<?php echo e(route('admin.rekap.laporanAkhir', ['nisn' => ':nisn'])); ?>"
        data-sertifikat-url="<?php echo e(route('admin.rekap.sertifikat', ['nisn' => ':nisn'])); ?>"
        data-asset-loader="<?php echo e(asset('images/unsri-pride.png')); ?>">

        <!-- Global Navigation Tabs: Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav d-flex w-100 gap-3">
                <a href="<?php echo e(route('admin.kelolaSiswa')); ?>"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaSiswa') ? 'active' : ''); ?>">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="<?php echo e(route('admin.kelolaGuru')); ?>"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaGuru') ? 'active' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="<?php echo e(route('admin.kelolaPembimbing')); ?>"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center <?php echo e(Route::is('admin.kelolaPembimbing') ? 'active' : ''); ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">

            
            <div class="management-header">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h5>Manajemen Siswa</h5>
                        <p>Kelola data seluruh siswa magang dan riwayat mereka.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <form action="<?php echo e(route('admin.kelolaSiswa')); ?>" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" name="search" value="<?php echo e($search); ?>" class="p-input with-icon"
                                placeholder="Cari Siswa / Sekolah / NISN..." onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>

            
            <?php if(session('success')): ?>
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1"
                    style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center" id="siswa-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-siswa" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang (<?php echo e($siswa->count()); ?>)</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="history-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-riwayat" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Siswa (<?php echo e($riwayatSiswas->count()); ?>)</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="lokasi-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-lokasi" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Lokasi Absensi (<?php echo e($lokasis->count()); ?>)</span>
                    </button>
                </div>
            </div>

            
            <div class="tab-content">

                
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Siswa</span>
                        </button>
                    </div>

                    
                    <div class="tab-toolbar">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="active">
                                <i class="fas fa-th-large"></i> &nbsp;Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list"></i> &nbsp;Seluruh Siswa
                            </button>
                        </div>
                        <div class="tab-toolbar-info">
                            Menampilkan <strong><?php echo e($siswa->count()); ?></strong> siswa aktif
                        </div>
                    </div>

                    
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                            <?php $__empty_1 = true; $__currentLoopData = $groupedSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="student-card">

                                        
                                        <div class="card-actions">
                                            <button class="btn-premium-circle btn-view-p btn-detail" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailSiswa" data-nisn="<?php echo e($g['leader']->nisn); ?>"
                                                data-nama="<?php echo e($g['leader']->nama); ?>"
                                                data-email="<?php echo e($g['leader']->email); ?>"
                                                data-no_hp="<?php echo e($g['leader']->no_hp); ?>"
                                                data-jk="<?php echo e($g['leader']->jenis_kelamin); ?>"
                                                data-kelas="<?php echo e($g['leader']->kelas); ?>"
                                                data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                data-sekolah="<?php echo e($g['leader']->sekolah); ?>"
                                                data-npsn="<?php echo e($g['leader']->npsn); ?>"
                                                data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                data-tipe_magang="<?php echo e($g['leader']->tipe_magang); ?>"
                                                data-nisn_ketua="<?php echo e($g['leader']->nisn_ketua); ?>"
                                                data-surat_balasan="<?php echo e($g['leader']->surat_balasan); ?>"
                                                data-tahun_ajaran="<?php echo e($g['leader']->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>"
                                                data-guru-nip="<?php echo e($g['leader']->guru->id_guru ?? '-'); ?>"
                                                data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>"
                                                data-pl-nip="<?php echo e($g['leader']->pembimbing->id_pembimbing ?? '-'); ?>"
                                                data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>"
                                                title="Lihat Detail Profil">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                        </div>

                                        
                                        <div class="card-identity">
                                            <div class="card-avatar">
                                                <?php if($g['is_group']): ?>
                                                    <div class="avatar-group-icon">
                                                        <i class="fas fa-user-friends"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <span><?php echo e(strtoupper(substr($g['leader']->nama, 0, 1))); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-identity-info">
                                                <h6><?php echo e($g['leader']->nama); ?></h6>
                                                <p>NISN: <?php echo e($g['leader']->nisn); ?></p>
                                                <?php if($g['is_group']): ?>
                                                    <span class="badge-kelompok">Kelompok
                                                        (<?php echo e($g['members']->count()); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-info-list">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span
                                                    class="card-info-value"><?php echo e(Str::limit($g['leader']->sekolah, 22)); ?></span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span
                                                    class="card-info-value"><?php echo e(Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22)); ?></span>
                                            </div>
                                        </div>

                                        <div class="card-footer-bar">
                                            <?php if($g['leader']->absen_hari_ini): ?>
                                                <span class="status-label hadir">
                                                    <span class="status-dot hadir"></span> Hadir
                                                </span>
                                            <?php else: ?>
                                                <span class="status-label belum">
                                                    <span class="status-dot belum"></span> Belum Absen
                                                </span>
                                            <?php endif; ?>
                                            <span class="card-guru-info">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                                <?php echo e(Str::limit($g['leader']->guru->nama ?? '-', 15)); ?>

                                            </span>
                                        </div>

                                        <div class="action-grid">
                                            <?php if($g['is_group']): ?>
                                                <button class="btn-action btn-show-members"
                                                    data-name="<?php echo e($g['leader']->nama); ?>"
                                                    data-members="<?php echo e($g['members']->toJson()); ?>"
                                                    data-show-actions="false">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-user-slash"></i></div>
                                        <p>Tidak ada siswa aktif ditemukan.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="view-container d-none" id="active-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th class="col-w-50">#</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah</th>
                                        <th>Lokasi Magang</th>
                                        <th>Status Hari Ini</th>
                                        <th class="text-end col-w-160">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td data-label="#"><?php echo e($siswa->firstItem() + $index); ?></td>
                                            <td data-label="Identitas">
                                                <div class="d-flex align-items-center gap-2">
                                                    
                                                    <div>
                                                        <div class="cell-name fw-bold"><?php echo e($s->nama); ?></div>
                                                        <div class="cell-sub"><?php echo e($s->nisn); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="Sekolah">
                                                <div class="cell-name"><?php echo e(Str::limit($s->sekolah, 25)); ?></div>
                                                
                                            </td>
                                            <td data-label="Instansi">
                                                <div class="cell-name"><?php echo e($s->perusahaan ?? 'Belum Ditugaskan'); ?></div>
                                            </td>
                                            <td data-label="Status">
                                                <?php if($s->absen_hari_ini): ?>
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle"></i> Hadir
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-clock"></i> Menunggu
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-premium-circle btn-view-p btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="<?php echo e($s->nisn); ?>" data-nama="<?php echo e($s->nama); ?>"
                                                        data-email="<?php echo e($s->email); ?>"
                                                        data-no_hp="<?php echo e($s->no_hp); ?>"
                                                        data-jk="<?php echo e($s->jenis_kelamin); ?>"
                                                        data-kelas="<?php echo e($s->kelas); ?>"
                                                        data-jurusan="<?php echo e($s->jurusan); ?>"
                                                        data-sekolah="<?php echo e($s->sekolah); ?>"
                                                        data-npsn="<?php echo e($s->npsn); ?>"
                                                        data-perusahaan="<?php echo e($s->perusahaan); ?>"
                                                        data-tipe_magang="<?php echo e($s->tipe_magang); ?>"
                                                        data-nisn_ketua="<?php echo e($s->nisn_ketua); ?>"
                                                        data-surat_balasan="<?php echo e($s->surat_balasan); ?>"
                                                        data-tahun_ajaran="<?php echo e($s->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                        data-mulai="<?php echo e($s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                        data-selesai="<?php echo e($s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                        data-guru-nama="<?php echo e($s->guru->nama ?? '-'); ?>"
                                                        data-guru-nip="<?php echo e($s->guru->id_guru ?? '-'); ?>"
                                                        data-guru-hp="<?php echo e($s->guru->no_hp ?? '-'); ?>"
                                                        data-pl-nama="<?php echo e($s->pembimbing->nama ?? '-'); ?>"
                                                        data-pl-nip="<?php echo e($s->pembimbing->id_pembimbing ?? '-'); ?>"
                                                        data-pl-hp="<?php echo e($s->pembimbing->no_telp ?? '-'); ?>"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-edit-p btn-edit"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                                        data-id="<?php echo e($s->nisn); ?>" data-nama="<?php echo e($s->nama); ?>"
                                                        data-email="<?php echo e($s->email); ?>"
                                                        data-no_hp="<?php echo e($s->no_hp); ?>"
                                                        data-jk="<?php echo e($s->jenis_kelamin); ?>"
                                                        data-kelas="<?php echo e($s->kelas); ?>"
                                                        data-jurusan="<?php echo e($s->jurusan); ?>"
                                                        data-sekolah="<?php echo e($s->sekolah); ?>"
                                                        data-npsn="<?php echo e($s->npsn); ?>"
                                                        data-id_tahun_ajaran="<?php echo e($s->id_tahun_ajaran); ?>"
                                                        data-perusahaan="<?php echo e($s->perusahaan); ?>"
                                                        data-tipe_magang="<?php echo e($s->tipe_magang); ?>"
                                                        data-nisn_ketua="<?php echo e($s->nisn_ketua); ?>"
                                                        data-surat_balasan="<?php echo e($s->surat_balasan); ?>"
                                                        data-guruNip="<?php echo e($s->id_guru); ?>"
                                                        data-plNip="<?php echo e($s->id_pembimbing); ?>"
                                                        data-mulai_raw="<?php echo e($s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('Y-m-d') : ''); ?>"
                                                        data-selesai_raw="<?php echo e($s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('Y-m-d') : ''); ?>"
                                                        title="Edit Data">
                                                        <i class="fas fa-user-edit"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-delete-p btn-delete-trigger"
                                                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                        data-url="<?php echo e(route('admin.destroySiswa', $s->nisn)); ?>"
                                                        title="Hapus Akun">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center p-5 text-muted">
                                                Tidak ada data siswa aktif yang terdaftar.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-riwayat" role="tabpanel">

                    
                    <div class="history-toolbar mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="view-mode-wrapper">
                                <button class="view-mode-btn" data-view="grouped" data-target="riwayat"
                                    title="Per Kelompok">
                                    <i class="fas fa-th-large"></i>&nbsp; Per Kelompok
                                </button>
                                <button class="view-mode-btn active" data-view="flat" data-target="riwayat"
                                    title="Tampilan List">
                                    <i class="fas fa-list"></i>&nbsp; Seluruh Siswa
                                </button>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="filter-label text-muted small fw-bold">
                                    <i class="fas fa-filter"></i> Periode:
                                </span>

                                <form action="<?php echo e(route('admin.kelolaSiswa')); ?>" method="GET" class="filter-form d-flex align-items-center gap-2"
                                    id="filterPeriodeForm">
                                    <input type="hidden" name="tab" value="history">
                                    <?php if($search): ?>
                                        <input type="hidden" name="search" value="<?php echo e($search); ?>">
                                    <?php endif; ?>
                                    <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius: 10px; min-width: 160px; height: 38px;">
                                        <option value="">-- Semua Periode --</option>
                                        <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($opt->id_tahun_ajaran); ?>"
                                                <?php echo e($periodeId == $opt->id_tahun_ajaran ? 'selected' : ''); ?>>
                                                <?php echo e($opt->tahun_ajaran); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($periodeId): ?>
                                        <a href="<?php echo e(route('admin.kelolaSiswa', ['tab' => 'history', 'search' => $search])); ?>"
                                            class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" 
                                            title="Reset Filter" style="width: 32px; height: 32px;">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <div class="view-container d-none" id="riwayat-grouped-view">
                        <div class="row g-4">
                            <?php $__empty_1 = true; $__currentLoopData = $groupedRiwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="history-card">
                                        
                                        <div class="student-header">
                                            <div class="student-avatar">
                                                <?php if($g['is_group']): ?>
                                                    <i class="fas fa-layer-group"></i>
                                                <?php else: ?>
                                                    <?php echo e(strtoupper(substr($g['leader']->nama, 0, 1))); ?>

                                                <?php endif; ?>
                                            </div>
                                            <div class="student-meta">
                                                <h6 class="student-name">
                                                    <?php echo e($g['leader']->nama); ?>

                                                    <?php if($g['is_group']): ?>
                                                        <span
                                                            class="badge bg-info-light text-info-dark"><?php echo e($g['members']->count()); ?>

                                                            Anggota</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <p class="student-nisn">NISN: <?php echo e($g['leader']->nisn); ?></p>
                                                <div class="mt-1">
                                                    <span class="badge-completed">
                                                        <i class="fas fa-flag-checkered"></i> SELESAI
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="status-wrapper">
                                                <span class="badge-archive">
                                                    <i class="fas fa-archive"></i> Arsip
                                                </span>
                                            </div>
                                        </div>

                                        
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-school"></i> SEKOLAH</label>
                                                <span class="info-value"
                                                    title="<?php echo e($g['leader']->sekolah); ?>"><?php echo e($g['leader']->sekolah); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-building"></i>
                                                    PERUSAHAAN</label>
                                                <span class="info-value"
                                                    title="<?php echo e($g['leader']->perusahaan); ?>"><?php echo e($g['leader']->perusahaan ?? '-'); ?></span>
                                            </div>
                                        </div>

                                        
                                        <div class="action-grid">
                                            <?php if($g['is_group']): ?>
                                                <button class="btn-action btn-detail-group btn-show-members"
                                                    data-name="<?php echo e($g['leader']->nama); ?>"
                                                    data-members="<?php echo e($g['members']->toJson()); ?>"
                                                    data-show-actions="true">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota Kelompok
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-detail-group btn-detail"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                    data-nisn="<?php echo e($g['leader']->nisn); ?>"
                                                    data-nama="<?php echo e($g['leader']->nama); ?>"
                                                    data-email="<?php echo e($g['leader']->email); ?>"
                                                    data-no_hp="<?php echo e($g['leader']->no_hp); ?>"
                                                    data-jk="<?php echo e($g['leader']->jenis_kelamin); ?>"
                                                    data-kelas="<?php echo e($g['leader']->kelas); ?>"
                                                    data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                    data-sekolah="<?php echo e($g['leader']->sekolah); ?>"
                                                    data-npsn="<?php echo e($g['leader']->npsn); ?>"
                                                    data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                    data-tipe_magang="<?php echo e($g['leader']->tipe_magang); ?>"
                                                    data-nisn_ketua="<?php echo e($g['leader']->nisn_ketua); ?>"
                                                    data-surat_balasan="<?php echo e($g['leader']->surat_balasan); ?>"
                                                    data-tahun_ajaran="<?php echo e($g['leader']->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                    data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                    data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                    data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>"
                                                    data-guru-nip="<?php echo e($g['leader']->guru->id_guru ?? '-'); ?>"
                                                    data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>"
                                                    data-pl-nip="<?php echo e($g['leader']->pembimbing->id_pembimbing ?? '-'); ?>"
                                                    data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>">
                                                    <i class="fas fa-user-circle"></i> Detail Profil
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn-action btn-detail-group btn-preview-pdf"
                                                data-url="<?php echo e(route('admin.rekap.kelompok', $g['leader']->nisn)); ?>">
                                                <i class="fas fa-users"></i> Rekap Kelompok bulanan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>


                    
                    <div class="view-container" id="riwayat-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th class="col-w-50">#</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah </th>
                                        <th>Lokasi Magang</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end col-w-200">Aksi & Rekap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $riwayatSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td data-label="#"><?php echo e($index + 1); ?></td>
                                            <td data-label="Siswa">
                                                <div class="cell-name fw-bold"><?php echo e($rs->nama); ?></div>
                                                <div class="cell-sub"><?php echo e($rs->nisn); ?>

                                                </div>
                                            </td>
                                            <td data-label="Sekolah">
                                                <div class="cell-name"><?php echo e(Str::limit($rs->sekolah, 25)); ?></div>
                                                
                                            </td>
                                            <td data-label="Penempatan">
                                                <div class="cell-name"><?php echo e($rs->perusahaan ?? '-'); ?></div>
                                                
                                            </td>
                                            <td data-label="Periode">
                                                <div class="period-display">
                                                    <div class="period-item">
                                                        <i class="fas fa-calendar-check text-primary opacity-50"></i>
                                                        <?php echo e(\Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d/m/y')); ?>

                                                        -
                                                        <?php echo e(\Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d/m/y')); ?>

                                                    </div>
                                                    <div class="cell-sub text-info">
                                                        <?php echo e($rs->tahunAjaran->tahun_ajaran ?? '-'); ?></div>
                                                </div>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="d-flex flex-column align-items-end gap-2 py-2">
                                                    <div class="action-group justify-content-end">
                                                        <button class="btn-premium-circle btn-view-p btn-detail"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                            data-nisn="<?php echo e($rs->nisn); ?>"
                                                            data-nama="<?php echo e($rs->nama); ?>"
                                                            data-email="<?php echo e($rs->email); ?>"
                                                            data-no_hp="<?php echo e($rs->no_hp); ?>"
                                                            data-jk="<?php echo e($rs->jenis_kelamin); ?>"
                                                            data-kelas="<?php echo e($rs->kelas); ?>"
                                                            data-jurusan="<?php echo e($rs->jurusan); ?>"
                                                            data-sekolah="<?php echo e($rs->sekolah); ?>"
                                                            data-npsn="<?php echo e($rs->npsn); ?>"
                                                            data-perusahaan="<?php echo e($rs->perusahaan); ?>"
                                                            data-tipe_magang="<?php echo e($rs->tipe_magang); ?>"
                                                            data-nisn_ketua="<?php echo e($rs->nisn_ketua); ?>"
                                                            data-surat_balasan="<?php echo e($rs->surat_balasan); ?>"
                                                            data-tahun_ajaran="<?php echo e($rs->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                            data-mulai="<?php echo e($rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                            data-selesai="<?php echo e($rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                            data-guru-nama="<?php echo e($rs->guru->nama ?? '-'); ?>"
                                                            data-guru-nip="<?php echo e($rs->guru->id_guru ?? '-'); ?>"
                                                            data-guru-hp="<?php echo e($rs->guru->no_hp ?? '-'); ?>"
                                                            data-pl-nama="<?php echo e($rs->pembimbing->nama ?? '-'); ?>"
                                                            data-pl-nip="<?php echo e($rs->pembimbing->id_pembimbing ?? '-'); ?>"
                                                            data-pl-hp="<?php echo e($rs->pembimbing->no_telp ?? '-'); ?>"
                                                            title="Profil Lengkap">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-absensi-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.absensi', $rs->nisn)); ?>"
                                                            title="Rekap Absensi">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.jurnal', $rs->nisn)); ?>"
                                                            title="Rekap Jurnal / Kegiatan">
                                                            <i class="fas fa-book-open"></i>
                                                        </button>
                                                    </div>
                                                    <div class="action-group justify-content-end">
                                                        <button class="btn-premium-circle btn-star-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.nilaiGuru', $rs->nisn)); ?>"
                                                            title="Penilaian Guru">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-user-check-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.nilaiPembimbing', $rs->nisn)); ?>"
                                                            title="Penilaian Pembimbing">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-pdf-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.laporanAkhir', $rs->nisn)); ?>"
                                                            title="Laporan Akhir Siswa">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-cert-p btn-preview-pdf"
                                                            data-url="<?php echo e(route('admin.rekap.sertifikat', $rs->nisn)); ?>"
                                                            title="Sertifikat Magang">
                                                            <i class="fas fa-certificate"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center p-5 text-muted">
                                                Belum ada riwayat siswa yang tersedia.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-lokasi" role="tabpanel">

                    
                    <div class="tab-toolbar justify-content-between">
                        <div class="tab-toolbar-info">
                            <i class="fas fa-info-circle me-1 text-primary"></i>
                            Menampilkan <strong><?php echo e($lokasis->count()); ?></strong> titik lokasi absensi terdaftar
                        </div>
                        <button class="btn-primary-custom btn-sm rounded-pill px-4" data-bs-toggle="modal"
                            data-bs-target="#modalTambahLokasi">
                            <i class="fas fa-plus me-1"></i> Tambah Lokasi
                        </button>
                    </div>

                    <div class="row g-4">
                        <?php $__empty_1 = true; $__currentLoopData = $lokasis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="col-xl-4 col-md-6">
                                <div class="student-card p-4"> 
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="card-avatar"
                                            style="background: rgba(13, 110, 253, 0.1); color: #0d6efd; width: 45px; height: 45px;">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div class="card-actions position-static">
                                            <button class="action-round btn-edit-loc" data-bs-toggle="modal"
                                                data-bs-target="#modalEditLokasi" data-id="<?php echo e($l->id); ?>"
                                                data-nama="<?php echo e($l->nama_lokasi); ?>" data-lat="<?php echo e($l->latitude); ?>"
                                                data-lng="<?php echo e($l->longitude); ?>" data-radius="<?php echo e($l->radius); ?>"
                                                data-active="<?php echo e($l->is_active); ?>" title="Edit Lokasi">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-round btn-delete-loc" data-bs-toggle="modal"
                                                data-bs-target="#modalHapusLokasi"
                                                data-url="<?php echo e(route('admin.destroyLokasi', $l->id)); ?>"
                                                title="Hapus Lokasi">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-1"><?php echo e($l->nama_lokasi); ?></h6>
                                    <p class="text-muted small mb-3">ID Lokasi: #LOK-<?php echo e($l->id); ?></p>

                                    <div class="card-info-list mb-3">
                                        <div class="card-info-row">
                                            <span class="card-info-label">Latitude</span>
                                            <span class="card-info-value fw-mono"><?php echo e($l->latitude); ?></span>
                                        </div>
                                        <div class="card-info-row">
                                            <span class="card-info-label">Longitude</span>
                                            <span class="card-info-value fw-mono"><?php echo e($l->longitude); ?></span>
                                        </div>
                                        <div class="card-info-row">
                                            <span class="card-info-label">Radius</span>
                                            <span class="card-info-value"><i class="fas fa-bullseye me-1 opacity-50"></i>
                                                <?php echo e($l->radius); ?>m</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <?php if($l->is_active): ?>
                                            <span class="badge-status hadir" style="font-size: 0.75rem;">
                                                <i class="fas fa-check-circle"></i> Status Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status belum" style="font-size: 0.75rem;">
                                                <i class="fas fa-times-circle"></i> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-map-marker-slash"></i></div>
                                    <p>Belum ada titik lokasi absensi yang terdaftar.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title">Preview Laporan</h6>
                    </div>

                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File"
                                target="_blank">
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body pdf-viewer-body">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <div class="loader-logo-container">
                                <img src="<?php echo e(asset('images/unsri-pride.png')); ?>" alt="UNSRI">
                            </div>
                            
                        </div>

                        <div id="pdfErrorMsg" class="d-none">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Gagal memuat file PDF.<br><small>Coba gunakan tombol Unduh.</small></p>
                        </div>
                    </div>
                </div
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('admin.kelolaSiswaModals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('assets/js/admin/kelolaSiswa.js')); ?>"></script>
    <?php $__env->stopPush(); ?>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/admin/kelolaSiswa.blade.php ENDPATH**/ ?>