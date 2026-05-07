
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


<div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header-primary" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-primary" style="background: rgba(255,255,255,0.1); color: #3b82f6;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="modal-header-title text-white">
                        <h5 class="mb-0 fw-bold">Daftar Anggota Kelompok</h5>
                        <p class="mb-0 text-white-50 small" id="modalGroupName">Nama Kelompok</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="px-4 py-3 bg-light border-bottom">
                    <p class="text-muted small mb-0 fw-bold">
                        <i class="fas fa-info-circle me-1 text-primary"></i> 
                        Menampilkan seluruh siswa yang terdaftar dalam kelompok ini.
                    </p>
                </div>
                <div class="table-responsive px-4 pb-4 mt-3">
                    <table class="main-table w-100" style="border: 1px solid #eee; border-radius: 12px; border-collapse: separate; border-spacing: 0;">
                        <thead style="background: #f8fafc;">
                            <tr>
                                <th class="p-3">Identitas Siswa</th>
                                <th class="text-center p-3">Status</th>
                                <th class="text-end p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modalGroupBody">
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Daftar</button>
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
                    <h5 class="modal-title mb-0">Preview Laporan</h5>
                </div>
                
                <div class="pdf-viewer-actions">
                    <div class="pdf-desktop-actions">
                        <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" target="_blank">
                            <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body pdf-viewer-body">
                <div id="pdfCanvasContainer">
                    <div id="pdfLoadingIndicator">
                        <i class="fas fa-spinner fa-spin fa-3x"></i>
                        <p class="mt-3">Sedang memuat dokumen...</p>
                    </div>
                    <div id="pdfErrorMsg" style="display:none; color: #fff; text-align: center;">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                        <h5>Gagal memuat file PDF</h5>
                        <p>Maaf, terjadi kesalahan saat memuat dokumen. Anda tetap bisa langsung mengunduh file menggunakan tombol di atas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/pimpinan/siswa_modals.blade.php ENDPATH**/ ?>