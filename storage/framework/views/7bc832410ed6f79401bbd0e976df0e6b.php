

<?php $__env->startSection('title', 'Daftar Siswa Bimbingan - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'pembimbing-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dosen/daftarSiswa.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="dashboard-container mt-4 mb-5">
        
        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title"><i class="fas fa-users text-primary me-2"></i>Daftar Siswa Bimbingan</h2>
                <p class="page-subtitle">Kelola dan pantau seluruh siswa magang di bawah bimbingan Anda.</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <form id="searchForm" class="search-form">
                        <span class="search-icon"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" value="<?php echo e($search); ?>" class="search-input"
                            placeholder="Cari Nama, NISN, atau Perusahaan..." autocomplete="off">
                    </form>
                </div>
            </div>
        </div>

        
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav">
                <button class="tab-button active btn-tab-trigger" data-target="active-students">
                    <i class="fas fa-user-clock"></i>
                    <span>Siswa Bimbingan (<?php echo e($siswasActive->count()); ?>)</span>
                </button>
                <button class="tab-button btn-tab-trigger" data-target="history-students" id="btnTabHistory">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Siswa (<?php echo e($siswasHistory->count()); ?><?php echo e($periodeId ? ' &bull; filtered' : ''); ?>)</span>
                </button>
            </div>
        </div>

        <div class="tab-contents">
            
            <div class="tab-pane-content" id="active-students">
                <div class="content-card">
                    <div class="custom-table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Info Siswa</th>
                                    <th width="20%">Sekolah / Perusahaan</th>
                                    <th width="15%" class="text-center">Status Hari Ini</th>
                                    <th width="25%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="activeTableBody">
                                <?php $__empty_1 = true; $__currentLoopData = $siswasActive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="student-row">
                                        <td class="text-center"><?php echo e($index + 1); ?></td>
                                        <td>
                                            <div class="user-info-cell">
                                                <?php if($siswa->foto_profil): ?>
                                                    <img src="<?php echo e(asset('storage/' . $siswa->foto_profil)); ?>" alt="<?php echo e($siswa->nama); ?>" class="avatar-sm">
                                                <?php else: ?>
                                                    <div class="avatar-sm-placeholder bg-blue-light text-primary">
                                                        <?php echo e(strtoupper(substr($siswa->nama, 0, 1))); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div class="user-details">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold text-dark"><?php echo e($siswa->nama); ?></span>
                                                        <span class="badge bg-success text-white" style="font-size: 0.6rem; padding: 1px 6px; border-radius: 50px;">AKTIF</span>
                                                    </div>
                                                    <span class="text-muted small">NISN: <?php echo e($siswa->nisn); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge-role bg-purple-light text-purple"><i class="fas fa-school me-1"></i> <?php echo e($siswa->sekolah); ?></span>
                                                <span class="small text-muted"><i class="fas fa-building me-1"></i> <?php echo e($siswa->perusahaan); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($siswa->absen_hari_ini): ?>
                                                <span class="status-badge status-hadir text-success"><i class="fas fa-check-circle"></i> Hadir</span>
                                            <?php else: ?>
                                                <span class="status-badge status-absen text-danger"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons justify-content-center">
                                                <button class="btn-action-icon btn-view btn-show-profile" 
                                                        data-siswa="<?php echo e(json_encode($siswa)); ?>" title="Lihat Profil">
                                                    <i class="fas fa-user-circle text-primary"></i> <span class="text-primary">Profil</span>
                                                </button>
                                                <a href="<?php echo e(route('pembimbing.logbook', $siswa->nisn)); ?>" class="btn-action-icon btn-view" title="Logbook">
                                                    <i class="fas fa-book"></i>
                                                </a>
                                                <a href="<?php echo e(route('pembimbing.absensi', $siswa->nisn)); ?>" class="btn-action-icon btn-view" title="Absensi">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="empty-row">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-users-slash empty-icon"></i>
                                                <h4>Belum Ada Siswa</h4>
                                                <p class="text-muted">Tidak ditemukan siswa bimbingan yang aktif.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr id="noResultsActive" style="display: none;">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-search empty-icon"></i>
                                            <h4>Tidak Ditemukan</h4>
                                            <p class="text-muted">Tidak ada siswa yang cocok dengan pencarian di tab ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="tab-pane-content" id="history-students" style="display: none;">

                
                <div class="history-filter-bar">
                    <div class="filter-label">
                        <i class="fas fa-filter"></i>
                        <span>Filter Periode:</span>
                    </div>
                    <form id="periodeFilterForm" method="GET" action="<?php echo e(route('pembimbing.siswa')); ?>" class="filter-form">
                        <?php if($search): ?>
                            <input type="hidden" name="search" value="<?php echo e($search); ?>">
                        <?php endif; ?>
                        
                        <input type="hidden" name="tab" value="history">
                        <select name="periode" id="periodeSelect" class="filter-select" onchange="document.getElementById('periodeFilterForm').submit()">
                            <option value="">-- Semua Periode --</option>
                            <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($opt->id_tahun_ajaran); ?>"
                                    <?php echo e($periodeId == $opt->id_tahun_ajaran ? 'selected' : ''); ?>>
                                    <?php echo e($opt->tahun_ajaran); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($periodeId): ?>
                            <a href="<?php echo e(route('pembimbing.siswa', array_filter(['search' => $search, 'tab' => 'history']))); ?>"
                               class="btn-reset-filter" title="Hapus Filter">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        <?php endif; ?>
                    </form>
                    <?php if($periodeId): ?>
                        <?php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); ?>
                        <?php if($selectedPeriode): ?>
                            <span class="filter-active-badge">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo e($selectedPeriode->tahun_ajaran); ?>

                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="content-card">
                    <div class="custom-table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="28%">Info Siswa</th>
                                    <th width="20%">Sekolah / Perusahaan</th>
                                    <th width="17%" class="text-center">Periode Magang</th>
                                    <th width="12%" class="text-center">Rata-rata Nilai</th>
                                    <th width="18%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                <?php $__empty_1 = true; $__currentLoopData = $siswasHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $penilaian = $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                    ?>
                                    <tr class="student-row">
                                        <td class="text-center"><?php echo e($index + 1); ?></td>
                                        <td>
                                            <div class="user-info-cell">
                                                <?php if($siswa->foto_profil): ?>
                                                    <img src="<?php echo e(asset('storage/' . $siswa->foto_profil)); ?>" alt="<?php echo e($siswa->nama); ?>" class="avatar-sm">
                                                <?php else: ?>
                                                    <div class="avatar-sm-placeholder bg-success-light text-success">
                                                        <?php echo e(strtoupper(substr($siswa->nama, 0, 1))); ?>

                                                    </div>
                                                <?php endif; ?>
                                                <div class="user-details">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold text-dark"><?php echo e($siswa->nama); ?></span>
                                                        <span class="badge bg-secondary text-white" style="font-size: 0.6rem; padding: 1px 6px; border-radius: 50px;">SELESAI</span>
                                                    </div>
                                                    <span class="text-muted small">NISN: <?php echo e($siswa->nisn); ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge-role bg-purple-light text-purple"><i class="fas fa-school me-1"></i> <?php echo e($siswa->sekolah); ?></span>
                                                <span class="small text-muted"><i class="fas fa-building me-1"></i> <?php echo e($siswa->perusahaan); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($siswa->tahunAjaran): ?>
                                                <span class="periode-badge">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    <?php echo e($siswa->tahunAjaran->tahun_ajaran); ?>

                                                </span>
                                            <?php elseif($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang): ?>
                                                <span class="periode-badge periode-badge-plain">
                                                    <?php echo e(\Carbon\Carbon::parse($siswa->tgl_mulai_magang)->format('M Y')); ?>

                                                    &ndash;
                                                    <?php echo e(\Carbon\Carbon::parse($siswa->tgl_selesai_magang)->format('M Y')); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($penilaian): ?>
                                                <span class="eval-badge bg-purple-light text-purple"><?php echo e(number_format($penilaian->rata_rata, 1)); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons justify-content-center">
                                                
                                                <button class="btn-action-icon btn-view btn-show-profile" 
                                                        data-siswa="<?php echo e(json_encode($siswa)); ?>" title="Lihat Profil">
                                                    <i class="fas fa-user-circle text-primary"></i> <span class="text-primary">Profil</span>
                                                </button>
                                                
                                                
                                                <a href="<?php echo e(route('pembimbing.laporan.cetak', $siswa->nisn)); ?>" class="btn-action-icon btn-view" title="Cetak Penilaian" style="background:var(--color-purple-lt); color:var(--color-primary);">
                                                    <i class="fas fa-star-half-alt"></i>
                                                </a>

                                                
                                                <a href="<?php echo e(route('pembimbing.siswa.cetakJurnal', $siswa->nisn)); ?>" class="btn-action-icon btn-view" title="Cetak Jurnal Kegiatan" style="background:var(--color-blue-lt); color:var(--color-blue);">
                                                    <i class="fas fa-book"></i>
                                                </a>

                                                
                                                <a href="<?php echo e(route('pembimbing.siswa.cetakAbsensi', $siswa->nisn)); ?>" class="btn-action-icon btn-view" title="Cetak Absensi Individu" style="background:var(--color-green-lt); color:var(--color-green);">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr class="empty-row">
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-history empty-icon"></i>
                                                <h4>Belum Ada Riwayat</h4>
                                                <p class="text-muted">
                                                    <?php if($periodeId): ?>
                                                        Tidak ada siswa yang menyelesaikan magang pada periode ini.
                                                    <?php else: ?>
                                                        Belum ada siswa yang menyelesaikan bimbingan/penilaian.
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr id="noResultsHistory" style="display: none;">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-search empty-icon"></i>
                                            <h4>Tidak Ditemukan</h4>
                                            <p class="text-muted">Tidak ada siswa yang cocok dengan pencarian di tab ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="custom-modal-overlay" id="studentProfileModalOverlay">
        <div class="custom-modal">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-id-badge text-primary me-2"></i>Profil Lengkap Siswa</h5>
                <button type="button" class="modal-close" id="closeProfileModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="profile-header text-center mb-4">
                    <div class="avatar-lg mx-auto mb-3">
                        <span id="modalInitial" class="avatar-lg-placeholder"></span>
                        <img id="modalPhoto" src="" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: none;">
                    </div>
                    <h4 class="fw-bold mb-1" id="modalName"></h4>
                    <p class="text-muted small mb-0" id="modalNisn"></p>
                </div>

                <div class="profile-info-grid">
                    <div class="info-group">
                        <label>Email</label>
                        <p id="modalEmail"></p>
                    </div>
                    <div class="info-group">
                        <label>No. WhatsApp</label>
                        <p id="modalPhone"></p>
                    </div>
                    <div class="info-group">
                        <label>Asal Sekolah</label>
                        <p id="modalSchool"></p>
                    </div>
                    <div class="info-group">
                        <label>Kelas / Jurusan</label>
                        <p id="modalClass"></p>
                    </div>
                    <div class="info-group">
                        <label>Perusahaan Magang</label>
                        <p id="modalCompany"></p>
                    </div>
                    <div class="info-group">
                        <label>Guru Pembimbing</label>
                        <p id="modalGuru"></p>
                    </div>
                    <div class="info-group col-span-2">
                        <label>Periode Magang</label>
                        <p id="modalPeriod"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Main Tab Switching
            const tabTriggers = document.querySelectorAll('.btn-tab-trigger');
            const tabPanes = document.querySelectorAll('.tab-pane-content');

            function activateTab(targetId) {
                tabTriggers.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(pane => {
                    pane.style.display = pane.id === targetId ? 'block' : 'none';
                });
                const activeBtn = document.querySelector(`.btn-tab-trigger[data-target="${targetId}"]`);
                if (activeBtn) activeBtn.classList.add('active');
            }

            tabTriggers.forEach(btn => {
                btn.addEventListener('click', function() {
                    activateTab(this.getAttribute('data-target'));
                });
            });

            // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
                activateTab('history-students');
            }

            // Live Search Logic for both tables
            const searchInput = document.getElementById('searchInput');
            const activeRows = document.querySelectorAll('#activeTableBody .student-row');
            const historyRows = document.querySelectorAll('#historyTableBody .student-row');
            const noResultsActive = document.getElementById('noResultsActive');
            const noResultsHistory = document.getElementById('noResultsHistory');
            const emptyRows = document.querySelectorAll('.empty-row');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const tables = [
                        { rows: activeRows, noResults: noResultsActive },
                        { rows: historyRows, noResults: noResultsHistory }
                    ];

                    tables.forEach(({ rows, noResults }) => {
                        let hasMatch = false;
                        rows.forEach(row => {
                            const isMatch = row.innerText.toLowerCase().includes(searchTerm);
                            row.style.display = isMatch ? 'table-row' : 'none';
                            if (isMatch) hasMatch = true;
                        });

                        if (searchTerm === '') {
                            noResults.style.display = 'none';
                            emptyRows.forEach(row => row.style.display = 'table-row');
                        } else {
                            emptyRows.forEach(row => row.style.display = 'none');
                            noResults.style.display = hasMatch ? 'none' : 'table-row';
                        }
                    });
                });

                document.getElementById('searchForm').addEventListener('submit', (e) => e.preventDefault());
            }

            // Modal Logic
            const modalOverlay = document.getElementById('studentProfileModalOverlay');
            const closeModalBtn = document.getElementById('closeProfileModal');
            
            function openModal() {
                modalOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modalOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }

            closeModalBtn.addEventListener('click', closeModal);
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) closeModal();
            });

            document.querySelectorAll('.btn-show-profile').forEach(button => {
                button.addEventListener('click', function() {
                    const siswa = JSON.parse(this.getAttribute('data-siswa'));
                    
                    document.getElementById('modalName').innerText = siswa.nama;
                    document.getElementById('modalNisn').innerText = 'NISN: ' + siswa.nisn;
                    document.getElementById('modalEmail').innerText = siswa.email || '-';
                    document.getElementById('modalPhone').innerText = siswa.no_hp || '-';
                    document.getElementById('modalSchool').innerText = siswa.sekolah;
                    document.getElementById('modalClass').innerText = (siswa.kelas || '-') + ' / ' + (siswa.jurusan || '-');
                    document.getElementById('modalCompany').innerText = siswa.perusahaan;
                    document.getElementById('modalGuru').innerText = siswa.guru ? siswa.guru.nama : '-';
                    
                    // Format Period
                    if (siswa.tgl_mulai_magang && siswa.tgl_selesai_magang) {
                        const start = new Date(siswa.tgl_mulai_magang);
                        const end = new Date(siswa.tgl_selesai_magang);
                        const options = { day: 'numeric', month: 'short', year: 'numeric' };
                        document.getElementById('modalPeriod').innerText = start.toLocaleDateString('id-ID', options) + ' - ' + end.toLocaleDateString('id-ID', options);
                    } else {
                        document.getElementById('modalPeriod').innerText = '-';
                    }

                    // Handle Photo/Initial
                    const initialEl = document.getElementById('modalInitial');
                    const photoEl = document.getElementById('modalPhoto');
                    
                    if (siswa.foto_profil) {
                        photoEl.src = "<?php echo e(asset('storage')); ?>/" + siswa.foto_profil;
                        photoEl.style.display = 'block';
                        initialEl.style.display = 'none';
                    } else {
                        initialEl.innerText = siswa.nama.charAt(0).toUpperCase();
                        initialEl.style.display = 'flex';
                        photoEl.style.display = 'none';
                    }
                    
                    openModal();
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.pembimbing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pembimbing/daftarSiswa.blade.php ENDPATH**/ ?>