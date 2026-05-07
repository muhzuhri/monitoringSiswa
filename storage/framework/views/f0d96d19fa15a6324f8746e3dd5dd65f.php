<?php $__env->startSection('title', 'Data Siswa - Pimpinan Dashboard'); ?>
<?php $__env->startSection('body-class', 'dashboard-page pimpinan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/siswa.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="<?php echo e(asset('assets/js/pimpinan/siswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container" id="siswa-container"
        data-jurnal-url="<?php echo e(route('admin.rekap.jurnal', ['nisn' => ':nisn'])); ?>"
        data-absensi-url="<?php echo e(route('admin.rekap.absensi', ['nisn' => ':nisn'])); ?>">

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

        <div class="admin-content-wrapper shadow-sm" style="border-radius: 24px; background: #fff; overflow: hidden;">

            
            <div class="management-header p-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: #fdfdfd;">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.5rem;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Pemantauan Siswa Magang</h5>
                        <p class="text-muted small mb-0">Monitoring perkembangan dan riwayat seluruh siswa magang secara
                            real-time.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <form action="<?php echo e(route('pimpinan.siswa')); ?>" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" name="search" value="<?php echo e($search); ?>" class="p-input with-icon"
                                style="border-radius: 12px; background: #f8fafc;"
                                placeholder="Cari Siswa / Sekolah / NISN..." onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="px-4 pt-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1"
                    style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center py-3" id="siswa-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-siswa" type="button" role="tab"
                        style="border-radius: 12px;">
                        <i class="fas fa-users me-2"></i>
                        <span>Siswa Magang (<?php echo e($siswa->total()); ?>)</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center py-3" id="history-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-riwayat" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-history me-2"></i>
                        <span>Riwayat Siswa (<?php echo e($riwayatSiswas->count()); ?>)</span>
                    </button>
                </div>
            </div>

            
            <div class="tab-content p-4">

                
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">

                    <div class="tab-toolbar mb-4">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="active">
                                <i class="fas fa-th-large me-1"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list me-1"></i> Seluruh Siswa
                            </button>
                        </div>
                        <div class="tab-toolbar-info text-muted">
                            <i class="fas fa-info-circle me-1"></i> Menampilkan <strong><?php echo e($siswa->count()); ?></strong>
                            siswa aktif pada halaman ini
                        </div>
                    </div>

                    
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                            <?php $__empty_1 = true; $__currentLoopData = $groupedSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="student-card border-0 shadow-sm hover-elevate"
                                        style="border-radius: 20px; transition: all 0.3s ease;">
                                        <div class="card-actions">
                                            <button class="btn-premium-circle btn-view-p btn-detail" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailSiswa" data-nisn="<?php echo e($g['leader']->nisn); ?>"
                                                data-nama="<?php echo e($g['leader']->nama); ?>"
                                                data-email="<?php echo e($g['leader']->email); ?>"
                                                data-no_hp="<?php echo e($g['leader']->no_hp); ?>"
                                                data-kelas="<?php echo e($g['leader']->kelas); ?>"
                                                data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                data-sekolah="<?php echo e($g['leader']->sekolah); ?>"
                                                data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                data-jk="<?php echo e($g['leader']->jenis_kelamin); ?>"
                                                data-npsn="<?php echo e($g['leader']->npsn); ?>"
                                                data-tipe_magang="<?php echo e($g['leader']->tipe_magang); ?>"
                                                data-nisn_ketua="<?php echo e($g['leader']->nisn_ketua); ?>"
                                                data-surat_balasan="<?php echo e($g['leader']->surat_balasan); ?>"
                                                data-tahun_ajaran="<?php echo e($g['leader']->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>"
                                                data-guru-nip="<?php echo e($g['leader']->guru->id_guru ?? '-'); ?>"
                                                data-guru-hp="<?php echo e($g['leader']->guru->no_hp ?? '-'); ?>"
                                                data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>"
                                                data-pl-nip="<?php echo e($g['leader']->pembimbing->id_pembimbing ?? '-'); ?>"
                                                data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>"
                                                title="Lihat Detail Profil">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <div class="card-identity">
                                            <div class="card-avatar"
                                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                                                <?php if($g['is_group']): ?>
                                                    <div class="avatar-group-icon">
                                                        <i class="fas fa-user-friends"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <span
                                                        class="fw-bold"><?php echo e(strtoupper(substr($g['leader']->nama, 0, 1))); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-identity-info">
                                                <h6 class="fw-bold mb-1"><?php echo e(Str::limit($g['leader']->nama, 25)); ?></h6>
                                                <p class="text-muted small mb-0">NISN: <?php echo e($g['leader']->nisn); ?></p>
                                                <?php if($g['is_group']): ?>
                                                    <span class="badge-kelompok mt-1">Kelompok
                                                        (<?php echo e($g['members']->count()); ?> Siswa)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-info-list mt-3">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span class="card-info-value fw-bold"
                                                    title="<?php echo e($g['leader']->sekolah); ?>"><?php echo e(Str::limit($g['leader']->sekolah, 22)); ?></span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span
                                                    class="card-info-value text-primary fw-bold"><?php echo e(Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22)); ?></span>
                                            </div>
                                        </div>

                                        <div class="card-footer-bar mt-3 pt-3"
                                            style="border-top: 1px dashed rgba(0,0,0,0.1);">
                                            <?php if($g['leader']->absen_hari_ini): ?>
                                                <span class="status-label hadir">
                                                    <span class="status-dot hadir"></span> Hadir
                                                </span>
                                            <?php else: ?>
                                                <span class="status-label belum">
                                                    <span class="status-dot belum"></span> Menunggu
                                                </span>
                                            <?php endif; ?>
                                            <span class="card-guru-info text-muted extra-small">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                                <?php echo e(Str::limit($g['leader']->guru->nama ?? '-', 15)); ?>

                                            </span>
                                        </div>

                                        <div class="action-grid mt-3">
                                            <?php if($g['is_group']): ?>
                                                <button class="btn-action btn-show-members"
                                                    style="background: #f1f5f9; color: #475569; border: none; border-radius: 10px; width: 100%; padding: 10px; font-weight: 600;"
                                                    data-name="<?php echo e($g['leader']->nama); ?>" data-type="active"
                                                    data-members="<?php echo e($g['members']->map(function ($m) {
                                                            return [
                                                                'nism' => $m->nisn,
                                                                'nisn' => $m->nisn,
                                                                'nama' => $m->nama,
                                                                'email' => $m->email,
                                                                'no_hp' => $m->no_hp,
                                                                'kelas' => $m->kelas,
                                                                'jurusan' => $m->jurusan,
                                                                'sekolah' => $m->sekolah,
                                                                'perusahaan' => $m->perusahaan,
                                                                'mulai' => $m->tgl_mulai_magang ? \Carbon\Carbon::parse($m->tgl_mulai_magang)->format('d M Y') : '-',
                                                                'selesai' => $m->tgl_selesai_magang ? \Carbon\Carbon::parse($m->tgl_selesai_magang)->format('d M Y') : '-',
                                                                'guru_nama' => $m->guru->nama ?? '-',
                                                                'guru_nip' => $m->guru->id_guru ?? '-',
                                                                'guru_hp' => $m->guru->no_hp ?? '-',
                                                                'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                'jk' => $m->jenis_kelamin,
                                                                'npsn' => $m->npsn,
                                                                'tipe_magang' => $m->tipe_magang,
                                                                'nisn_ketua' => $m->nisn_ketua,
                                                                'surat_balasan' => $m->surat_balasan,
                                                                'tahun_ajaran' => $m->tahunAjaran->tahun_ajaran ?? '-',
                                                                'status' => $m->status,
                                                            ];
                                                        })->toJson()); ?>">
                                                    <i class="fas fa-users-viewfinder me-2"></i> Lihat Anggota
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action detail-btn btn-detail"
                                                    style="background: #e0f2fe; color: #0369a1; border: none; border-radius: 10px; width: 100%; padding: 10px; font-weight: 600;"
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
                                                    data-guru-hp="<?php echo e($g['leader']->guru->no_hp ?? '-'); ?>"
                                                    data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>"
                                                    data-pl-nip="<?php echo e($g['leader']->id_pembimbing ?? '-'); ?>"
                                                    data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>"
                                                    style="background: var(--primary-light); color: var(--primary); border: none;">
                                                    <i class="fas fa-id-card"></i> Detail Profil
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-user-slash"></i></div>
                                        <p>Tidak ada siswa aktif ditemukan.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4">
                            <?php echo e($siswa->links()); ?>

                        </div>
                    </div>

                    
                    <div class="view-container d-none" id="active-flat-view">
                        <div class="data-table-wrapper shadow-none border" style="border-radius: 16px; overflow: hidden;">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Pendidikan & Instansi</th>
                                        <th>Status Presensi</th>
                                        <th>Pembimbing</th>
                                        <th class="text-end">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="cell-name fw-bold"><?php echo e($s->nama); ?></div>
                                                <div class="cell-sub text-muted font-monospace small"><?php echo e($s->nisn); ?>

                                                    &middot; <?php echo e($s->kelas); ?></div>
                                            </td>
                                            <td>
                                                <div class="cell-sub text-dark fw-medium"><i
                                                        class="fas fa-university me-1 text-primary"></i>
                                                    <?php echo e(Str::limit($s->sekolah, 30)); ?></div>
                                                <div class="cell-sub text-muted small"><i
                                                        class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo e($s->perusahaan ?? '-'); ?></div>
                                            </td>
                                            <td>
                                                <?php if($s->absen_hari_ini): ?>
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-clock me-1"></i> Menunggu
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="cell-sub small">
                                                    <i class="fas fa-graduation-cap me-1 opacity-50"></i>
                                                    <?php echo e(Str::limit($s->guru->nama ?? '-', 20)); ?>

                                                </div>
                                                <div class="cell-sub small">
                                                    <i class="fas fa-user-tie me-1 opacity-50"></i>
                                                    <?php echo e(Str::limit($s->pembimbing->nama ?? '-', 20)); ?>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-premium-circle btn-view-p btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="<?php echo e($s->nisn); ?>"
                                                        data-nama="<?php echo e($s->nama); ?>"
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
                                                        data-pl-nip="<?php echo e($s->id_pembimbing ?? '-'); ?>"
                                                        data-pl-hp="<?php echo e($s->pembimbing->no_telp ?? '-'); ?>"
                                                        style="background: var(--primary-light); color: var(--primary); border: none;">
                                                        <i class="fas fa-id-card"></i> 
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                                                <p>Tidak ada data siswa aktif yang ditemukan.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-riwayat" role="tabpanel">

                    <div class="tab-toolbar mb-4">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="riwayat">
                                <i class="fas fa-th-large me-1"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="riwayat">
                                <i class="fas fa-list me-1"></i> Seluruh Riwayat
                            </button>
                        </div>

                        
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <span class="filter-label text-muted small fw-bold">
                                <i class="fas fa-filter"></i> Periode:
                            </span>
                            <form action="<?php echo e(route('pimpinan.siswa')); ?>" method="GET" id="filterRiwayatForm">
                                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                                <select name="periode" class="p-input small-select" onchange="this.form.submit()"
                                    style="padding: 0.5rem 2.5rem 0.5rem 1rem; height: auto; border-radius: 10px; font-size: 0.85rem;">
                                    <option value="">Semua Periode</option>
                                    <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id_tahun_ajaran); ?>"
                                            <?php echo e($periodeId == $p->id_tahun_ajaran ? 'selected' : ''); ?>>
                                            <?php echo e($p->tahun_ajaran); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </form>
                        </div>
                    </div>

                    
                    <div class="view-container" id="riwayat-grouped-view">
                        <div class="row g-4">
                            <?php $__empty_1 = true; $__currentLoopData = $groupedRiwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-xl-4 col-md-6">
                                    <div class="history-card border-0 shadow-sm"
                                        style="border-radius: 20px; background: #fff;">
                                        
                                        <div class="student-header p-3 pb-0">
                                            <div class="student-avatar"
                                                style="background: #f1f5f9; color: #64748b; font-weight: 700;">
                                                <?php if($g['is_group']): ?>
                                                    <i class="fas fa-layer-group"></i>
                                                <?php else: ?>
                                                    <?php echo e(strtoupper(substr($g['leader']->nama, 0, 1))); ?>

                                                <?php endif; ?>
                                            </div>
                                            <div class="student-meta">
                                                <h6 class="student-name fw-bold mb-0">
                                                    <?php echo e(Str::limit($g['leader']->nama, 22)); ?>

                                                </h6>
                                                <?php if($g['is_group']): ?>
                                                    <span
                                                        class="badge bg-info-light text-info-dark extra-small mt-1"><?php echo e($g['members']->count()); ?>

                                                        Anggota</span>
                                                <?php endif; ?>
                                                <p class="student-nisn small text-muted mt-1 mb-0">NISN:
                                                    <?php echo e($g['leader']->nisn); ?></p>
                                            </div>
                                            <div class="status-wrapper">
                                                <span class="badge-archive">
                                                    <i class="fas fa-archive"></i> Arsip
                                                </span>
                                            </div>
                                        </div>

                                        
                                        <div class="info-grid mt-2">
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-school"></i> SEKOLAH</label>
                                                <span class="info-value fw-medium"
                                                    title="<?php echo e($g['leader']->sekolah); ?>"><?php echo e(Str::limit($g['leader']->sekolah, 25)); ?></span>
                                            </div>
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-building"></i> INSTANSI</label>
                                                <span
                                                    class="info-value text-success fw-bold"><?php echo e($g['leader']->perusahaan ?? '-'); ?></span>
                                            </div>
                                        </div>

                                        
                                        <div class="action-grid p-3">
                                            <?php if($g['is_group']): ?>
                                                <button class="btn-action btn-show-members"
                                                    style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
                                                    data-name="<?php echo e($g['leader']->nama); ?>" data-type="history"
                                                    data-members="<?php echo e($g['members']->map(function ($m) {
                                                            return [
                                                                'nism' => $m->nisn,
                                                                'nisn' => $m->nisn,
                                                                'nama' => $m->nama,
                                                                'email' => $m->email,
                                                                'no_hp' => $m->no_hp,
                                                                'kelas' => $m->kelas,
                                                                'jurusan' => $m->jurusan,
                                                                'sekolah' => $m->sekolah,
                                                                'perusahaan' => $m->perusahaan,
                                                                'mulai' => $m->tgl_mulai_magang ? \Carbon\Carbon::parse($m->tgl_mulai_magang)->format('d M Y') : '-',
                                                                'selesai' => $m->tgl_selesai_magang ? \Carbon\Carbon::parse($m->tgl_selesai_magang)->format('d M Y') : '-',
                                                                'guru_nama' => $m->guru->nama ?? '-',
                                                                'guru_nip' => $m->guru->id_guru ?? '-',
                                                                'guru_hp' => $m->guru->no_hp ?? '-',
                                                                'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                'jk' => $m->jenis_kelamin,
                                                                'npsn' => $m->npsn,
                                                                'tipe_magang' => $m->tipe_magang,
                                                                'nisn_ketua' => $m->nisn_ketua,
                                                                'surat_balasan' => $m->surat_balasan,
                                                                'tahun_ajaran' => $m->tahunAjaran->tahun_ajaran ?? '-',
                                                                'status' => $m->status,
                                                            ];
                                                        })->toJson()); ?>">
                                                    <i class="fas fa-users-viewfinder me-2"></i> Anggota
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action detail-btn btn-detail"
                                                    style="background: #f0f9ff; color: #036ae0; border: 1px solid #bae6fd; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
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
                                                    data-guru-hp="<?php echo e($g['leader']->guru->no_hp ?? '-'); ?>"
                                                    data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>"
                                                    data-pl-nip="<?php echo e($g['leader']->id_pembimbing ?? '-'); ?>"
                                                    data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>"
                                                    style="background: var(--primary-light); color: var(--primary); border: none;">
                                                    <i class="fas fa-id-card"></i> Detail Profil
                                                </button>
                                            <?php endif; ?>

                                            <button class="btn-action btn-preview-pdf"
                                                style="background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
                                                data-url="<?php echo e(route('admin.rekap.kelompok', $g['leader']->nisn)); ?>">
                                                <i class="fas fa-file-pdf me-2"></i> Rekap Bln
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa pada periode ini.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="view-container d-none" id="riwayat-flat-view">
                        <div class="data-table-wrapper border shadow-none" style="border-radius: 16px; overflow: hidden;">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Pendidikan / Instansi</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end">Opsi & Rekap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $riwayatSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="cell-name fw-bold"><?php echo e($rs->nama); ?></div>
                                                <div class="cell-sub text-muted small">NISN: <?php echo e($rs->nisn); ?></div>
                                            </td>
                                            <td>
                                                <div class="cell-name text-dark font-weight-600">
                                                    <?php echo e(Str::limit($rs->sekolah, 25)); ?></div>
                                                <div class="cell-sub text-muted small"><i
                                                        class="fas fa-building me-1 opacity-50"></i>
                                                    <?php echo e($rs->perusahaan ?? '-'); ?></div>
                                            </td>
                                            <td>
                                                <div class="period-display">
                                                    <div class="period-item small">
                                                        <i class="fas fa-calendar-alt text-primary opacity-50 me-1"></i>
                                                        <?php echo e(\Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d/m/y')); ?>

                                                        -
                                                        <?php echo e(\Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d/m/y')); ?>

                                                    </div>
                                                    <div class="cell-sub text-info extra-small fw-bold">
                                                        <?php echo e($rs->tahunAjaran->tahun_ajaran ?? '-'); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end gap-2">
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
                                                        data-pl-nip="<?php echo e($rs->id_pembimbing ?? '-'); ?>"
                                                        data-pl-hp="<?php echo e($rs->pembimbing->no_telp ?? '-'); ?>"
                                                        style="background: var(--primary-light); color: var(--primary); border: none;">
                                                        <i class="fas fa-id-card"></i> 
                                                    </button>
                                                    <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf"
                                                        data-url="<?php echo e(route('admin.rekap.jurnal', $rs->nisn)); ?>"
                                                        title="Rekap Jurnal Kegiatan">
                                                        <i class="fas fa-book-open"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-absensi-p btn-preview-pdf"
                                                        data-url="<?php echo e(route('admin.rekap.absensi', $rs->nisn)); ?>"
                                                        title="Rekap Presensi Harian">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                                                <p>Belum ada riwayat siswa tersedia pada kriteria ini.</p>
                                            </td>
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

    
    <?php echo $__env->make('pimpinan.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/siswa.blade.php ENDPATH**/ ?>