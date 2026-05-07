{{-- ============================================================
     CONSOLIDATED MODALS FOR PIMPINAN MODULE
     Consolidates: guru_modals, pembimbing_modals, siswa_modals
============================================================ --}}


{{-- 1. MODAL: DETAIL GURU (READ-ONLY) --}}
<div class="modal fade" id="modalDetailGuru" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-dark">
                <div class="p-modal-header-content">
                    <div class="modal-header-icon on-dark color-guru">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5 class="text-white p-mb-0">Profil Guru Pembimbing</h5>
                        <p class="text-white p-small p-mb-0 opacity-7">Informasi personal dan daftar bimbingan aktif.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-form-body">
                <div class="p-modal-grid">
                    <div class="p-modal-grid-2-col">
                        <div class="detail-card">
                            <div class="detail-card-title text-primary">
                                <i class="fas fa-user-circle p-gap-2"></i> Informasi Personal
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-item">
                                    <label class="detail-label">Nama Lengkap</label>
                                    <span id="g_det_nama" class="detail-value p-fw-bold">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">NIP / ID Guru</label>
                                    <span id="g_det_id_guru" class="p-badge-pill">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Alamat Email</label>
                                    <span id="g_det_email" class="detail-value text-primary">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <div class="detail-card-title text-success">
                                <i class="fas fa-school p-gap-2"></i> Instansi & Bidang
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-item">
                                    <label class="detail-label">Sekolah Asal</label>
                                    <span id="g_det_sekolah" class="detail-value">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Jabatan / Mapel</label>
                                    <span id="g_det_jabatan" class="detail-value">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="supervisors-panel">
                        <div class="panel-header">
                            <p class="panel-title"><i class="fas fa-users text-primary p-gap-2"></i> Siswa Bimbingan</p>
                            <div class="p-modal-header-content">
                                <label class="p-small text-muted p-fw-bold p-mb-0">Filter:</label>
                                <select id="g_filter_periode" class="form-select modal-select">
                                    <option value="all">Semua Periode</option>
                                    @foreach($periodeOptions as $p)
                                        <option value="{{ $p->id_tahun_ajaran }}">{{ $p->tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="g_supervised_list" class="p-grid-row p-3">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer">
                <button type="button" class="btn-p-dark" data-bs-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>

{{-- 2. MODAL: DETAIL PEMBIMBING LAPANGAN (READ-ONLY) --}}
<div class="modal fade" id="modalDetailDosen" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-dark">
                <div class="p-modal-header-content">
                    <div class="modal-header-icon on-dark color-pl">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5 class="text-white p-mb-0">Profil Pembimbing Lapangan</h5>
                        <p class="text-white p-small p-mb-0 opacity-7">Detail informasi mitra perusahaan/lapangan.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-form-body">
                <div class="p-modal-grid">
                    <div class="p-modal-grid-2-col">
                        <div class="detail-card">
                            <div class="detail-card-title text-primary">
                                <i class="fas fa-user-circle p-gap-2"></i> Informasi Personal
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-item">
                                    <label class="detail-label">Nama Lengkap</label>
                                    <span id="pl_det_nama" class="detail-value p-fw-bold">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Jabatan di Perusahaan</label>
                                    <span id="pl_det_jabatan" class="detail-value fw-medium">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">Alamat Email</label>
                                    <span id="pl_det_email" class="detail-value text-primary">-</span>
                                </div>
                                <div class="detail-item">
                                    <label class="detail-label">No. HP / WhatsApp</label>
                                    <span id="pl_det_telp" class="detail-value">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-card">
                            <div class="detail-card-title text-success">
                                <i class="fas fa-building p-gap-2"></i> Instansi Penempatan
                            </div>
                            <div class="detail-card-body">
                                <div class="detail-item">
                                    <label class="detail-label">Asal Instansi / Mitra</label>
                                    <span id="pl_det_instansi" class="detail-value text-success p-fw-bold">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="supervisors-panel">
                        <div class="panel-header">
                            <p class="panel-title"><i class="fas fa-users-viewfinder text-primary p-gap-2"></i> Siswa Ploting</p>
                            <div class="p-modal-header-content">
                                <label class="p-small text-muted p-fw-bold p-mb-0">Filter:</label>
                                <select id="pl_filter_periode" class="form-select modal-select">
                                    <option value="all">Semua Periode</option>
                                    @foreach($periodeOptions as $p)
                                        <option value="{{ $p->id_tahun_ajaran }}">{{ $p->tahun_ajaran }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="pl_supervised_list" class="p-grid-row p-3">
                            <!-- Populated via JS -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer">
                <button type="button" class="btn-p-dark" data-bs-dismiss="modal">Tutup Profil</button>
            </div>
        </div>
    </div>
</div>

{{-- 3. MODAL: DETAIL SISWA (READ-ONLY) --}}
<div class="modal fade" id="modalDetailSiswa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-dark">
                <div class="p-modal-header-content">
                    <div class="modal-header-icon on-dark color-siswa">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5 class="text-white p-mb-0">Informasi Lengkap Siswa</h5>
                        <p class="text-white p-small p-mb-0 opacity-7">Biodata diri dan riwayat penempatan magang aktif.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-form-body p-large">
                <div class="p-modal-grid-2-col">
                    <div class="p-modal-column">
                        <div class="detail-section-block shadow-sm">
                            <div class="detail-section-title">
                                <i class="fas fa-user-circle"></i> Data Personal & Kontak
                            </div>
                            <div class="p-detail-grid">
                                <div class="p-col-12">
                                    <label class="detail-label">Nama Lengkap</label>
                                    <span class="detail-value text-primary" id="s_det_nama">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">NISN Siswa</label>
                                    <span class="detail-value" id="s_det_nisn">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">Jenis Kelamin</label>
                                    <span class="detail-value" id="s_det_jk">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">No. WhatsApp</label>
                                    <span class="detail-value" id="s_det_hp">-</span>
                                </div>
                                <div class="p-col-12">
                                    <label class="detail-label">Alamat Email Resmi</label>
                                    <span class="detail-value p-fw-normal" id="s_det_email">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="detail-section-block shadow-sm">
                            <div class="detail-section-title">
                                <i class="fas fa-university"></i> Identitas Pendidikan
                            </div>
                            <div class="p-detail-grid">
                                <div class="p-col-6">
                                    <label class="detail-label">Kelas & Jurusan</label>
                                    <span class="detail-value" id="s_det_kelas_jurusan">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">Tahun Ajaran</label>
                                    <span class="detail-value text-info" id="s_det_tahun_ajaran">-</span>
                                </div>
                                <div class="p-col-12">
                                    <label class="detail-label">Lembaga Pendidikan Asal</label>
                                    <span class="detail-value" id="s_det_sekolah">-</span>
                                </div>
                                <div class="p-col-12">
                                    <label class="detail-label">NPSN Sekolah</label>
                                    <span class="detail-value" id="s_det_npsn">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-modal-column">
                        <div class="detail-section-block shadow-sm">
                            <div class="detail-section-title">
                                <i class="fas fa-building-circle-check"></i> Penempatan & Tipe
                            </div>
                            <div class="p-detail-grid">
                                <div class="p-col-12">
                                    <label class="detail-label">Instansi / Lokasi Magang</label>
                                    <span class="detail-value text-success" id="s_det_perusahaan">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">Tipe Magang</label>
                                    <span class="p-badge-pill" id="s_det_tipe_magang">-</span>
                                </div>
                                <div class="p-col-6">
                                    <label class="detail-label">NISN Ketua (Jika Kelompok)</label>
                                    <span class="detail-value" id="s_det_nisn_ketua">-</span>
                                </div>
                                <div class="p-col-12">
                                    <label class="detail-label">Durasi Waktu Magang</label>
                                    <span class="detail-value" id="s_det_periode">-</span>
                                </div>
                                <div class="p-col-12">
                                    <label class="detail-label">Surat Balasan / Referensi</label>
                                    <div id="s_det_surat_wrapper" class="p-mt-1 p-modal-header-content p-gap-3">
                                        <span class="detail-value text-muted p-italic" id="s_det_surat_balasan">Belum diunggah</span>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill d-none" id="s_btn_view_surat">
                                            <i class="fas fa-file-invoice p-gap-2"></i> Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="detail-section-block shadow-sm">
                            <div class="detail-section-title">
                                <i class="fas fa-user-shield"></i> Tim Pembimbing Resmi
                            </div>
                            <div class="user-mini-flex">
                                {{-- Guru --}}
                                <div class="user-mini-card">
                                    <div class="user-mini-header">
                                        <div class="user-mini-icon bg-primary shadow-sm">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </div>
                                        <div class="flex-grow-1 user-mini-info">
                                            <div class="label text-primary">Pembimbing Sekolah</div>
                                            <h6 class="name" id="s_det_guru_nama">-</h6>
                                        </div>
                                    </div>
                                    <div class="user-mini-footer">
                                        <div class="p-small text-muted"><i class="fas fa-id-card p-gap-2"></i> <span id="s_det_guru_nip">-</span></div>
                                        <a href="#" id="s_det_guru_wa_btn" class="btn-wa-mini">
                                            <i class="fab fa-whatsapp"></i> Chat Guru
                                        </a>
                                    </div>
                                </div>
                                {{-- PL --}}
                                <div class="user-mini-card">
                                    <div class="user-mini-header">
                                        <div class="user-mini-icon bg-info shadow-sm">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div class="flex-grow-1 user-mini-info">
                                            <div class="label text-info">Pembimbing Lapangan</div>
                                            <h6 class="name" id="s_det_pl_nama">-</h6>
                                        </div>
                                    </div>
                                    <div class="user-mini-footer">
                                        <div class="p-small text-muted"><i class="fas fa-fingerprint p-gap-2"></i> <span id="s_det_pl_nip">-</span></div>
                                        <a href="#" id="s_det_pl_wa_btn" class="btn-wa-mini">
                                            <i class="fab fa-whatsapp"></i> Chat PL
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer pb-4">
                <button type="button" class="btn-p-dark" data-bs-dismiss="modal">Tutup Informasi</button>
            </div>
        </div>
    </div>
</div>

{{-- 4. MODAL: ANGGOTA KELOMPOK --}}
<div class="modal fade" id="groupMembersModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-primary">
                <div class="p-modal-header-content">
                    <div class="modal-header-icon on-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="p-modal-title-wrapper">
                        <h5 class="text-white p-mb-0">Daftar Anggota Kelompok</h5>
                        <p class="text-white p-small p-mb-0 opacity-7" id="modalGroupName">Nama Kelompok</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="p-0">
                <div class="px-4 py-3 bg-light border-bottom">
                    <p class="text-muted p-small p-mb-0 p-fw-bold">
                        <i class="fas fa-info-circle text-primary p-gap-2"></i> Menampilkan seluruh siswa yang terdaftar dalam kelompok ini.
                    </p>
                </div>
                <div class="modal-table-container">
                    <div class="p-table-responsive mt-3">
                        <table class="modal-table">
                            <thead>
                                <tr>
                                    <th>Identitas Siswa</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modalGroupBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-form-footer pb-4">
                <button type="button" class="btn-p-dark" data-bs-dismiss="modal">Tutup Daftar</button>
            </div>
        </div>
    </div>
</div>

{{-- 5. MODAL: PREVIEW PDF --}}
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
                        <a id="downloadPdfBtn" href="#" class="btn-pdf-action text-decoration-none" title="Unduh File" target="_blank">
                            <i class="fas fa-download"></i> <span>Unduh</span>
                        </a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body pdf-viewer-body">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <div class="loader-logo-container">
                                <img src="{{ asset('images/unsri-pride.png') }}" alt="UNSRI">
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

