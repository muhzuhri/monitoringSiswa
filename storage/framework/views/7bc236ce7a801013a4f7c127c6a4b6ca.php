

<?php $__env->startSection('title', 'Daftar Siswa Bimbingan - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/daftarSiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">

        
        <div class="page-header">
            <div class="header-text">
                <h4 class="page-title">Siswa Bimbingan</h4>
                <p class="page-subtitle">Kelola dan pantau seluruh siswa magang di bawah bimbingan Anda.</p>
            </div>
            <div class="search-wrapper">
                <form id="headerSearchForm" class="search-form">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="headerSearchInput" value="<?php echo e($search); ?>" class="search-input"
                        placeholder="Cari Nama, NISN, atau Perusahaan..." autocomplete="off">
                </form>
            </div>
        </div>

        
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="bimbingan-tab" data-bs-toggle="pill" data-bs-target="#bimbingan"
                    type="button" role="tab">
                    <i class="fas fa-users"></i>
                    <span>Siswa Bimbingan (<?php echo e($siswas->count()); ?>)</span>
                </button>
                <button class="tab-button" id="search-tab" data-bs-toggle="pill" data-bs-target="#search-students"
                    type="button" role="tab">
                    <i class="fas fa-search-plus"></i>
                    <span>Cari Siswa (<?php echo e($availableSiswas->count()); ?>)</span>
                </button>
                <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#riwayat-history"
                    type="button" role="tab">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Siswa (<?php echo e($riwayatSiswas->count()); ?><?php echo e($periodeId ? ' &bull; filtered' : ''); ?>)</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="siswaTabContent">
            
            <div class="tab-pane fade show active" id="bimbingan" role="tabpanel">
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="view-mode-wrapper"
                        style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
                        <button class="view-mode-btn active" data-view="grouped" data-target="bimbingan">
                            <i class="fas fa-th-large me-1"></i> Perkelompok
                        </button>
                        <button class="view-mode-btn" data-view="flat" data-target="bimbingan">
                            <i class="fas fa-list me-1"></i> Seluruh Siswa
                        </button>
                    </div>
                </div>

                
                <div class="view-container" id="bimbingan-grouped-view">
                    <div class="siswa-grid">
                        <?php $__empty_1 = true; $__currentLoopData = $groupedSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="student-card <?php echo e($g['is_group'] ? 'group-card' : ''); ?>">
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
                                                <span class="badge bg-info-light text-info-dark ms-1"
                                                    style="font-size: 0.65rem; border-radius: 50px;"><?php echo e($g['members']->count()); ?>

                                                    Anggota</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="student-nisn">NISN: <?php echo e($g['leader']->nisn); ?></p>
                                        <div class="mt-2 text-start">
                                            <?php if($g['leader']->status === 'selesai'): ?>
                                                <span class="badge bg-secondary text-white"
                                                    style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                    <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success text-white"
                                                    style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                    <i class="fas fa-check-circle me-1"></i> AKTIF
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="status-wrapper">
                                        <?php
                                            $hadirAll = $g['members']->every(fn($m) => $m->absen_hari_ini);
                                            $hadirCount = $g['members']->filter(fn($m) => $m->absen_hari_ini)->count();
                                        ?>
                                        <?php if($hadirAll): ?>
                                            <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i>
                                                Hadir</span>
                                        <?php elseif($hadirCount > 0): ?>
                                            <span class="status-badge status-warning"><i class="fas fa-user-clock"></i>
                                                <?php echo e($hadirCount); ?>/<?php echo e($g['members']->count()); ?></span>
                                        <?php else: ?>
                                            <span class="status-badge status-absen"><i class="fas fa-times-circle"></i>
                                                Belum Absen</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                        <span class="info-value"><?php echo e($g['leader']->sekolah); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                        <span class="info-value"><?php echo e($g['leader']->perusahaan); ?></span>
                                    </div>
                                </div>
                                <div class="action-grid">
                                    <?php if($g['is_group']): ?>
                                        <button class="btn-action btn-detail-group btn-show-members"
                                            data-name="<?php echo e($g['leader']->nama); ?>"
                                            data-members="<?php echo e($g['members']->toJson()); ?>"
                                            data-logbook-route="<?php echo e(route('guru.logbook', ['nisn' => ':nisn'])); ?>"
                                            data-absensi-route="<?php echo e(route('guru.absensi', ['nisn' => ':nisn'])); ?>"
                                            data-logbook-download="<?php echo e(route('guru.rekap.jurnal', ['nisn' => ':nisn'])); ?>"
                                            data-absensi-download="<?php echo e(route('guru.rekap.absensi', ['nisn' => ':nisn'])); ?>">
                                            <i class="fas fa-search"></i> Pantau Kelompok
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-action btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailSiswa" 
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-state">Belum ada siswa bimbingan.</div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="view-container d-none" id="bimbingan-flat-view">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Identitas Siswa</th>
                                        <th>Status Hari Ini</th>
                                        <th>Sekolah / Perusahaan</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="active-flat-row">
                                            <td class="ps-4 text-muted small"><?php echo e($idx + 1); ?></td>
                                            <td>
                                                <div class="td-siswa-name"><?php echo e($s->nama); ?></div>
                                                <div class="td-siswa-nisn"><i
                                                        class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                    <?php echo e($s->nisn); ?></div>
                                            </td>
                                            <td>
                                                <?php if($s->absen_hari_ini): ?>
                                                    <span class="status-badge status-hadir"><i
                                                            class="fas fa-check-circle"></i> Hadir</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-absen"><i
                                                            class="fas fa-times-circle"></i> Belum Absen</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school small"><i
                                                            class="fas fa-university me-1"></i> <?php echo e($s->sekolah); ?></span>
                                                    <small class="text-muted"><i class="fas fa-building me-1"></i>
                                                        <?php echo e($s->perusahaan); ?></small>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn-small btn-preview-pdf"
                                                        style="background: rgba(15, 23, 42, 0.04); color: #64748b;"
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
                                                        data-pl-nip="<?php echo e($s->id_pembimbing ?? '-'); ?>"
                                                        data-pl-hp="<?php echo e($s->pembimbing->no_telp ?? '-'); ?>"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-id-card"></i>
                                                    </button>
                                                    <a href="<?php echo e(route('guru.logbook', $s->nisn)); ?>" class="btn-small btn-preview-pdf"
                                                        style="background: var(--primary-light); color: var(--primary);"
                                                        title="Logbook">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('guru.absensi', $s->nisn)); ?>" class="btn-small btn-preview-pdf"
                                                        style="background: var(--warning-light); color: #92400e;"
                                                        title="Absensi">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-4">Tidak ada data.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="noResultsBimbingan" class="empty-state" style="display: none; width: 100%;">Tidak ada siswa
                    bimbingan yang cocok dengan pencarian.</div>
            </div>

            
            <div class="tab-pane fade" id="search-students" role="tabpanel">
                


                <div class="ui-card">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Identitas Siswa / Ketua</th>
                                    <th>Asal Sekolah</th>
                                    <th>Tipe Magang</th>
                                    <th>Kapasitas</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $groupedAvailable; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asKey => $ga): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="td-siswa-name"><?php echo e($ga['leader']->nama); ?></div>
                                            <div class="td-siswa-nisn"><i class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                <?php echo e($ga['leader']->nisn); ?></div>
                                        </td>
                                        <td><span class="badge-school"><i class="fas fa-university me-1"></i>
                                                <?php echo e($ga['leader']->sekolah); ?></span></td>
                                        <td>
                                            <?php if($ga['is_group']): ?>
                                                <span class="badge bg-info-light text-info-dark px-3 py-2"
                                                    style="border-radius: 50px;">
                                                    <i class="fas fa-layer-group me-1"></i> Kelompok
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-light text-muted px-3 py-2"
                                                    style="border-radius: 50px; background: #f1f5f9;">
                                                    <i class="fas fa-user me-1"></i> Individu
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($ga['is_group']): ?>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-stack me-2">
                                                        <?php $__currentLoopData = $ga['members']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="avatar-mini" title="<?php echo e($member->nama); ?>">
                                                                <?php echo e(strtoupper(substr($member->nama, 0, 1))); ?></div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                    <small class="fw-bold text-primary"><?php echo e($ga['members']->count()); ?>

                                                        Orang</small>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted">1 Orang</small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="<?php echo e(route('guru.siswa.claim', $ga['leader']->nisn)); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn-small btn-accept">
                                                    <i class="fas fa-plus-circle"></i> Pilih Jadi Bimbingan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="empty-row text-center p-5">
                                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                            <p>Tidak ada siswa tersedia untuk kriteria ini.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane fade" id="riwayat-history" role="tabpanel">

                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="view-mode-wrapper"
                        style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
                        <button class="view-mode-btn" data-view="grouped" data-target="history">
                            <i class="fas fa-th-large me-1"></i> Perkelompok
                        </button>
                        <button class="view-mode-btn active" data-view="flat" data-target="history">
                            <i class="fas fa-list me-1"></i> Seluruh Siswa
                        </button>
                    </div>
                </div>

                
                <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap"
                    style="background:#ffffff; border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                    <div class="filter-label"
                        style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em; margin-bottom: 0;">
                        <i class="fas fa-filter"></i>
                        <span>Filter Periode:</span>
                    </div>
                    <form id="periodeFilterForm" method="GET" action="<?php echo e(route('guru.siswa')); ?>"
                        class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0"
                        style="gap:0.75rem;">
                        <?php if($search): ?>
                            <input type="hidden" name="search" value="<?php echo e($search); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="tab" value="history">
                        <select name="periode" id="periodeSelect" class="form-select"
                            style="min-width:220px; border-radius:var(--radius-sm); font-weight:600; font-size:0.85rem; border:1.5px solid var(--border); cursor:pointer;"
                            onchange="document.getElementById('periodeFilterForm').submit()">
                            <option value="">-- Semua Periode --</option>
                            <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($opt->id_tahun_ajaran); ?>"
                                    <?php echo e($periodeId == $opt->id_tahun_ajaran ? 'selected' : ''); ?>>
                                    <?php echo e($opt->tahun_ajaran); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($periodeId): ?>
                            <a href="<?php echo e(route('guru.siswa', array_filter(['search' => $search, 'tab' => 'history']))); ?>"
                                class="btn btn-outline-danger btn-sm"
                                style="border-radius:var(--radius-sm); font-weight:700; padding:0.45rem 1rem;"
                                title="Hapus Filter">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        <?php endif; ?>
                    </form>
                    <?php if($periodeId): ?>
                        <?php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); ?>
                        <?php if($selectedPeriode): ?>
                            <span class="badge bg-primary ms-auto mt-2 mt-md-0"
                                style="padding:0.5rem 1rem; border-radius:99px; font-weight:800; font-size:0.75rem;">
                                <i class="fas fa-calendar-alt me-1"></i>
                                <?php echo e($selectedPeriode->tahun_ajaran); ?>

                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                
                <div class="view-container d-none" id="history-grouped-view">
                    <div class="siswa-grid">
                        <?php $__empty_1 = true; $__currentLoopData = $groupedRiwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="student-card group-card">
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
                                                <span class="badge bg-info-light text-info-dark ms-1"
                                                    style="font-size: 0.65rem; border-radius: 50px;"><?php echo e($g['members']->count()); ?>

                                                    Anggota</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="student-nisn">NISN: <?php echo e($g['leader']->nisn); ?></p>
                                        <div class="mt-2 text-start">
                                            <span class="badge bg-secondary text-white"
                                                style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                            </span>
                                        </div>
                                    </div>
                                    <div class="status-wrapper">
                                        <span class="status-badge status-hadir"
                                            style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                                            <i class="fas fa-archive"></i> Arsip
                                        </span>
                                    </div>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                        <span class="info-value"><?php echo e($g['leader']->sekolah); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                        <span class="info-value"><?php echo e($g['leader']->perusahaan); ?></span>
                                    </div>
                                </div>
                                <div class="action-grid">
                                    <?php if($g['is_group']): ?>
                                        <button class="btn-action btn-detail-group btn-show-members"
                                            data-name="<?php echo e($g['leader']->nama); ?>"
                                            data-members="<?php echo e($g['members']->toJson()); ?>" data-context="history"
                                            data-logbook-route="<?php echo e(route('guru.logbook', ['nisn' => ':nisn'])); ?>"
                                            data-absensi-route="<?php echo e(route('guru.absensi', ['nisn' => ':nisn'])); ?>"
                                            data-logbook-download="<?php echo e(route('guru.rekap.jurnal', ['nisn' => ':nisn'])); ?>"
                                            data-absensi-download="<?php echo e(route('guru.rekap.absensi', ['nisn' => ':nisn'])); ?>">
                                            <i class="fas fa-users"></i> Anggota Kelompok
                                        </button>
                                    <?php endif; ?>
                                    <div class="action-row">
                                        <button class="btn-action btn-absensi btn-preview-pdf"
                                            data-url="<?php echo e(route('guru.rekap.kelompok', $g['leader']->nisn)); ?>"
                                            style="width: 100%;">
                                            <i class="fas fa-file-signature"></i> Rekap Absensi Kelompok
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-state">Belum ada riwayat siswa.</div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="view-container" id="history-flat-view">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah / Perusahaan</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="riwayatTableBody">
                                    <?php $__empty_1 = true; $__currentLoopData = $riwayatSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr class="history-row">
                                            <td class="ps-4 text-muted small"><?php echo e($index + 1); ?></td>
                                            <td>
                                                <div class="td-siswa-name fw-bold text-dark"><?php echo e($siswa->nama); ?></div>
                                                <div class="td-siswa-nisn small text-muted"><i
                                                        class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                    <?php echo e($siswa->nisn); ?></div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school bg-light text-dark px-2 py-1 rounded small"
                                                        style="border: 1px solid #eee; font-size: 0.75rem;"><i
                                                            class="fas fa-university me-1 text-primary"></i>
                                                        <?php echo e($siswa->sekolah); ?></span>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><i
                                                            class="fas fa-building me-1"></i>
                                                        <?php echo e($siswa->perusahaan); ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted" style="line-height: 1.4;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-calendar-alt me-2 text-primary opacity-75"
                                                            style="width: 14px;"></i>
                                                        <span
                                                            style="font-size: 0.75rem;"><?php echo e($siswa->tgl_mulai_magang ? \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->translatedFormat('d M Y') : '-'); ?></span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-flag-checkered me-2 text-primary opacity-75"
                                                            style="width: 14px;"></i>
                                                        <span
                                                            style="font-size: 0.75rem;"><?php echo e($siswa->tgl_selesai_magang ? \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->translatedFormat('d M Y') : '-'); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2 mb-1">
                                                    <button class="btn-small btn-preview-pdf"
                                                        style="background: rgba(15, 23, 42, 0.04); color: #64748b;"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="<?php echo e($siswa->nisn); ?>"
                                                        data-nama="<?php echo e($siswa->nama); ?>"
                                                        data-email="<?php echo e($siswa->email); ?>"
                                                        data-no_hp="<?php echo e($siswa->no_hp); ?>"
                                                        data-jk="<?php echo e($siswa->jenis_kelamin); ?>"
                                                        data-kelas="<?php echo e($siswa->kelas); ?>"
                                                        data-jurusan="<?php echo e($siswa->jurusan); ?>"
                                                        data-sekolah="<?php echo e($siswa->sekolah); ?>"
                                                        data-npsn="<?php echo e($siswa->npsn); ?>"
                                                        data-perusahaan="<?php echo e($siswa->perusahaan); ?>"
                                                        data-tipe_magang="<?php echo e($siswa->tipe_magang); ?>"
                                                        data-nisn_ketua="<?php echo e($siswa->nisn_ketua); ?>"
                                                        data-surat_balasan="<?php echo e($siswa->surat_balasan); ?>"
                                                        data-tahun_ajaran="<?php echo e($siswa->tahunAjaran->tahun_ajaran ?? '-'); ?>"
                                                        data-mulai="<?php echo e($siswa->tgl_mulai_magang ? \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                        data-selesai="<?php echo e($siswa->tgl_selesai_magang ? \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                        data-guru-nama="<?php echo e($siswa->guru->nama ?? '-'); ?>"
                                                        data-guru-nip="<?php echo e($siswa->guru->id_guru ?? '-'); ?>"
                                                        data-guru-hp="<?php echo e($siswa->guru->no_hp ?? '-'); ?>"
                                                        data-pl-nama="<?php echo e($siswa->pembimbing->nama ?? '-'); ?>"
                                                        data-pl-nip="<?php echo e($siswa->id_pembimbing ?? '-'); ?>"
                                                        data-pl-hp="<?php echo e($siswa->pembimbing->no_telp ?? '-'); ?>"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-id-card"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Jurnal"
                                                        data-url="<?php echo e(route('guru.rekap.jurnal', $siswa->nisn)); ?>"
                                                        style= "padding: 6px 19px 6px 16px;">
                                                        <i class="fas fa-book"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Absensi"
                                                        data-url="<?php echo e(route('guru.rekap.absensi', $siswa->nisn)); ?>">
                                                        <i class="fas fa-file-signature"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-end gap-2">
                                                    
                                                    <button class="btn-small btn-preview-pdf" title="Penilaian Pembimbing"
                                                        data-url="<?php echo e(route('guru.siswa.cetakPenilaianPembimbing', $siswa->nisn)); ?>">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Laporan Akhir Siswa"
                                                        data-url="<?php echo e(route('guru.siswa.cetakLaporan', $siswa->nisn)); ?>">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Sertifikat Magang"
                                                        data-url="<?php echo e(route('guru.siswa.cetakSertifikat', $siswa->nisn)); ?>">
                                                        <i class="fas fa-certificate"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="empty-row text-center p-5">
                                                <i class="fas fa-history fa-5x mb-3 text-muted opacity-25"></i>
                                                <p class="text-muted">Belum ada riwayat siswa binaan yang selesai.</p>
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

    
    <div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fas fa-users-viewfinder me-3 text-primary"></i> <span
                            id="modalGroupName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-4 py-3">
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Klik Logbook atau Absensi
                            untuk melihat detail masing-masing siswa.</p>
                    </div>
                    <div class="table-responsive px-4 pb-4">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Identitas Siswa</th>
                                    <th class="text-center">Status Hari Ini</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modalGroupBody">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pe-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-success border-0 shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>
                            <div class="fw-bold">Berhasil!</div>
                            <div class="small opacity-75"><?php echo e(session('success')); ?></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('warning')): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-warning border-0 shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-4 text-dark"></i>
                        <div class="text-dark">
                            <div class="fw-bold">Peringatan</div>
                            <div class="small opacity-75"><?php echo e(session('warning')); ?></div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    </div> 

    
    <div class="modal fade" id="modalDetailSiswa" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">

                
                <div class="modal-header-dark"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; border-radius: 24px 24px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon" style="background: rgba(255,255,255,0.1); color: #3b82f6;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="modal-header-title text-start">
                            <h5 class="fw-bold text-white mb-0">Informasi Lengkap Siswa</h5>
                            <p class="mb-0 text-white-50 small text-start">Biodata diri dan riwayat penempatan magang
                                aktif.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                
                <div class="modal-form-body bg-light" style="padding: 2.5rem;">
                    <div class="row g-4">

                        
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">

                                
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-user-circle"></i> Data Personal & Kontak
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Nama Lengkap</label>
                                            <span class="detail-value text-primary" id="det_name">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">NISN Siswa</label>
                                            <span class="detail-value" id="det_nisn">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Jenis Kelamin</label>
                                            <span class="detail-value" id="det_jk">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">No. WhatsApp</label>
                                            <span class="detail-value" id="det_hp">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Alamat Email Resmi</label>
                                            <span class="detail-value fw-normal" id="det_email">-</span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-university"></i> Identitas Pendidikan
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Kelas & Jurusan</label>
                                            <span class="detail-value" id="det_kelas_jurusan">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Tahun Ajaran</label>
                                            <span class="detail-value text-info" id="det_tahun_ajaran">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Lembaga Pendidikan Asal</label>
                                            <span class="detail-value" id="det_sekolah">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">NPSN Sekolah</label>
                                            <span class="detail-value" id="det_npsn">-</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">

                                
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-building-circle-check"></i> Penempatan & Tipe
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Instansi / Lokasi Magang</label>
                                            <span class="detail-value text-success" id="det_perusahaan">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item">
                                            <label class="detail-label">Tipe Magang</label>
                                            <span class="detail-value badge text-dark bg-light border px-3"
                                                id="det_tipe_magang"
                                                style="display:inline-block; font-size: 0.8rem; border-radius: 50px;">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item">
                                            <label class="detail-label">NISN Ketua (Jika Kelompok)</label>
                                            <span class="detail-value" id="det_nisn_ketua">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Durasi Waktu Magang</label>
                                            <span class="detail-value" id="det_periode">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Surat Balasan / Referensi</label>
                                            <div id="det_surat_balasan_wrapper"
                                                class="mt-1 d-flex align-items-center gap-3">
                                                <span class="detail-value text-muted italic" id="det_surat_balasan">Belum
                                                    diunggah</span>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill d-none"
                                                    id="btn_view_surat">
                                                    <i class="fas fa-file-invoice me-1"></i> Preview
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-user-shield"></i> Tim Pembimbing Resmi
                                    </div>
                                    <div class="d-flex flex-column gap-3">
                                        
                                        <div class="p-3 rounded-4 border bg-white shadow-sm">
                                            <div class="d-flex align-items-center gap-3 mb-3 text-start">
                                                <div class="icon-box-small bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 45px; height: 45px; flex-shrink: 0;">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="text-uppercase fw-bold text-primary"
                                                        style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing Sekolah
                                                    </div>
                                                    <h6 class="mb-0 fw-bold text-dark" id="det_guru_nama">-</h6>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <div class="small text-muted"><i class="fas fa-id-card me-1"></i> <span
                                                        id="det_guru_nip">-</span></div>
                                                <a href="#" id="det_guru_wa_btn"
                                                    class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    <i class="fab fa-whatsapp me-1"></i> Chat Guru
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="p-3 rounded-4 border bg-white shadow-sm">
                                            <div class="d-flex align-items-center gap-3 mb-3 text-start">
                                                <div class="icon-box-small bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 45px; height: 45px; flex-shrink: 0;">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="text-uppercase fw-bold text-info"
                                                        style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing
                                                        Lapangan</div>
                                                    <h6 class="mb-0 fw-bold text-dark" id="det_pl_nama">-</h6>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <div class="small text-muted"><i class="fas fa-fingerprint me-1"></i>
                                                    <span id="det_pl_nip">-</span></div>
                                                <a href="#" id="det_pl_wa_btn"
                                                    class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    <i class="fab fa-whatsapp me-1"></i> Chat PL
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                
                <div class="modal-form-footer border-0 pb-4 bg-light d-flex justify-content-center">
                    <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow"
                        data-bs-dismiss="modal">
                        Tutup Informasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title">Preview Laporan</h6>
                    </div>

                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" download>
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        <div class="vr mx-2 opacity-10"></div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>
                <div class="pdf-viewer-body bg-light">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <div class="loader-logo-container">
                                <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div id="pdfErrorMsg" class="d-none">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Gagal memuat file PDF.<br><small>Coba gunakan tombol Unduh.</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <script src="<?php echo e(asset('assets/js/guru/daftarSiswa.js')); ?>?v=<?php echo e(time()); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/daftarSiswa.blade.php ENDPATH**/ ?>