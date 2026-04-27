<?php $__env->startSection('title', 'Data Siswa - Pimpinan Dashboard'); ?>
<?php $__env->startSection('body-class', 'dashboard-page pimpinan-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/siswa.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pimpinan/siswa-modals.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="<?php echo e(asset('assets/js/pimpinan/siswa.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="management-container" id="siswa-container" 
         data-jurnal-url="<?php echo e(route('admin.rekap.jurnal', ['nisn' => ':nisn'])); ?>"
         data-absensi-url="<?php echo e(route('admin.rekap.absensi', ['nisn' => ':nisn'])); ?>">
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
                    <h5>Data Seluruh Siswa Magang</h5>
                    <p>Memantau biodata dan penempatan siswa magang secara real-time.</p>
                </div>
                <div class="header-actions">
                    <form action="<?php echo e(route('pimpinan.siswa')); ?>" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input
                                type="text"
                                name="search"
                                value="<?php echo e($search); ?>"
                                class="p-input with-icon"
                                placeholder="Cari Siswa / Sekolah / NISN..."
                                onchange="this.form.submit()"
                            >
                        </div>
                    </form>                    
                </div>
            </div>

            
            <div class="tabs-wrapper px-0">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="siswa-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-siswa"
                        type="button" role="tab" aria-controls="pane-siswa" aria-selected="true">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang (<?php echo e($siswa->total()); ?>)</span>
                    </button>
                    <button class="tab-button" id="history-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-riwayat"
                        type="button" role="tab" aria-controls="pane-riwayat" aria-selected="false">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Siswa (<?php echo e($riwayatSiswas->count()); ?>)</span>
                    </button>
                </div>
            </div>

            
            <div class="tab-content">

                
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">

                    <div class="tab-toolbar">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="active">
                                <i class="fas fa-th-large"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list"></i> Seluruh Siswa
                            </button>
                        </div>
                        <div class="tab-toolbar-info">
                            Menampilkan <strong><?php echo e($siswa->count()); ?></strong> siswa aktif pada halaman ini
                        </div>
                    </div>

                    
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                             <?php $__empty_1 = true; $__currentLoopData = $groupedSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                 <div class="col-xl-4 col-md-6">
                                     <div class="student-card">
                                         <div class="card-actions">
                                             <button class="action-round btn-detail"
                                                 data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                 data-nisn="<?php echo e($g['leader']->nisn); ?>" data-nama="<?php echo e($g['leader']->nama); ?>"
                                                 data-email="<?php echo e($g['leader']->email); ?>" data-no_hp="<?php echo e($g['leader']->no_hp); ?>"
                                                 data-kelas="<?php echo e($g['leader']->kelas); ?>" data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                 data-sekolah="<?php echo e($g['leader']->sekolah); ?>" data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                 data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                 data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                 data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>" data-guru-nip="<?php echo e($g['leader']->guru->id_guru ?? '-'); ?>"
                                                 data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>" data-pl-nip="<?php echo e($g['leader']->pembimbing->id_pembimbing ?? '-'); ?>"
                                                 data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>"
                                                 title="Lihat Detail">
                                                 <i class="fas fa-eye"></i>
                                             </button>
                                         </div>
 
                                         <div class="card-identity">
                                             <div class="card-avatar">
                                                 <?php if($g['is_group']): ?>
                                                     <i class="fas fa-users"></i>
                                                 <?php else: ?>
                                                     <?php echo e(strtoupper(substr($g['leader']->nama, 0, 1))); ?>

                                                 <?php endif; ?>
                                             </div>
                                              <div class="card-identity-info">
                                                 <h6><?php echo e(Str::limit($g['leader']->nama, 25)); ?></h6>
                                                 <p>NISN: <?php echo e($g['leader']->nisn); ?></p>
                                                 <?php if($g['is_group']): ?>
                                                     <span class="badge-info-light">(<?php echo e($g['members']->count()); ?> Siswa)</span>
                                                 <?php endif; ?>
                                             </div>
                                         </div>
 
                                         <div class="card-info-list">
                                             <div class="card-info-row">
                                                 <span class="card-info-label">Sekolah</span>
                                                 <span class="card-info-value"><?php echo e(Str::limit($g['leader']->sekolah, 22)); ?></span>
                                             </div>
                                             <div class="card-info-row">
                                                 <span class="card-info-label">Penempatan</span>
                                                 <span class="card-info-value"><?php echo e(Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22)); ?></span>
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
                                                         data-type="active"
                                                         data-members="<?php echo e($g['members']->map(function($m) {
                                                             return [
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
                                                                 'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                 'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                 'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                 'status' => $m->status
                                                             ];
                                                         })->toJson()); ?>">
                                                     <i class="fas fa-users-viewfinder"></i> Anggota
                                                 </button>
                                             <?php else: ?>
                                                 <button class="btn-action detail-btn btn-detail"
                                                     data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                     data-nisn="<?php echo e($g['leader']->nisn); ?>" data-nama="<?php echo e($g['leader']->nama); ?>" data-email="<?php echo e($g['leader']->email); ?>"
                                                     data-no_hp="<?php echo e($g['leader']->no_hp); ?>" data-kelas="<?php echo e($g['leader']->kelas); ?>" data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                     data-sekolah="<?php echo e($g['leader']->sekolah); ?>" data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                     data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                     data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                     data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>" data-guru-nip="<?php echo e($g['leader']->id_guru ?? '-'); ?>"
                                                     data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>" data-pl-nip="<?php echo e($g['leader']->id_pembimbing ?? '-'); ?>"
                                                     data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>">
                                                     <i class="fas fa-info-circle"></i> Detail
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
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah / Penempatan</th>
                                        <th>Status Hari Ini</th>
                                        <th>Pembimbing</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="cell-name"><?php echo e($s->nama); ?></div>
                                                <div class="cell-sub"><?php echo e($s->nisn); ?> &middot; <?php echo e($s->kelas); ?></div>
                                            </td>
                                            <td>
                                                <div class="cell-sub"><i class="fas fa-university me-1 text-primary opacity-75"></i> <?php echo e($s->sekolah); ?></div>
                                                <div class="cell-sub"><i class="fas fa-building me-1 opacity-50"></i> <?php echo e($s->perusahaan ?? '-'); ?></div>
                                            </td>
                                            <td>
                                                <?php if($s->absen_hari_ini): ?>
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle"></i> Hadir
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-times-circle"></i> Belum Absen
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="cell-sub"><i class="fas fa-chalkboard-teacher me-1 opacity-50"></i> <?php echo e($s->guru->nama ?? '-'); ?></div>
                                                <div class="cell-sub"><i class="fas fa-user-tie me-1 opacity-50"></i> <?php echo e($s->pembimbing->nama ?? '-'); ?></div>
                                            </td>
                                            <td>
                                                 <div class="action-group justify-content-end">
                                                     <button class="btn-icon btn-detail-soft btn-detail"
                                                         data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                         data-nisn="<?php echo e($s->nisn); ?>" data-nama="<?php echo e($s->nama); ?>" data-email="<?php echo e($s->email); ?>"
                                                         data-no_hp="<?php echo e($s->no_hp); ?>" data-kelas="<?php echo e($s->kelas); ?>" data-jurusan="<?php echo e($s->jurusan); ?>"
                                                         data-sekolah="<?php echo e($s->sekolah); ?>" data-perusahaan="<?php echo e($s->perusahaan); ?>"
                                                         data-mulai="<?php echo e($s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                         data-selesai="<?php echo e($s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                         data-guru-nama="<?php echo e($s->guru->nama ?? '-'); ?>" data-guru-nip="<?php echo e($s->guru->id_guru ?? '-'); ?>"
                                                         data-pl-nama="<?php echo e($s->pembimbing->nama ?? '-'); ?>" data-pl-nip="<?php echo e($s->pembimbing->id_pembimbing ?? '-'); ?>"
                                                         data-pl-hp="<?php echo e($s->pembimbing->no_telp ?? '-'); ?>"
                                                         title="Lihat Detail Profil">
                                                         <i class="fas fa-id-card text-primary"></i>
                                                     </button>
                                                 </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center p-5 text-muted">
                                                Tidak ada data siswa aktif.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="pane-riwayat" role="tabpanel">

                    <div class="tab-toolbar">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="riwayat">
                                <i class="fas fa-th-large"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="riwayat">
                                <i class="fas fa-list"></i> Seluruh Riwayat
                            </button>
                        </div>
                        
                        <div class="tab-toolbar-info">
                            Menampilkan <strong><?php echo e($riwayatSiswas->count()); ?></strong> riwayat siswa magang
                        </div>

                        
                        <div class="ms-auto" style="min-width: 200px;">
                            <form action="<?php echo e(route('pimpinan.siswa')); ?>" method="GET" id="filterRiwayatForm" class="d-flex gap-2">
                                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                                <select name="periode" class="p-input small-select" onchange="this.form.submit()" style="padding: 0.5rem 1rem; height: auto;">
                                    <option value="">Semua Periode</option>
                                    <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id_tahun_ajaran); ?>" <?php echo e($periodeId == $p->id_tahun_ajaran ? 'selected' : ''); ?>>
                                            <?php echo e($p->nama_tahun_ajaran); ?>

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
                                                     <?php echo e(Str::limit($g['leader']->nama, 22)); ?>

                                                     <?php if($g['is_group']): ?>
                                                         <span class="badge-info-light">(<?php echo e($g['members']->count()); ?> Anggota)</span>
                                                     <?php endif; ?>
                                                 </h6>
                                                 <p class="student-nisn">NISN: <?php echo e($g['leader']->nisn); ?></p>
                                                 <div class="mt-2 d-flex gap-2 align-items-center">
                                                     <span class="badge-status hadir" style="background: #ecfdf5; color: #059669; font-size: 0.65rem; padding: 2px 10px; border-radius: 50px; font-weight: 700;">
                                                         <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                                     </span>
                                                     <span class="badge-status" style="background: #f1f5f9; color: #64748b; font-size: 0.65rem; padding: 2px 10px; border-radius: 50px; font-weight: 700;">
                                                         <i class="fas fa-archive me-1"></i> Arsip
                                                     </span>
                                                 </div>
                                             </div>
                                         </div>
 
                                         
                                         <div class="info-grid">
                                             <div class="info-item">
                                                 <label class="info-label"><i class="fas fa-university"></i> SEKOLAH</label>
                                                 <span class="info-value" title="<?php echo e($g['leader']->sekolah); ?>"><?php echo e($g['leader']->sekolah); ?></span>
                                             </div>
                                             <div class="info-item">
                                                 <label class="info-label"><i class="fas fa-building"></i> INSTANSI</label>
                                                 <span class="info-value" title="<?php echo e($g['leader']->perusahaan); ?>"><?php echo e($g['leader']->perusahaan ?? '-'); ?></span>
                                             </div>
                                         </div>
 
                                         
                                         <div class="action-grid">
                                             <?php if($g['is_group']): ?>
                                                 <button class="btn-action btn-show-members" 
                                                         data-name="<?php echo e($g['leader']->nama); ?>"
                                                         data-type="history"
                                                         data-members="<?php echo e($g['members']->map(function($m) {
                                                             return [
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
                                                                 'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                 'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                 'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                 'status' => $m->status
                                                             ];
                                                         })->toJson()); ?>">
                                                     <i class="fas fa-users-viewfinder"></i> Anggota
                                                 </button>
                                             <?php else: ?>
                                                 <button class="btn-action btn-detail"
                                                     data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                     data-nisn="<?php echo e($g['leader']->nisn); ?>" data-nama="<?php echo e($g['leader']->nama); ?>"
                                                     data-email="<?php echo e($g['leader']->email); ?>" data-no_hp="<?php echo e($g['leader']->no_hp); ?>"
                                                     data-kelas="<?php echo e($g['leader']->kelas); ?>" data-jurusan="<?php echo e($g['leader']->jurusan); ?>"
                                                     data-sekolah="<?php echo e($g['leader']->sekolah); ?>" data-perusahaan="<?php echo e($g['leader']->perusahaan); ?>"
                                                     data-mulai="<?php echo e($g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                     data-selesai="<?php echo e($g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                     data-guru-nama="<?php echo e($g['leader']->guru->nama ?? '-'); ?>" data-guru-nip="<?php echo e($g['leader']->guru->id_guru ?? '-'); ?>"
                                                     data-pl-nama="<?php echo e($g['leader']->pembimbing->nama ?? '-'); ?>" data-pl-nip="<?php echo e($g['leader']->pembimbing->id_pembimbing ?? '-'); ?>"
                                                     data-pl-hp="<?php echo e($g['leader']->pembimbing->no_telp ?? '-'); ?>">
                                                     <i class="fas fa-user-circle"></i> Detail
                                                 </button>
                                             <?php endif; ?>
                                             
                                             <button class="btn-action btn-preview-pdf" 
                                                     data-url="<?php echo e(route('admin.rekap.kelompok', $g['leader']->nisn)); ?>">
                                                 <i class="fas fa-file-pdf"></i> Rekap Absensi
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="view-container d-none" id="riwayat-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Siswa Magang</th>
                                        <th>Sekolah / Instansi</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $riwayatSiswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <div class="cell-name"><?php echo e($rs->nama); ?></div>
                                                <div class="cell-sub">NISN: <?php echo e($rs->nisn); ?></div>
                                            </td>
                                            <td>
                                                <div class="cell-name"><?php echo e($rs->sekolah); ?></div>
                                                <div class="cell-sub"><?php echo e($rs->perusahaan ?? '-'); ?></div>
                                            </td>
                                            <td>
                                                <div class="cell-sub">
                                                    <i class="fas fa-calendar-alt me-1 text-primary opacity-50"></i>
                                                    <?php echo e($rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-'); ?> - 
                                                    <?php echo e($rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-'); ?>

                                                </div>
                                            </td>
                                            <td>
                                                 <div class="action-group justify-content-end gap-2">
                                                     <button class="btn-icon btn-detail-soft btn-detail"
                                                         data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                         data-nisn="<?php echo e($rs->nisn); ?>" data-nama="<?php echo e($rs->nama); ?>" data-email="<?php echo e($rs->email); ?>"
                                                         data-no_hp="<?php echo e($rs->no_hp); ?>" data-kelas="<?php echo e($rs->kelas); ?>" data-jurusan="<?php echo e($rs->jurusan); ?>"
                                                         data-sekolah="<?php echo e($rs->sekolah); ?>" data-perusahaan="<?php echo e($rs->perusahaan); ?>"
                                                         data-mulai="<?php echo e($rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-'); ?>"
                                                         data-selesai="<?php echo e($rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-'); ?>"
                                                         data-guru-nama="<?php echo e($rs->guru->nama ?? '-'); ?>" data-guru-nip="<?php echo e($rs->guru->id_guru ?? '-'); ?>"
                                                         data-pl-nama="<?php echo e($rs->pembimbing->nama ?? '-'); ?>" data-pl-nip="<?php echo e($rs->pembimbing->id_pembimbing ?? '-'); ?>"
                                                         data-pl-hp="<?php echo e($rs->pembimbing->no_telp ?? '-'); ?>"
                                                         title="Lihat Detail Profil">
                                                         <i class="fas fa-id-card text-primary"></i>
                                                     </button>
                                                     <button class="btn-icon btn-detail-soft btn-preview-pdf" 
                                                         data-url="<?php echo e(route('admin.rekap.jurnal', $rs->nisn)); ?>"
                                                         title="Lihat Jurnal Individu">
                                                         <i class="fas fa-book text-info"></i>
                                                     </button>
                                                     <button class="btn-icon btn-detail-soft btn-preview-pdf" 
                                                         data-url="<?php echo e(route('admin.rekap.absensi', $rs->nisn)); ?>"
                                                         title="Lihat Absensi Individu">
                                                         <i class="fas fa-file-signature text-success"></i>
                                                     </button>
                                                 </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                Belum ada riwayat siswa tersedia.
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

    
    <?php echo $__env->make('pimpinan.siswa_modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pimpinan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/siswa.blade.php ENDPATH**/ ?>