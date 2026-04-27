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
                                    <select name="perusahaan" class="p-input with-icon">
                                        <option value="">-- Lokasi Penempatan --</option>
                                        @foreach($lokasis as $lok)
                                            <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                        @endforeach
                                    </select>
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
                                    <select name="perusahaan" id="edit_perusahaan" class="p-input with-icon">
                                        <option value="">-- Lokasi Penempatan --</option>
                                        @foreach($lokasis as $lok)
                                            <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }}</option>
                                        @endforeach
                                    </select>
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
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>


