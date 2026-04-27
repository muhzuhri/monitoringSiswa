
<div class="modal fade" id="modalDetailGuru" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-dark">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-dark">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5>Profil Guru Pembimbing</h5>
                        <p>Informasi personal dan daftar bimbingan.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-form-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-card-title text-primary">
                                <i class="fas fa-user-circle"></i> Informasi Personal
                            </div>
                            <div class="detail-item">
                                <label>Nama Lengkap</label>
                                <span id="det_nama">-</span>
                            </div>
                            <div class="detail-item">
                                <label>NIP / ID</label>
                                <span id="det_id_guru">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Alamat Email</label>
                                <span id="det_email" class="text-primary">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-card-title text-success">
                                <i class="fas fa-school"></i> Instansi & Bidang
                            </div>
                            <div class="detail-item">
                                <label>Sekolah Asal</label>
                                <span id="det_sekolah">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Jabatan / Mapel</label>
                                <span id="det_jabatan">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="supervisors-panel">
                            <p class="supervisors-panel-title">Daftar Siswa Bimbingan</p>
                            <div id="supervised_students_list" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                                <!-- Populated via JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/guru_modals.blade.php ENDPATH**/ ?>