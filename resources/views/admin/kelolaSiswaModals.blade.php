{{-- ============================================================
     MODAL: TAMBAH SISWA
============================================================ --}}
<div class="modal fade" id="modalTambahSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.storeSiswa') }}">
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

                        {{-- Kolom Kiri: Akun --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Informasi Akun</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="nama" class="p-input with-icon" required placeholder="Nama Lengkap">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="p-input with-icon" required placeholder="Email Resmi">
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

                        {{-- Kolom Kanan: Akademik --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Detail Akademik</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="nisn" class="p-input with-icon" required placeholder="NISN (10 Digit)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-chalkboard input-icon"></i>
                                    <input type="text" name="kelas" class="p-input with-icon" required placeholder="Kelas (Contoh: XII-RPL)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-graduation-cap input-icon"></i>
                                    <input type="text" name="jurusan" class="p-input with-icon" required placeholder="Program Keahlian">
                                </div>
                            </div>
                        </div>

                        {{-- Instansi & Penempatan --}}
                        <div class="col-12">
                            <span class="form-section-label">Instansi & Penempatan</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-school input-icon"></i>
                                    <input type="text" name="sekolah" class="p-input with-icon" required placeholder="Asal Sekolah">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-building input-icon"></i>
                                    <input type="text" name="perusahaan" class="p-input with-icon" placeholder="Tempat Penempatan (Kosongkan jika belum)">
                                </div>
                            </div>
                        </div>

                        {{-- Pembimbing --}}
                        <div class="col-md-6">
                            <div class="p-input-wrapper">
                                <i class="fas fa-chalkboard-teacher input-icon"></i>
                                <select name="id_guru" class="p-input with-icon">
                                    <option value="">-- Pilih Guru Pembimbing --</option>
                                    @foreach($gurus as $g)
                                        <option value="{{ $g->id_guru }}">{{ $g->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-input-wrapper">
                                <i class="fas fa-user-tie input-icon"></i>
                                <select name="id_pembimbing" class="p-input with-icon">
                                    <option value="">-- Pilih Pembimbing Lapangan --</option>
                                    @foreach($pembimbings as $d)
                                        <option value="{{ $d->id_pembimbing }}">{{ $d->nama }}</option>
                                    @endforeach
                                </select>
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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditSiswa" method="POST">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="modal-header-warning">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-warning">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5>Ubah Profil Siswa</h5>
                            <p>Perbarui informasi data siswa magang.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body --}}
                <div class="modal-form-body">
                    <div class="row g-4">

                        {{-- Kolom Kiri --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Informasi Utama</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" name="nama" id="edit_nama" class="p-input with-icon" required placeholder="Nama Lengkap">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" id="edit_email" class="p-input with-icon" required placeholder="Email">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password" class="p-input with-icon" placeholder="Kata Sandi Baru (kosongkan jika tidak diubah)">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="col-md-6">
                            <span class="form-section-label">Identitas & Akademik</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="nisn" id="edit_nisn" class="p-input with-icon" required placeholder="NISN">
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-chalkboard input-icon"></i>
                                        <input type="text" name="kelas" id="edit_kelas" class="p-input with-icon" required placeholder="Kelas">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-graduation-cap input-icon"></i>
                                        <input type="text" name="jurusan" id="edit_jurusan" class="p-input with-icon" required placeholder="Jurusan">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-school input-icon"></i>
                                    <input type="text" name="sekolah" id="edit_sekolah" class="p-input with-icon" required placeholder="Sekolah">
                                </div>
                            </div>
                        </div>

                        {{-- Penempatan & Pembimbing --}}
                        <div class="col-12">
                            <span class="form-section-label">Penempatan & Pembimbing</span>
                            <div class="mb-3">
                                <div class="p-input-wrapper">
                                    <i class="fas fa-building input-icon"></i>
                                    <input type="text" name="perusahaan" id="edit_perusahaan" class="p-input with-icon" placeholder="Nama Perusahaan">
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-chalkboard-teacher input-icon"></i>
                                        <select name="id_guru" id="edit_id_guru" class="p-input with-icon">
                                            <option value="">-- Guru Pembimbing --</option>
                                            @foreach($gurus as $g)
                                                <option value="{{ $g->id_guru }}">{{ $g->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-input-wrapper">
                                        <i class="fas fa-user-tie input-icon"></i>
                                        <select name="id_pembimbing" id="edit_id_pembimbing" class="p-input with-icon">
                                            <option value="">-- Pembimbing Lapangan --</option>
                                            @foreach($pembimbings as $d)
                                                <option value="{{ $d->id_pembimbing }}">{{ $d->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-form-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: DETAIL SISWA
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
