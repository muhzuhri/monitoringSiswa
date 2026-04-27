<?php $__env->startSection('title', 'Penilaian Magang Siswa - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/penilaian.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Penilaian Akhir Magang</h3>
                <p class="page-subtitle">Kelola dan berikan nilai akhir untuk siswa bimbingan Anda.</p>
            </div>
            
            <div class="search-section">
            <form id="searchForm" class="search-form">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" 
                        placeholder="Cari nama siswa atau NISN..." value="<?php echo e($search ?? ''); ?>" autocomplete="off">
                </div>
            </form>
            </div>
            <?php if(isset($siswa)): ?>
                <a href="<?php echo e(route('guru.penilaian')); ?>" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(isset($siswasPending) && isset($siswasDone)): ?>
            

            
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending"
                        type="button" role="tab">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Menunggu Penilaian (<?php echo e($siswasPending->count()); ?>)</span>
                    </button>
                    <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">
                        <i class="fas fa-check-double"></i>
                        <span>Riwayat Penilaian (<?php echo e($siswasDone->count()); ?><?php echo e(isset($periodeId) && $periodeId ? ' • filtered' : ''); ?>)</span>
                    </button>
                    <button class="tab-button" id="kriteria-tab" data-bs-toggle="pill" data-bs-target="#kriteria"
                        type="button" role="tab">
                        <i class="fas fa-sliders-h"></i>
                        <span>Kategori Penilaian</span>
                    </button>
                </div>
            </div>

            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswasPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><strong><?php echo e($s->nama); ?></strong></td>
                                            <td><?php echo e($s->nisn); ?></td>
                                            <td><?php echo e($s->perusahaan); ?></td>
                                            <td><span class="badge-status status-pending">Belum Dinilai</span></td>
                                            <td class="text-end">
                                                <a href="<?php echo e(route('guru.penilaian.input', $s->nisn)); ?>" class="btn-action btn-primary">
                                                    <i class="fas fa-edit"></i> Input Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-clipboard-check mb-3 fa-3x text-muted"></i>
                                                <p>Tidak ada siswa yang menunggu penilaian.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr id="noResultsPending" style="display: none;">
                                        <td colspan="5" class="empty-state text-center py-4 text-muted">
                                            Tidak ada siswa yang cocok dengan pencarian di tab ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="history" role="tabpanel">

                    
                    <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap" style="background:#ffffff; border:1px solid var(--border); border-radius:16px; padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                        <div class="filter-label" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-filter"></i>
                            <span>Filter Periode:</span>
                        </div>
                        <form id="periodeFilterForm" method="GET" action="<?php echo e(route('guru.penilaian')); ?>" class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0" style="gap:0.75rem;">
                            <?php if(isset($search)): ?>
                                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                            <?php endif; ?>
                            <input type="hidden" name="tab" value="history">
                            <select name="periode" id="periodeSelect" class="form-select" style="min-width:220px; border-radius:12px; font-weight:600; font-size:0.85rem; border:1.5px solid var(--border); cursor:pointer;" onchange="document.getElementById('periodeFilterForm').submit()">
                                <option value="">-- Semua Periode --</option>
                                <?php if(isset($periodeOptions)): ?>
                                    <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($opt->id_tahun_ajaran); ?>"
                                            <?php echo e((isset($periodeId) && $periodeId == $opt->id_tahun_ajaran) ? 'selected' : ''); ?>>
                                            <?php echo e($opt->tahun_ajaran); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <?php if(isset($periodeId) && $periodeId): ?>
                                <a href="<?php echo e(route('guru.penilaian', array_filter(['search' => $search ?? '', 'tab' => 'history']))); ?>"
                                   class="btn btn-outline-danger btn-sm" style="border-radius:12px; font-weight:700; padding:0.45rem 1rem;" title="Hapus Filter">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            <?php endif; ?>
                        </form>
                        <?php if(isset($periodeId) && $periodeId && isset($periodeOptions)): ?>
                            <?php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); ?>
                            <?php if($selectedPeriode): ?>
                                <span class="badge bg-primary ms-auto mt-2 mt-md-0" style="padding:0.5rem 1rem; border-radius:99px; font-weight:800; font-size:0.75rem;">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo e($selectedPeriode->tahun_ajaran); ?>

                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Rata-rata</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $siswasDone; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $p = $s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
                                        ?>
                                        <tr>
                                            <td><strong><?php echo e($s->nama); ?></strong></td>
                                            <td><?php echo e($s->nisn); ?></td>
                                            <td><span class="fw-bold text-primary"><?php echo e(number_format($p->rata_rata, 1)); ?></span></td>
                                            <td><span class="badge-status status-done">Sudah Dinilai</span></td>
                                            <td class="col-aksi">
                                                <div class="table-aksi-flex">
                                                    <a href="<?php echo e(route('guru.penilaian.input', $s->nisn)); ?>" class="btn-action btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="<?php echo e(route('guru.penilaian.export', $s->nisn)); ?>" class="btn-action btn-success">
                                                        <i class="fas fa-print"></i> Cetak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-history mb-3 fa-3x text-muted"></i>
                                                <p>Belum ada riwayat penilaian.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr id="noResultsHistory" style="display: none;">
                                        <td colspan="5" class="empty-state text-center py-4 text-muted">
                                            Tidak ada data yang cocok dengan pencarian di tab ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="kriteria" role="tabpanel">
                    <div class="kriteria-header">
                        <div>
                            <h4 class="kriteria-title"><i class="fas fa-sliders-h"></i> Pengaturan Kriteria Penilaian</h4>
                            <p class="kriteria-subtitle">Sesuaikan kriteria penilaian. Semua perubahan akan langsung diterapkan pada form penilaian siswa.</p>
                        </div>
                        <button class="btn-action btn-primary" data-bs-toggle="modal" data-bs-target="#addCriteriaModal">
                            <i class="fas fa-plus"></i> Tambah Kriteria
                        </button>
                    </div>

                    
                    <div class="section-divider mb-3">
                        <h5 class="section-subtitle-styled"><i class="fas fa-user-check me-2"></i> I. KEPRIBADIAN / ETOS KERJA</h5>
                    </div>
                    <div class="ui-card mb-5">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="80">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="150" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $countKepribadian = 0; ?>
                                    <?php $__currentLoopData = $kriteriaKustom->where('tipe', 'guru_kepribadian'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $countKepribadian++; ?>
                                        <tr>
                                            <td class="col-urutan"><span class="fw-bold text-muted">#<?php echo e($k->urutan); ?></span></td>
                                            <td class="fw-semibold"><?php echo e($k->nama_kriteria); ?></td>
                                            <td class="col-aksi">
                                                <button class="btn btn-sm btn-outline-primary btn-table-action" data-bs-toggle="modal" data-bs-target="#editCriteriaModal"
                                                        data-id="<?php echo e($k->id_kriteria); ?>"
                                                        data-nama="<?php echo e($k->nama_kriteria); ?>"
                                                        data-tipe="<?php echo e($k->tipe); ?>"
                                                        data-urutan="<?php echo e($k->urutan); ?>"
                                                        onclick="setupEditCriteria(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="<?php echo e(route('guru.kriteria.destroy', $k->id_kriteria)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kriteria ini?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($countKepribadian === 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada kriteria kepribadian.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                    <div class="section-divider mb-3 mt-4">
                        <h5 class="section-subtitle-styled"><i class="fas fa-tools me-2"></i> II. KEMAMPUAN</h5>
                    </div>
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="80">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="150" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $countKemampuan = 0; ?>
                                    <?php $__currentLoopData = $kriteriaKustom->where('tipe', 'guru_kemampuan'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $countKemampuan++; ?>
                                        <tr>
                                            <td class="col-urutan"><span class="fw-bold text-muted">#<?php echo e($k->urutan); ?></span></td>
                                            <td class="fw-semibold"><?php echo e($k->nama_kriteria); ?></td>
                                            <td class="col-aksi">
                                                <button class="btn btn-sm btn-outline-primary btn-table-action" data-bs-toggle="modal" data-bs-target="#editCriteriaModal"
                                                        data-id="<?php echo e($k->id_kriteria); ?>"
                                                        data-nama="<?php echo e($k->nama_kriteria); ?>"
                                                        data-tipe="<?php echo e($k->tipe); ?>"
                                                        data-urutan="<?php echo e($k->urutan); ?>"
                                                        onclick="setupEditCriteria(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="<?php echo e(route('guru.kriteria.destroy', $k->id_kriteria)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kriteria ini?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($countKemampuan === 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada kriteria kemampuan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif(isset($siswa)): ?>
            
            <div class="ui-card">
                <div class="card-header">
                    <h4 class="card-title">Form Penilaian Siswa</h4>
                    <p class="card-description">Nama: <strong><?php echo e($siswa->nama); ?></strong> | NISN: <strong><?php echo e($siswa->nisn); ?></strong></p>
                </div>

                <form action="<?php echo e(route('guru.penilaian.store', $siswa->nisn)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Kategori Penilaian</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Penilaian Akhir Magang" <?php echo e(old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Akhir Magang' ? 'selected' : ''); ?>>Penilaian Akhir Magang</option>
                            <option value="Penilaian Tengah Magang" <?php echo e(old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Tengah Magang' ? 'selected' : ''); ?>>Penilaian Tengah Magang</option>
                        </select>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">I. KEPRIBADIAN / ETOS KERJA</h5>
                        <div class="form-grid">
                            <?php $__currentLoopData = $kriteria->where('tipe', 'guru_kepribadian'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                ?>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e($k->nama_kriteria); ?> (0-100)</label>
                                    <input type="number" name="scores[<?php echo e($k->id_kriteria); ?>]" class="form-control" min="0" max="100" 
                                        value="<?php echo e(old('scores.'.$k->id_kriteria, $score)); ?>" required>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">II. KEMAMPUAN</h5>
                        <div class="form-grid">
                            <?php $__currentLoopData = $kriteria->where('tipe', 'guru_kemampuan'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                ?>
                                <div class="form-group">
                                    <label class="form-label"><?php echo e($k->nama_kriteria); ?> (0-100)</label>
                                    <input type="number" name="scores[<?php echo e($k->id_kriteria); ?>]" class="form-control" min="0" max="100" 
                                        value="<?php echo e(old('scores.'.$k->id_kriteria, $score)); ?>" required>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="form-group full-width mb-4">
                        <label class="form-label">Saran / Catatan</label>
                        <textarea name="saran" class="form-control" rows="4" placeholder="Masukkan saran pengembangan untuk siswa..."><?php echo e(old('saran', $penilaian ? $penilaian->saran : '')); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-action btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                            <i class="fas fa-save"></i> Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Tambah Kriteria -->
    <div class="modal fade" id="addCriteriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title custom-modal-title">Tambah Kriteria Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-modal-body">
                    <form action="<?php echo e(route('guru.kriteria.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Kedisiplinan" required>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Tipe Kategori</label>
                            <select name="tipe" class="form-control" required>
                                <option value="guru_kepribadian">Kepribadian / Etos Kerja</option>
                                <option value="guru_kemampuan">Kemampuan</option>
                            </select>
                        </div>
                        <div class="custom-form-group-last">
                            <label class="custom-form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control" value="0">
                        </div>
                        <div class="custom-modal-actions">
                            <button type="button" class="btn-modal btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-modal btn-primary">Simpan Kriteria</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kriteria -->
    <div class="modal fade" id="editCriteriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title custom-modal-title">Edit Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-modal-body">
                    <form id="formEditKriteria" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Tipe Kategori</label>
                            <select name="tipe" id="edit_tipe" class="form-control" required>
                                <option value="guru_kepribadian">Kepribadian / Etos Kerja</option>
                                <option value="guru_kemampuan">Kemampuan</option>
                            </select>
                        </div>
                        <div class="custom-form-group-last">
                            <label class="custom-form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control">
                        </div>
                        <div class="custom-modal-actions">
                            <button type="button" class="btn-modal btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-modal btn-primary">Update Kriteria</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Search Logic
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const pendingRows = document.querySelectorAll('#pending table tbody tr:not(#noResultsPending)');
            const historyRows = document.querySelectorAll('#history table tbody tr:not(#noResultsHistory)');
            const noResultsPending = document.getElementById('noResultsPending');
            const noResultsHistory = document.getElementById('noResultsHistory');

            // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
                const historyTabBtn = document.getElementById('history-tab');
                if (historyTabBtn) {
                    const tab = new bootstrap.Tab(historyTabBtn);
                    tab.show();
                }
            } else if (urlParams.get('tab') === 'kriteria') {
                const kriteriaTabBtn = document.getElementById('kriteria-tab');
                if (kriteriaTabBtn) {
                    const tab = new bootstrap.Tab(kriteriaTabBtn);
                    tab.show();
                }
            }

            // Hide search on kriteria tab
            document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const searchSection = document.querySelector('.search-section');
                    if (searchSection) {
                        searchSection.style.display = e.target.id === 'kriteria-tab' ? 'none' : 'block';
                    }
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Filter Pending Table
                    let pendingMatchFound = false;
                    pendingRows.forEach(row => {
                        if (row.querySelector('strong') === null) return; // Skip empty state row
                        
                        const text = row.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) pendingMatchFound = true;
                    });
                    if (noResultsPending) {
                        noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'table-row';
                    }

                    // Filter History Table
                    let historyMatchFound = false;
                    historyRows.forEach(row => {
                        if (row.querySelector('strong') === null) return; // Skip empty state row
                        
                        const text = row.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) historyMatchFound = true;
                    });
                    if (noResultsHistory) {
                        noResultsHistory.style.display = (historyMatchFound || searchTerm === '') ? 'none' : 'table-row';
                    }
                });

                if (searchForm) {
                    searchForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                    });
                }
            }
        });

        function setupEditCriteria(el) {
            const form = document.getElementById('formEditKriteria');
            form.action = `/guru/kriteria/${el.dataset.id}`;
            document.getElementById('edit_nama').value = el.dataset.nama;
            document.getElementById('edit_tipe').value = el.dataset.tipe;
            document.getElementById('edit_urutan').value = el.dataset.urutan;
        }
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/penilaian.blade.php ENDPATH**/ ?>