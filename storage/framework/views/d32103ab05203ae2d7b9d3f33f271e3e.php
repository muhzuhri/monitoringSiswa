
<div class="modal fade" id="modalDetailDosen" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header-dark" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-dark" style="background: rgba(255,255,255,0.1); color: #10b981;">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5 class="fw-bold text-white mb-0">Profil Pembimbing Lapangan</h5>
                        <p class="mb-0 text-white-50 small">Detail informasi mitra perusahaan/lapangan.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-form-body bg-light" style="padding: 2rem;">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-card h-100 shadow-sm" style="background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05);">
                            <div class="detail-card-title p-3 border-bottom text-primary fw-bold" style="font-size: 0.9rem;">
                                <i class="fas fa-user-circle me-2"></i> Informasi Personal
                            </div>
                            <div class="p-3">
                                <div class="detail-item mb-3">
                                    <label class="text-muted extra-small d-block text-uppercase fw-bold mb-1">Nama Lengkap</label>
                                    <span id="det_name" class="fw-bold text-dark">-</span>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted extra-small d-block text-uppercase fw-bold mb-1">Jabatan di Perusahaan</label>
                                    <span id="det_jabatan" class="text-dark fw-medium">-</span>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted extra-small d-block text-uppercase fw-bold mb-1">Alamat Email</label>
                                    <span id="det_email" class="text-primary fw-medium">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="text-muted extra-small d-block text-uppercase fw-bold mb-1">No. HP / WhatsApp</label>
                                    <span id="det_telp" class="text-dark fw-medium">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card h-100 shadow-sm" style="background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05);">
                            <div class="detail-card-title p-3 border-bottom text-success fw-bold" style="font-size: 0.9rem;">
                                <i class="fas fa-building me-2"></i> Instansi Penempatan
                            </div>
                            <div class="p-3">
                                <div class="detail-item">
                                    <label class="text-muted extra-small d-block text-uppercase fw-bold mb-1">Asal Instansi / Mitra</label>
                                    <span id="det_instansi" class="text-success fw-bold h6 mb-0 d-block">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="supervisors-panel shadow-sm" style="background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); overflow: hidden;">
                            <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                                <p class="supervisors-panel-title mb-0 fw-bold text-dark"><i class="fas fa-users-viewfinder me-2 text-primary"></i> Daftar Siswa Ploting</p>
                                
                                
                                <div class="d-flex align-items-center gap-2">
                                    <label class="small text-muted fw-bold mb-0">Filter Periode:</label>
                                    <select id="filter_periode_pl" class="form-select form-select-sm" style="width: 15rem; border-radius: 8px;">
                                        <option value="all">Semua Periode</option>
                                        <?php $__currentLoopData = $periodeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($p->id_tahun_ajaran); ?>"><?php echo e($p->tahun_ajaran); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div id="supervised_students_list" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 p-3" style="min-height: 100px;">
                                <!-- Populated via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer bg-light p-3 d-flex justify-content-center border-top">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/pembimbing_modals.blade.php ENDPATH**/ ?>