{{-- ============================================================
     MODAL: DETAIL SISWA (READ-ONLY FOR PIMPINAN)
============================================================ --}}
<div class="modal fade" id="modalDetailSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header-dark">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-dark">
                        <i class="fas fa-id-card-alt"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5>Informasi Lengkap Siswa</h5>
                        <p>Detail biodata dan penempatan magang.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body --}}
            <div class="modal-form-body">
                <div class="row g-4">

                    {{-- Data Personal --}}
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-card-title text-primary">
                                <i class="fas fa-user-circle"></i> Data Personal
                            </div>
                            <div class="detail-item">
                                <label>Nama Lengkap</label>
                                <span id="det_name">-</span>
                            </div>
                            <div class="detail-item">
                                <label>NISN</label>
                                <span id="det_nisn">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Alamat Email</label>
                                <span id="det_email" class="text-primary">-</span>
                            </div>
                            <div class="detail-item">
                                <label>No. Telepon</label>
                                <span id="det_hp">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Data Akademik --}}
                    <div class="col-md-6">
                        <div class="detail-card">
                            <div class="detail-card-title text-success">
                                <i class="fas fa-university"></i> Data Akademik
                            </div>
                            <div class="detail-item">
                                <label>Kelas & Jurusan</label>
                                <span id="det_kelas_jurusan">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Lembaga Pendidikan</label>
                                <span id="det_sekolah">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Tempat Magang</label>
                                <span id="det_perusahaan" class="text-success">-</span>
                            </div>
                            <div class="detail-item">
                                <label>Periode Magang</label>
                                <span id="det_periode">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tim Pembimbing --}}
                    <div class="col-12">
                        <div class="supervisors-panel">
                            <p class="supervisors-panel-title">Tim Pembimbing</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="supervisor-card">
                                        <span class="supervisor-badge guru">Pembimbing Sekolah</span>
                                        <div class="supervisor-name" id="det_guru_nama">-</div>
                                        <p class="supervisor-meta">NIP/ID: <span id="det_guru_nip">-</span></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="supervisor-card">
                                        <span class="supervisor-badge pl">Pembimbing Lapangan</span>
                                        <div class="supervisor-name" id="det_pl_nama">-</div>
                                        <p class="supervisor-meta">ID: <span id="det_pl_nip">-</span></p>
                                        <p class="supervisor-meta">
                                            <i class="fas fa-phone-alt me-1"></i>
                                            <span id="det_pl_hp">-</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-form-footer">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: ANGGOTA KELOMPOK
============================================================ --}}
<div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header-primary">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="modal-header-title text-white">
                        <h5 class="mb-0">Daftar Anggota Kelompok</h5>
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
                <div class="table-responsive px-4 pb-4">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Identitas Siswa</th>
                                <th class="text-center">Status Magang</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="modalGroupBody">
                            {{-- Rows via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: PREVIEW PDF
============================================================ --}}
<div class="modal fade" id="previewPdfModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon on-dark">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0">Pratinjau Laporan</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-secondary bg-opacity-10" style="height: 80vh;">
                <iframe id="pdfIframe" src="" width="100%" height="100%" frameborder="0"></iframe>
            </div>
            <div class="modal-footer bg-white py-3 px-4">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <p class="text-muted small mb-0 d-none d-md-block">
                        <i class="fas fa-info-circle me-1"></i> Gunakan panel kontrol di atas untuk navigasi PDF.
                    </p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
                        <a id="downloadPdfBtn" href="#" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </a>
                        <button id="printPdfBtn" class="btn btn-dark rounded-pill px-4 fw-bold">
                            <i class="fas fa-print me-2"></i>Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
