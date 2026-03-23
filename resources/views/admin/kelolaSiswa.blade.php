@extends('layouts.nav.admin')

@section('title', 'Manajemen Siswa - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@section('body')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    @endpush

    <div class="management-container">
        <div class="admin-content-wrapper">
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Siswa Magang</h5>
                    <small>Total {{ $siswa->total() }} siswa terdaftar.</small>
                </div>
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>

            <!-- Notifications -->
            @if (session('success'))
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Data Table Area -->
            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>NISN</th>
                            <th>Guru Pembimbing</th>
                            <th>Pembimbing Lapangan</th>
                            <th style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswa as $index => $item)
                            <tr>
                                <td data-label="#">{{ $siswa->firstItem() + $index }}</td>
                                <td data-label="Nama">{{ $item->nama }}</td>
                                <td data-label="Email">{{ $item->email }}</td>
                                <td data-label="NISN">{{ $item->nisn }}</td>
                                <td data-label="Guru Pembimbing">{{ $item->guru->nama ?? '-' }}</td>
                                <td data-label="Pembimbing Lapangan">{{ $item->pembimbing->nama ?? '-' }}</td>
                                <td data-label="Aksi">
                                    <div class="action-group">
                                        <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailSiswa" data-nisn="{{ $item->nisn }}"
                                            data-nama="{{ $item->nama }}" data-email="{{ $item->email }}"
                                            data-no_hp="{{ $item->no_hp }}"
                                            data-gender="{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                            data-kelas="{{ $item->kelas }}" data-jurusan="{{ $item->jurusan }}"
                                            data-sekolah="{{ $item->sekolah }}" data-perusahaan="{{ $item->perusahaan ?? '-' }}"
                                            data-mulai="{{ $item->tgl_mulai_magang ? \Carbon\Carbon::parse($item->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                            data-selesai="{{ $item->tgl_selesai_magang ? \Carbon\Carbon::parse($item->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                            data-guru-nama="{{ $item->guru->nama ?? '-' }}"
                                            data-guru-nip="{{ $item->guru->id_guru ?? '-' }}"
                                            data-pl-nama="{{ $item->pembimbing->nama ?? '-' }}"
                                            data-pl-nip="{{ $item->pembimbing->id_pembimbing ?? '-' }}"
                                            data-pl-hp="{{ $item->pembimbing->no_telp ?? '-' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-edit-soft btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#modalEditSiswa" data-id="{{ $item->nisn }}"
                                            data-nama="{{ $item->nama }}" data-email="{{ $item->email }}"
                                            data-kelas="{{ $item->kelas }}"
                                            data-jurusan="{{ $item->jurusan }}" data-sekolah="{{ $item->sekolah }}"
                                            data-perusahaan="{{ $item->perusahaan }}" data-guru-nip="{{ $item->id_guru }}"
                                            data-pl-nama="{{ $item->pembimbing->nama ?? '' }}"
                                            data-pl-nip="{{ $item->id_pembimbing ?? '' }}"
                                            data-pl-hp="{{ $item->pembimbing->no_telp ?? '' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete-soft btn-delete-trigger" data-bs-toggle="modal"
                                            data-bs-target="#modalHapus"
                                            data-url="{{ route('admin.destroySiswa', $item->nisn) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center-muted"
                                    style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    Belum ada data siswa. Klik tombol <strong>Tambah Siswa</strong> untuk membuat akun baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($siswa->hasPages())
                <div class="pagination-container">
                    {{ $siswa->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Siswa -->
    <div class="modal fade" id="modalTambahSiswa" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.storeSiswa') }}">
                    @csrf
                    <div class="modal-header-custom">
                        <h5><i class="fas fa-user-plus"></i> Registrasi Siswa Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body-custom">
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nama" class="p-input with-icon" required
                                    placeholder="Contoh: Ahmad Subardjo">
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Alamat Email Resmi</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="p-input with-icon" required
                                    placeholder="ahmad@email.com">
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Kata Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" class="p-input with-icon" required
                                        placeholder="Minimal 8 karakter">
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Kata Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password_confirmation" class="p-input with-icon" required
                                        placeholder="Ulangi kata sandi">
                                </div>
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>NISN (Nomor Induk)</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="nisn" class="p-input with-icon" required
                                        placeholder="10 Digit NISN">
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Kelas</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-chalkboard input-icon"></i>
                                    <input type="text" name="kelas" class="p-input with-icon" required
                                        placeholder="Contoh: XII-RPL-1">
                                </div>
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Program Keahlian / Jurusan</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-book-reader input-icon"></i>
                                <input type="text" name="jurusan" class="p-input with-icon" required
                                    placeholder="Contoh: Rekayasa Perangkat Lunak">
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Asal Instansi Pendidikan (Sekolah)</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-school input-icon"></i>
                                <input type="text" name="sekolah" class="p-input with-icon" required
                                    placeholder="SMK Negeri ...">
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Penempatan Magang (Nama Perusahaan)</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-building input-icon"></i>
                                <input type="text" name="perusahaan" class="p-input with-icon"
                                    placeholder="Kosongkan jika belum ada penempatan">
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Guru Pembimbing Internal</label>
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
                            <div class="p-form-group">
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

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-p-main">Daftarkan Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Siswa -->
    <div class="modal fade" id="modalEditSiswa" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditSiswa" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header-custom">
                        <h5><i class="fas fa-user-edit"></i> Edit Profil Siswa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nama" id="edit_nama" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Alamat Email</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" id="edit_email" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Ubah Kata Sandi (Opsional)</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" class="p-input with-icon"
                                        placeholder="Isi jika ingin mengubah">
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Sandi Baru</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password_confirmation" class="p-input with-icon"
                                        placeholder="Ulangi jika mengubah">
                                </div>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>NISN</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" name="nisn" id="edit_nisn" class="p-input with-icon" required>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Kelas</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-chalkboard input-icon"></i>
                                    <input type="text" name="kelas" id="edit_kelas" class="p-input with-icon" required>
                                </div>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Jurusan / Program Studi</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-book-reader input-icon"></i>
                                <input type="text" name="jurusan" id="edit_jurusan" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Asal Sekolah / Universitas</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-school input-icon"></i>
                                <input type="text" name="sekolah" id="edit_sekolah" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Tempat Magang Terkini</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-building input-icon"></i>
                                <input type="text" name="perusahaan" id="edit_perusahaan" class="p-input with-icon">
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Guru Pembimbing</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-chalkboard-teacher input-icon"></i>
                                    <select name="id_guru" id="edit_id_guru" class="p-input with-icon">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $g)
                                            <option value="{{ $g->id_guru }}">{{ $g->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="p-form-group">
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
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Kembali</button>
                        <button type="submit" class="btn-p-main">Perbarui Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Detail -->
    <div class="modal fade" id="modalDetailSiswa" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-address-card"></i> Preview Profil Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <div class="detail-grid">
                        <!-- Left Column: Personal -->
                        <div class="detail-section-card">
                            <p class="section-label"><i class="fas fa-user-circle"></i> Informasi Personal</p>
                            <div class="detail-p-item">
                                <label>Nama Lengkap</label>
                                <span id="det_name">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Nomor Induk (NISN)</label>
                                <span id="det_nisn">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Alamat Email</label>
                                <span id="det_email">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>No. Kontak</label>
                                <span id="det_hp">-</span>
                            </div>
                        </div>

                        <!-- Right Column: Academy -->
                        <div class="detail-section-card">
                            <p class="section-label"><i class="fas fa-university"></i> Akademik & Magang</p>
                            <div class="detail-p-item">
                                <label>Kelas & Jurusan</label>
                                <span id="det_kelas_jurusan">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Asal Instansi</label>
                                <span id="det_sekolah">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Tempat Magang</label>
                                <span id="det_perusahaan">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Durasi Magang</label>
                                <span id="det_periode">-</span>
                            </div>
                        </div>

                        <!-- Bottom Column: Supervisors -->
                        <div class="detail-section-card full-width">
                            <p class="section-label"><i class="fas fa-users-cog"></i> Tim Pembimbing Resmi</p>
                            <div class="supervisor-cards">
                                <div class="supervisor-mini-card">
                                    <div class="role-badge guru">INTERNAL (GURU)</div>
                                    <div class="sup-name" id="det_guru_nama">-</div>
                                    <div class="sup-meta">
                                        <div><i class="fas fa-id-badge me-2"></i>NIP: <span id="det_guru_nip">-</span></div>
                                    </div>
                                </div>
                                <div class="supervisor-mini-card">
                                    <div class="role-badge pl">EKSTERNAL (DPL)</div>
                                    <div class="sup-name" id="det_pl_nama">-</div>
                                    <div class="sup-meta">
                                        <div><i class="fas fa-id-card-alt me-2"></i>ID: <span id="det_pl_nip">-</span></div>
                                        <div><i class="fas fa-mobile-alt me-2"></i>Hubungi: <span id="det_pl_hp">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-custom">
                    <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-body-custom text-center" style="padding-top: 3.5rem;">
                    <div class="delete-confirm-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h4 style="font-weight: 800; margin-bottom: 1rem;">Konfirmasi Hapus</h4>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;">Apakah Anda yakin ingin menghapus data ini?
                        Tindakan ini tidak dapat dibatalkan.</p>

                    <form id="formHapus" method="POST" style="display: flex; justify-content: center; gap: 1rem;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom">Ya, Hapus Data</button>
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit Logic
            const editButtons = document.querySelectorAll('.btn-edit');
            const editForm = document.getElementById('formEditSiswa');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const email = this.getAttribute('data-email');
                    const kelas = this.getAttribute('data-kelas');
                    const jurusan = this.getAttribute('data-jurusan');
                    const sekolah = this.getAttribute('data-sekolah');
                    const perusahaan = this.getAttribute('data-perusahaan');
                    const guru_nip = this.getAttribute('data-guru-nip');
                    const pl_nip = this.getAttribute('data-pl-nip');

                    editForm.action = `/admin/siswa/${id}`;
                    document.getElementById('edit_nama').value = nama;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_nisn').value = id; // NISN
                    document.getElementById('edit_kelas').value = kelas;
                    document.getElementById('edit_jurusan').value = jurusan;
                    document.getElementById('edit_sekolah').value = sekolah;
                    document.getElementById('edit_perusahaan').value = perusahaan || '';
                    document.getElementById('edit_id_guru').value = guru_nip || '';
                    document.getElementById('edit_id_pembimbing').value = pl_nip || '';
                });
            });

            // Detail Logic
            const detailButtons = document.querySelectorAll('.btn-detail');
            detailButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const nama = this.getAttribute('data-nama');
                    const nisn = this.getAttribute('data-nisn');
                    const email = this.getAttribute('data-email');
                    const hp = this.getAttribute('data-no_hp');
                    const kelas = this.getAttribute('data-kelas');
                    const jurusan = this.getAttribute('data-jurusan');
                    const sekolah = this.getAttribute('data-sekolah');
                    const perusahaan = this.getAttribute('data-perusahaan');
                    const mulai = this.getAttribute('data-mulai');
                    const selesai = this.getAttribute('data-selesai');

                    document.getElementById('det_nama').textContent = nama;
                    document.getElementById('det_nisn').textContent = nisn;
                    document.getElementById('det_email').textContent = email;
                    document.getElementById('det_hp').textContent = hp || '-';
                    document.getElementById('det_kelas_jurusan').textContent = `${kelas} - ${jurusan}`;
                    document.getElementById('det_sekolah').textContent = sekolah;
                    document.getElementById('det_perusahaan').textContent = perusahaan || 'Belum ditugaskan';
                    document.getElementById('det_periode').textContent = (mulai && selesai) ? `${mulai} s/d ${selesai}` : 'Belum ditentukan';

                    document.getElementById('det_guru_nama').textContent = this.getAttribute('data-guru-nama');
                    document.getElementById('det_guru_nip').textContent = this.getAttribute('data-guru-nip');
                    document.getElementById('det_pl_nama').textContent = this.getAttribute('data-pl-nama');
                    document.getElementById('det_pl_nip').textContent = this.getAttribute('data-pl-nip');
                    document.getElementById('det_pl_hp').textContent = this.getAttribute('data-pl-hp');
                });
            });

            // Delete Logic
            const deleteButtons = document.querySelectorAll('.btn-delete-trigger');
            const deleteForm = document.getElementById('formHapus');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const url = this.getAttribute('data-url');
                    deleteForm.action = url;
                });
            });
        });
    </script>
@endsection