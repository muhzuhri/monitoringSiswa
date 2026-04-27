

<?php $__env->startSection('title', 'Verifikasi Laporan Akhir - SIM Magang'); ?>
<?php $__env->startSection('body-class', 'dashboard-page guru-page'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/guru/verifikasi.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('body'); ?>
    <div class="page-wrapper">

        
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Verifikasi Laporan Akhir</h3>
                <p class="page-subtitle">Tinjau dan proses verifikasi laporan akhir magang siswa bimbingan Anda.</p>
            </div>
            <!-- <div class="pending-badge">
                <span class="pending-label">PENDING VERIFIKASI</span>
                <span class="pending-count"><?php echo e(isset($laporanPending) ? $laporanPending->count() : 0); ?></span>
            </div> -->
            
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
        </div>

        <?php if(session('success')): ?>
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="ui-alert ui-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="ui-alert ui-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo e($errors->first()); ?></span>
            </div>
        <?php endif; ?>

        
        <?php if(isset($laporan)): ?>
                <div class="detail-layout">

                    
                    <div class="detail-main">
                        <div class="ui-card">
                            
                            <div class="file-header">
                                <div class="file-info">
                                    <div class="file-icon-wrap">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="file-title">Berkas Laporan: Versi <?php echo e($laporan->versi); ?></h5>
                                        <small class="file-date">Diunggah pada <?php echo e(\Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y, H:i')); ?></small>
                                    </div>
                                </div>
                                <a href="<?php echo e(asset('storage/' . $laporan->file)); ?>" target="_blank" class="btn-download">
                                    <i class="fas fa-download"></i> Unduh File
                                </a>
                            </div>

                            
                            <div class="report-preview-container">
                                <i class="fas fa-file-pdf file-icon"></i>
                                <h6 class="preview-title">Pratinjau Berkas Tidak Tersedia Langsung</h6>
                                <p class="preview-desc">Silakan unduh berkas untuk meninjau detail laporan secara menyeluruh di perangkat Anda.</p>
                                <a href="<?php echo e(asset('storage/' . $laporan->file)); ?>" target="_blank" class="btn-preview">
                                    <i class="fas fa-external-link-alt"></i> Buka Preview
                                </a>
                            </div>

                            <hr class="divider">

                            
                            <form action="<?php echo e(route('guru.verifikasi.update', $laporan->id_laporan)); ?>" method="POST" class="verify-form" id="verifyForm">
                                <?php echo csrf_field(); ?>

                                
                                <div class="siswa-summary-box">
                                    <div class="siswa-summary-avatar">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div>
                                        <div class="siswa-summary-name"><?php echo e($laporan->siswa->nama); ?></div>
                                        <div class="siswa-summary-meta">
                                            <span><i class="fas fa-id-card"></i> <?php echo e($laporan->siswa->nisn); ?></span>
                                            <span><i class="fas fa-building"></i> <?php echo e($laporan->siswa->perusahaan ?? '-'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label">Keputusan Verifikasi</label>
                                <div class="decision-group">
                                    <input type="radio" class="decision-radio" name="status" id="approve" value="approved"
                                        <?php echo e(old('status') == 'approved' ? 'checked' : ''); ?> required>
                                    <label class="decision-btn decision-approve" for="approve">
                                        <i class="fas fa-check-circle"></i>
                                        <span>SETUJUI</span>
                                    </label>

                                    <input type="radio" class="decision-radio" name="status" id="reject" value="rejected"
                                        <?php echo e(old('status') == 'rejected' ? 'checked' : ''); ?>>
                                    <label class="decision-btn decision-reject" for="reject">
                                        <i class="fas fa-times-circle"></i>
                                        <span>TOLAK</span>
                                    </label>
                                </div>

                                
                                <div id="catatanWrapper" class="catatan-wrapper" style="display: none;">
                                    <label class="form-label" for="catatan">
                                        <i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i>
                                        Alasan Penolakan <span class="catatan-required-badge">WAJIB DIISI</span>
                                    </label>
                                    <textarea name="catatan" id="catatan" class="form-textarea" rows="4"
                                        placeholder="Tuliskan alasan penolakan laporan secara jelas, agar siswa dapat melakukan perbaikan..."
                                    ><?php echo e(old('catatan')); ?></textarea>
                                </div>

                                
                                <div id="catatanApproveWrapper" class="catatan-wrapper" style="display: none;">
                                    <label class="form-label" for="catatanApprove">
                                        <i class="fas fa-comment-alt" style="color:var(--success);"></i>
                                        Catatan / Komentar <span style="font-weight:500; text-transform: none; color: var(--text-muted);">(opsional)</span>
                                    </label>
                                    <textarea name="catatan" id="catatanApprove" class="form-textarea" rows="3"
                                        placeholder="Tambahkan catatan atau apresiasi untuk siswa (opsional)..."
                                    ><?php echo e(old('catatan')); ?></textarea>
                                </div>

                                <div class="form-actions">
                                    <a href="<?php echo e(route('guru.verifikasi')); ?>" class="btn-back-list">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn-verify" id="btnVerify" disabled>
                                        <i class="fas fa-gavel"></i> Pilih Keputusan Dulu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>

            
        <?php else: ?>
            

            
            <div class="tabs-wrapper">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending"
                        type="button" role="tab">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Menunggu Verifikasi (<?php echo e($laporanPending->count()); ?>)</span>
                    </button>
                    <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">
                        <i class="fas fa-check-double"></i>
                        <span>Riwayat Verifikasi</span>
                    </button>
                </div>
            </div>

            <div class="tab-content" id="verifikasiTabContent">

                
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="laporan-grid">
                        <?php $__empty_1 = true; $__currentLoopData = $laporanPending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="ui-card laporan-card">
                                <div class="laporan-card-top">
                                    <span class="badge-versi">VERSI <?php echo e($p->versi); ?></span>
                                    <small class="laporan-date"><?php echo e(\Carbon\Carbon::parse($p->created_at)->format('d M Y')); ?></small>
                                </div>
                                <h5 class="laporan-name"><?php echo e($p->siswa->nama); ?></h5>
                                <p class="laporan-perusahaan"><i class="fas fa-university"></i> <?php echo e($p->siswa->perusahaan); ?></p>

                                <div class="file-preview-box">
                                    <small class="file-preview-label">FILE LAPORAN:</small>
                                    <div class="file-preview-row">
                                        <i class="fas fa-file-pdf file-pdf-icon"></i>
                                        <span class="file-name"><?php echo e(basename($p->file)); ?></span>
                                    </div>
                                </div>

                                <a href="<?php echo e(route('guru.verifikasi.show', $p->id_laporan)); ?>" class="btn-periksa">
                                    Periksa Laporan <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-state">
                                <i class="fas fa-clipboard-check empty-icon"></i>
                                <h4 class="empty-title">Semua Beres!</h4>
                                <p class="empty-desc">Tidak ada laporan baru yang perlu diperiksa saat ini.</p>
                            </div>
                        <?php endif; ?>
                        <div id="noResultsPending" class="empty-state" style="display: none; width: 100%;">
                            <i class="fas fa-search empty-icon"></i>
                            <h4 class="empty-title">Tidak ada hasil</h4>
                            <p class="empty-desc">Tidak ada laporan pending yang cocok dengan pencarian.</p>
                        </div>
                    </div>
                </div>

                
                <div class="tab-pane fade" id="history" role="tabpanel">

                    
                    <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap" style="background:#ffffff; border:1px solid var(--border); border-radius:16px; padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                        <div class="filter-label" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-filter"></i>
                            <span>Filter Periode:</span>
                        </div>
                        <form id="periodeFilterForm" method="GET" action="<?php echo e(route('guru.verifikasi')); ?>" class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0" style="gap:0.75rem;">
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
                                <a href="<?php echo e(route('guru.verifikasi', array_filter(['search' => $search ?? '', 'tab' => 'history']))); ?>"
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
                                        <th class="th-padded">Siswa</th>
                                        <th>Tanggal Update</th>
                                        <th>Status</th>
                                        <th class="th-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $historyLaporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td class="th-padded">
                                                <div class="td-siswa-name"><?php echo e($h->siswa->nama); ?></div>
                                                <div class="td-siswa-nisn"><?php echo e($h->siswa->nisn); ?></div>
                                            </td>
                                            <td class="td-date"><?php echo e(\Carbon\Carbon::parse($h->updated_at)->format('d M Y, H:i')); ?></td>
                                            <td>
                                                <?php if($h->status == 'approved'): ?>
                                                    <span class="status-badge status-ok">Disetujui</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-bad">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="th-end">
                                                <a href="<?php echo e(route('guru.verifikasi.show', $h->id_laporan)); ?>" class="btn-detail">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="empty-row text-center">Belum ada riwayat verifikasi.</td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr id="noResultsHistory" style="display: none;">
                                        <td colspan="5" class="empty-row text-center text-muted">
                                            Tidak ada riwayat yang cocok dengan pencarian.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php if($historyLaporan->hasPages()): ?>
                            <div class="table-pagination">
                                <?php echo e($historyLaporan->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ═══════════════════════════════════
            // FORM VERIFIKASI — Approve / Reject
            // ═══════════════════════════════════
            const approveRadio  = document.getElementById('approve');
            const rejectRadio   = document.getElementById('reject');
            const catatanWrapper        = document.getElementById('catatanWrapper');
            const catatanApproveWrapper = document.getElementById('catatanApproveWrapper');
            const catatanInput          = document.getElementById('catatan');
            const catatanApproveInput   = document.getElementById('catatanApprove');
            const btnVerify             = document.getElementById('btnVerify');

            function handleDecisionChange() {
                const approved = approveRadio && approveRadio.checked;
                const rejected = rejectRadio  && rejectRadio.checked;

                if (catatanWrapper)        catatanWrapper.style.display        = rejected  ? 'block' : 'none';
                if (catatanApproveWrapper) catatanApproveWrapper.style.display = approved  ? 'block' : 'none';

                // Lepas required dari keduanya
                if (catatanInput)        catatanInput.removeAttribute('required');
                if (catatanApproveInput) catatanApproveInput.removeAttribute('required');

                // Pasang required saat TOLAK
                if (rejected && catatanInput) catatanInput.setAttribute('required', 'required');

                // Update tombol
                if (btnVerify) {
                    if (approved) {
                        btnVerify.disabled = false;
                        btnVerify.innerHTML = '<i class="fas fa-check-circle"></i> Setujui Laporan';
                        btnVerify.className = 'btn-verify btn-verify-approve';
                    } else if (rejected) {
                        btnVerify.disabled = false;
                        btnVerify.innerHTML = '<i class="fas fa-times-circle"></i> Tolak Laporan';
                        btnVerify.className = 'btn-verify btn-verify-reject';
                    } else {
                        btnVerify.disabled = true;
                        btnVerify.innerHTML = '<i class="fas fa-gavel"></i> Pilih Keputusan Dulu';
                        btnVerify.className = 'btn-verify';
                    }
                }
            }

            if (approveRadio) approveRadio.addEventListener('change', handleDecisionChange);
            if (rejectRadio)  rejectRadio.addEventListener('change', handleDecisionChange);

            // Trigger on load (jika old() value ada)
            handleDecisionChange();

            // Konfirmasi sebelum kirim
            const verifyForm = document.getElementById('verifyForm');
            if (verifyForm) {
                verifyForm.addEventListener('submit', function(e) {
                    const isReject = rejectRadio && rejectRadio.checked;
                    const catatan  = catatanInput ? catatanInput.value.trim() : '';

                    if (isReject && catatan === '') {
                        e.preventDefault();
                        catatanInput.focus();
                        catatanInput.style.borderColor = 'var(--danger)';
                        return false;
                    }

                    const action = isReject ? 'MENOLAK' : 'MENYETUJUI';
                    if (!confirm(`Apakah Anda yakin ingin ${action} laporan akhir siswa ini? Tindakan ini tidak dapat dibatalkan.`)) {
                        e.preventDefault();
                    }
                });
            }

            // ═══════════════════════════════════
            // LIVE SEARCH — List View
            // ═══════════════════════════════════
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const pendingCards = document.querySelectorAll('#pending .laporan-card');
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
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    // Filter Pending Cards
                    let pendingMatchFound = false;
                    pendingCards.forEach(card => {
                        const text = card.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        card.style.display = isMatch ? 'flex' : 'none';
                        if (isMatch) pendingMatchFound = true;
                    });
                    if (noResultsPending) {
                        noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'block';
                    }

                    // Filter History Table
                    let historyMatchFound = false;
                    historyRows.forEach(row => {
                        if (row.querySelector('.td-siswa-name') === null) return;
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
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/guru/verifikasi.blade.php ENDPATH**/ ?>