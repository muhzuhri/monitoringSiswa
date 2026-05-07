{{-- ============================================================
     MODAL: TAMBAH SISWA
 ============================================================ --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form method="POST" action="{{ route('admin.storeSiswa') }}" enctype="multipart/form-data">
                @csrf

                {{-- Header --}}
                <div class="modal-header-primary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5>Registrasi Siswa Baru</h5>
                            <p>Lengkapi formulir untuk mendaftarkan siswa magang.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-form-body">
                    <div class="row g-4">

                        {{-- Kolom Kiri: Akun & Personal --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Informasi Akun & Personal</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="nama" class="p-input with-icon" required placeholder="Nama Lengkap">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-venus-mars input-icon"></i>
                                    <select name="jenis_kelamin" class="p-input with-icon" required>
                                        <option value="">-- Jenis Kelamin --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="p-input with-icon" required placeholder="Email Resmi">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="text" name="no_hp" class="p-input with-icon" placeholder="No. WhatsApp/HP">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <div class="input-group">
                                            <input type="password" name="password" class="p-input with-icon" required placeholder="Sandi">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-shield-alt input-icon"></i>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="p-input with-icon" required placeholder="Konfirmasi">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>

                        {{-- Detail Akademik & Penempatan --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Detail Akademik</span>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">NISN <span class="req">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-id-card input-icon"></i>
                                        <input type="text" name="nisn" class="p-input with-icon" required placeholder="10 Digit">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Tahun Ajaran <span class="req">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-calendar-check input-icon"></i>
                                        <select name="id_tahun_ajaran" class="p-input with-icon" required>
                                            <option value="">-- Pilih --</option>
                                            @foreach($periodeOptions as $p)
                                                <option value="{{ $p->id_tahun_ajaran }}">{{ $p->tahun_ajaran }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Kelas <span class="req">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-chalkboard input-icon"></i>
                                        <input type="text" name="kelas" class="p-input with-icon" required placeholder="XII-RPL">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted mb-1">Jurusan <span class="req">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-graduation-cap input-icon"></i>
                                        <input type="text" name="jurusan" class="p-input with-icon" required placeholder="Program Keahlian">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Cari NPSN Sekolah</label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-fingerprint input-icon"></i>
                                        <input type="text" name="npsn" id="reg_npsn" class="p-input with-icon" placeholder="Ketik NPSN untuk cari otomatis...">
                                    </div>
                                    <small id="reg_npsn_msg" class="text-muted"></small>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-muted mb-1">Asal Lembaga Sekolah <span class="req">*</span></label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-school input-icon"></i>
                                        <input type="text" name="sekolah" id="reg_sekolah" class="p-input with-icon" required placeholder="Nama Sekolah">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Penempatan & File --}}
                        <div class="col-md-12">
                            <span class="form-section-label">Penempatan & Berkas Magang</span>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted mb-1">Lokasi Penempatan</label>
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-building-circle-check input-icon"></i>
                                        <select name="perusahaan" class="p-input with-icon">
                                            <option value="">-- Pilih Lokasi --</option>
                                            @foreach($lokasis as $lok)
                                                <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted mb-1">Tipe</label>
                                    <div class="p-input-wrapper">
                                        <select name="tipe_magang" class="p-input" required>
                                            <option value="individu">Individu</option>
                                            <option value="kelompok">Kelompok</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted mb-1">NISN Ketua (Grup)</label>
                                    <div class="p-input-wrapper">
                                        <input type="text" name="nisn_ketua" class="p-input" placeholder="Hanya jika Grup">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Surat Balasan (PDF/JPG)</label>
                                    <div class="p-input-wrapper">
                                        <input type="file" name="surat_balasan" class="p-input" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Tgl. Mulai</label>
                                    <div class="p-input-wrapper">
                                        <input type="date" name="tgl_mulai_magang" class="p-input" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted mb-1">Tgl. Selesai</label>
                                    <div class="p-input-wrapper">
                                        <input type="date" name="tgl_selesai_magang" class="p-input" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-form-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom rounded-pill px-5">Daftarkan Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: EDIT SISWA
============================================================ --}}
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formEditSiswa" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="modal-header-warning">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-warning">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5 class="fw-bold">Ubah Profil Siswa</h5>
                            <p class="mb-0">Perbarui informasi data siswa magang secara mendetail.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body: 2-Column Balanced Layout --}}
                <div class="modal-form-body bg-light">
                    <div class="row g-4">

                        {{-- ══ KOLOM KIRI: PERSONAL & AKADEMIK ════════════════ --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">
                                
                                {{-- Card: Informasi Kontak --}}
                                <div class="edit-section-block shadow-sm border-0">
                                    <div class="edit-section-title">
                                        <i class="fas fa-id-badge"></i> Informasi Personal
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="edit-field-label">Nama Lengkap <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-user input-icon"></i>
                                                <input type="text" name="nama" id="edit_nama" class="p-input with-icon" required placeholder="Nama Lengkap">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="edit-field-label">Jenis Kelamin</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-venus-mars input-icon"></i>
                                                <select name="jenis_kelamin" id="edit_jk" class="p-input with-icon">
                                                    <option value="Laki-laki">Laki-laki</option>
                                                    <option value="Perempuan">Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="edit-field-label">No. WhatsApp</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-phone-alt input-icon"></i>
                                                <input type="text" name="no_hp" id="edit_hp" class="p-input with-icon" placeholder="08xx...">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="edit-field-label">Email Resmi <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-envelope input-icon"></i>
                                                <input type="email" name="email" id="edit_email" class="p-input with-icon" required placeholder="email@domain.com">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card: Identitas Akademik --}}
                                <div class="edit-section-block shadow-sm border-0">
                                    <div class="edit-section-title">
                                        <i class="fas fa-university"></i> Identitas Akademik
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="edit-field-label">NISN Siswa <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-id-card input-icon"></i>
                                                <input type="text" name="nisn" id="edit_nisn" class="p-input with-icon" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="edit-field-label">Tahun Ajaran</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-calendar-check input-icon"></i>
                                                <select name="id_tahun_ajaran" id="edit_id_tahun_ajaran" class="p-input with-icon">
                                                    @foreach($periodeOptions as $p)
                                                        <option value="{{ $p->id_tahun_ajaran }}">{{ $p->tahun_ajaran }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="edit-field-label">Kelas <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-chalkboard input-icon"></i>
                                                <input type="text" name="kelas" id="edit_kelas" class="p-input with-icon" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="edit-field-label">Program Keahlian <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-book-open input-icon"></i>
                                                <input type="text" name="jurusan" id="edit_jurusan" class="p-input with-icon" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="edit-field-label">Asal Lembaga Sekolah <span class="req">*</span></label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-school input-icon"></i>
                                                <input type="text" name="sekolah" id="edit_sekolah" class="p-input with-icon" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="edit-field-label">NPSN Sekolah</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-fingerprint input-icon"></i>
                                                <input type="text" name="npsn" id="edit_npsn" class="p-input with-icon">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ══ KOLOM KANAN: PENEMPATAN & KEAMANAN ══════════════ --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">

                                {{-- Card: Penempatan & Tipe --}}
                                <div class="edit-section-block shadow-sm border-0">
                                    <div class="edit-section-title">
                                        <i class="fas fa-map-location-dot"></i> Penempatan & Tipe
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="edit-field-label">Lokasi Penempatan</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-building-circle-check input-icon"></i>
                                                <select name="perusahaan" id="edit_perusahaan" class="p-input with-icon">
                                                    <option value="">-- Pilih Lokasi --</option>
                                                    @foreach($lokasis as $lok)
                                                        <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="edit-field-label">Tipe Magang</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-users-viewfinder input-icon"></i>
                                                <select name="tipe_magang" id="edit_tipe_magang" class="p-input with-icon">
                                                    <option value="individu">Individu</option>
                                                    <option value="kelompok">Kelompok</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="edit-field-label">NISN Ketua (Grup)</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-user-shield input-icon"></i>
                                                <input type="text" name="nisn_ketua" id="edit_nisn_ketua" class="p-input with-icon" placeholder="Hanya isi jika Grup">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="edit-field-label">Surat Balasan (Format PDF/JPG, Max 2MB)</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-file-pdf input-icon"></i>
                                                <input type="file" name="surat_balasan" id="edit_surat_balasan_input" class="p-input with-icon" accept=".pdf,.jpg,.jpeg,.png">
                                            </div>
                                            <small class="text-muted mt-1 d-block" id="edit_surat_balasan_info">Biarkan kosong jika tidak ingin mengubah file.</small>
                                        </div>
                                        <div class="col-6">
                                            <label class="edit-field-label">Tgl. Mulai</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-calendar-day input-icon"></i>
                                                <input type="date" name="tgl_mulai_magang" id="edit_tgl_mulai" class="p-input with-icon">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="edit-field-label">Tgl. Selesai</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-calendar-check input-icon"></i>
                                                <input type="date" name="tgl_selesai_magang" id="edit_tgl_selesai" class="p-input with-icon">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="edit-field-label">Guru Pembimbing</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-graduation-cap input-icon"></i>
                                                <select name="id_guru" id="edit_id_guru" class="p-input with-icon">
                                                    <option value="">-- Pilih Guru --</option>
                                                    @foreach($gurus as $g)
                                                        <option value="{{ $g->id_guru }}">{{ $g->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="edit-field-label">Pembimbing Lapangan</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-user-tie input-icon"></i>
                                                <select name="id_pembimbing" id="edit_id_pembimbing" class="p-input with-icon">
                                                    <option value="">-- Pilih Pembimbing --</option>
                                                    @foreach($pembimbings as $d)
                                                        <option value="{{ $d->id_pembimbing }}">{{ $d->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card: Keamanan --}}
                                <div class="edit-section-block shadow-sm border-0">
                                    <div class="edit-section-title">
                                        <i class="fas fa-shield-halved"></i> Keamanan Akun
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="edit-field-label">Ganti Sandi (Opsional)</label>
                                            <div class="p-input-wrapper">
                                                <i class="fas fa-key input-icon"></i>
                                                <div class="input-group">
                                                    <input type="password" name="password" class="p-input with-icon" placeholder="Isi jika ingin diubah">
                                                    <button class="btn btn-outline-secondary toggle-password border-0" type="button"><i class="fas fa-eye"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-form-footer border-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-primary-custom rounded-pill px-5">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: DETAIL SISWA
============================================================ --}}
<div class="modal fade" id="modalDetailSiswa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">

            {{-- Header --}}
            <div class="modal-header-dark" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="modal-header-icon" style="background: rgba(255,255,255,0.1); color: #3b82f6;">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="modal-header-title">
                        <h5 class="fw-bold text-white mb-0">Informasi Lengkap Siswa</h5>
                        <p class="mb-0 text-white-50 small">Biodata diri dan riwayat penempatan magang aktif.</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- Body: 2-Column Sectioned Layout --}}
            <div class="modal-form-body bg-light" style="padding: 2.5rem;">
                <div class="row g-4">

                    {{-- ══ KOLOM KIRI: PERSONAL & AKADEMIK ════════════════ --}}
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-4">
                            
                            {{-- Section: Data Personal --}}
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-user-circle"></i> Data Personal & Kontak
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Nama Lengkap</label>
                                        <span class="detail-value text-primary" id="det_name">-</span>
                                    </div>
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">NISN Siswa</label>
                                        <span class="detail-value" id="det_nisn">-</span>
                                    </div>
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">Jenis Kelamin</label>
                                        <span class="detail-value" id="det_jk">-</span>
                                    </div>
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">No. WhatsApp</label>
                                        <span class="detail-value" id="det_hp">-</span>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Alamat Email Resmi</label>
                                        <span class="detail-value fw-normal" id="det_email">-</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Data Akademik --}}
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-university"></i> Identitas Pendidikan
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">Kelas & Jurusan</label>
                                        <span class="detail-value" id="det_kelas_jurusan">-</span>
                                    </div>
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">Tahun Ajaran</label>
                                        <span class="detail-value text-info" id="det_tahun_ajaran">-</span>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Lembaga Pendidikan Asal</label>
                                        <span class="detail-value" id="det_sekolah">-</span>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">NPSN Sekolah</label>
                                        <span class="detail-value" id="det_npsn">-</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ══ KOLOM KANAN: PENEMPATAN & PEMBIMBING ══════════════ --}}
                    <div class="col-md-6">
                        <div class="d-flex flex-column gap-4">

                            {{-- Section: Penempatan Magang --}}
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-building-circle-check"></i> Penempatan & Tipe
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Instansi / Lokasi Magang</label>
                                        <span class="detail-value text-success" id="det_perusahaan">-</span>
                                    </div>
                                    <div class="col-md-6 detail-grid-item">
                                        <label class="detail-label">Tipe Magang</label>
                                        <span class="detail-value badge-status hadir px-3" id="det_tipe_magang" style="display:inline-block;">-</span>
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
                                        <div id="det_surat_balasan_wrapper" class="mt-1 d-flex align-items-center gap-3">
                                            <span class="detail-value text-muted italic" id="det_surat_balasan">Belum diunggah</span>
                                            <button class="btn btn-sm btn-primary-soft rounded-pill d-none" id="btn_view_surat">
                                                <i class="fas fa-file-invoice me-1"></i> Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Tim Pembimbing --}}
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-user-shield"></i> Tim Pembimbing Resmi
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    {{-- Guru --}}
                                    <div class="p-3 rounded-4 border bg-white shadow-sm hover-elevate">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="icon-box-small bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-chalkboard-teacher"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="text-uppercase fw-bold text-primary" style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing Sekolah</div>
                                                <h6 class="mb-0 fw-bold text-dark" id="det_guru_nama">-</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <div class="small text-muted"><i class="fas fa-id-card me-1"></i> <span id="det_guru_nip">-</span></div>
                                            <a href="#" id="det_guru_wa_btn" class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
                                                <i class="fab fa-whatsapp me-1"></i> Chat Guru
                                            </a>
                                        </div>
                                    </div>
                                    {{-- PL --}}
                                    <div class="p-3 rounded-4 border bg-white shadow-sm hover-elevate">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="icon-box-small bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="text-uppercase fw-bold text-info" style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing Lapangan</div>
                                                <h6 class="mb-0 fw-bold text-dark" id="det_pl_nama">-</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <div class="small text-muted"><i class="fas fa-fingerprint me-1"></i> <span id="det_pl_nip">-</span></div>
                                            <a href="#" id="det_pl_wa_btn" class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold" style="font-size: 0.7rem;">
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

            {{-- Footer: Compact & Professional --}}
            <div class="modal-form-footer border-0 pb-4 bg-light d-flex justify-content-center">
                <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow" data-bs-dismiss="modal">
                    Tutup Informasi
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: KONFIRMASI HAPUS
============================================================ --}}
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-form-body text-center py-5">
                <div class="delete-modal-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h4 class="fw-bold mb-3">Konfirmasi Hapus</h4>
                <p class="text-muted mb-4 px-3">
                    Apakah Anda yakin ingin menghapus data siswa ini?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="formHapus" method="POST" class="d-flex gap-3 justify-content-center">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Ya, Hapus Data</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODALS: LOKASI ABSENSI
============================================================ --}}

<!-- Modal Tambah Lokasi -->
<div class="modal fade" id="modalTambahLokasi" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.storeLokasi') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-map-plus fa-lg"></i>
                        <h5 class="modal-title fw-bold">Tambah Lokasi Baru</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control rounded-pill px-3" required placeholder="Contoh: Fasilkom Kampus Palembang">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Latitude</label>
                            <input type="text" name="latitude" class="form-control rounded-pill px-3" required placeholder="-2.98472005">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Longitude</label>
                            <input type="text" name="longitude" class="form-control rounded-pill px-3" required placeholder="104.73225951">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Radius Absen (Meter)</label>
                        <input type="number" name="radius" class="form-control rounded-pill px-3" required value="500">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Lokasi -->
<div class="modal fade" id="modalEditLokasi" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formEditLokasi" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-warning text-dark py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-map-marked-alt fa-lg"></i>
                        <h5 class="modal-title fw-bold">Edit Lokasi Absensi</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" id="edit_loc_nama" class="form-control rounded-pill px-3" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Latitude</label>
                            <input type="text" name="latitude" id="edit_loc_lat" class="form-control rounded-pill px-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Longitude</label>
                            <input type="text" name="longitude" id="edit_loc_lng" class="form-control rounded-pill px-3" required>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Radius (Meter)</label>
                            <input type="number" name="radius" id="edit_loc_radius" class="form-control rounded-pill px-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Status</label>
                            <select name="is_active" id="edit_loc_active" class="form-select rounded-pill px-3">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Update Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Lokasi -->
<div class="modal fade" id="modalHapusLokasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3" style="font-size: 50px;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h5 class="fw-bold">Hapus Lokasi?</h5>
                <p class="text-muted mb-4">Aksi ini tidak dapat dibatalkan.</p>
                <form id="formHapusLokasi" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold shadow-sm">Ya, Hapus</button>
                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
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
            <div class="modal-footer bg-light border-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Tutup Daftar</button>
            </div>
        </div>
    </div>


