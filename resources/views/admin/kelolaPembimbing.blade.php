@extends('layouts.nav.admin')

@section('title', 'Manajemen Pembimbing Lapangan - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@section('body')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-dosen.css') }}">
    @endpush

    <div class="management-container">
        <div class="admin-content-wrapper">
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Pembimbing Lapangan</h5>
                    <small>Total {{ $pembimbing->total() }} pembimbing lapangan terdaftar.</small>
                </div>
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahDosen">
                    <i class="fas fa-plus"></i> Tambah Pembimbing Lapangan
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
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>Jabatan</th>
                            <th>Siswa Bimbingan</th>
                            <th style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembimbing as $index => $item)
                                            <tr>
                                                <td data-label="#">{{ $pembimbing->firstItem() + $index }}</td>

                                                <td data-label="Nama">{{ $item->nama }}</td>
                                                <td data-label="Email">{{ $item->email }}</td>
                                                <td data-label="Jabatan">{{ $item->jabatan }}</td>
                                                <td data-label="Siswa Bimbingan">
                                                    <span
                                                        class="badge {{ $item->siswas->count() > 0 ? 'bg-success-soft text-success' : 'bg-secondary-soft text-muted' }}"
                                                        style="border-radius: 8px; padding: 0.5rem 0.75rem; font-weight: 700;">
                                                        {{ $item->siswas->count() }} Siswa
                                                    </span>
                                                </td>
                                                <td data-label="Aksi">
                                                    <div class="action-group">
                                                        <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                                            data-bs-target="#modalDetailDosen" data-nama="{{ $item->nama }}"
                                                            data-email="{{ $item->email }}" data-jabatan="{{ $item->jabatan }}"
                                                            data-instansi="{{ $item->instansi }}" data-telp="{{ $item->no_telp }}" data-siswas="{{ json_encode($item->siswas->map(function ($s) {
                                return ['nama' => $s->nama, 'nisn' => $s->nisn];
                            })) }}">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn-icon btn-edit-soft btn-edit" data-bs-toggle="modal"
                                                            data-bs-target="#modalEditDosen" data-id="{{ $item->id_pembimbing }}"
                                                            data-nama="{{ $item->nama }}" data-email="{{ $item->email }}"
                                                            data-jabatan="{{ $item->jabatan }}" data-instansi="{{ $item->instansi }}"
                                                            data-telp="{{ $item->no_telp }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn-icon btn-delete-soft btn-delete-trigger" data-bs-toggle="modal"
                                                            data-bs-target="#modalHapus"
                                                            data-url="{{ route('admin.destroyPembimbing', $item->id_pembimbing) }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center-muted"
                                    style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                    Belum ada data pembimbing lapangan. Klik tombol <strong>Tambah Pembimbing Lapangan</strong>
                                    untuk membuat akun baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($pembimbing->hasPages())
                <div class="pagination-container">
                    {{ $pembimbing->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Pembimbing Lapangan -->
    <div class="modal fade" id="modalTambahDosen" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.storePembimbing') }}">
                    @csrf
                    <div class="modal-header-custom">
                        <h5><i class="fas fa-user-plus"></i> Registrasi Pembimbing Lapangan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body-custom">
                        <div class="p-form-group">
                            <label>NIP / ID Pembimbing</label>
                            <input type="text" name="id_pembimbing" class="p-input" required placeholder="Masukkan NIP/ID unik">
                        </div>

                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="p-input" required placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="p-form-group">
                            <label>Alamat Email Resmi</label>
                            <input type="email" name="email" class="p-input" required placeholder="nama@email.com">
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Kata Sandi</label>
                                <input type="password" name="password" class="p-input" required
                                    placeholder="Minimal 6 karakter">
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" class="p-input" required
                                    placeholder="Ulangi kata sandi">
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" class="p-input" required
                                placeholder="Contoh: Pembimbing Lapangan">
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Instansi</label>
                                <select name="instansi" class="p-input" required style="appearance: auto;">
                                    <option value="">-- Pilih Instansi --</option>
                                    <option value="Fasilkom Unsri Indralaya">Fasilkom Unsri Indralaya</option>
                                    <option value="Fasilkom Unsri Bukit">Fasilkom Unsri Bukit</option>
                                </select>
                            </div>
                            <div class="p-form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="no_telp" class="p-input" required placeholder="08XXXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-p-main">Simpan Data Pembimbing Lapangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pembimbing Lapangan -->
    <div class="modal fade" id="modalEditDosen" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditDosen" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header-custom">
                        <h5><i class="fas fa-user-edit"></i> Edit Profil Pembimbing Lapangan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body-custom">
                        <div class="p-form-group">
                            <label>NIP / ID Pembimbing</label>
                            <input type="text" id="edit_id_display" class="p-input" disabled
                                style="background: #f1f5f9; cursor: not-allowed;" title="NIP/ID tidak dapat diubah">
                        </div>
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" id="edit_nama" class="p-input" required>
                        </div>
                        <div class="p-form-group">
                            <label>Email Resmi</label>
                            <input type="email" name="email" id="edit_email" class="p-input" required>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Ganti Kata Sandi (Opsional)</label>
                                <input type="password" name="password" class="p-input" placeholder="Isi jika ingin diubah">
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Sandi</label>
                                <input type="password" name="password_confirmation" class="p-input"
                                    placeholder="Isi jika ingin diubah">
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Jabatan</label>
                            <input type="text" name="jabatan" id="edit_jabatan" class="p-input" required>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Instansi</label>
                                <select name="instansi" id="edit_instansi" class="p-input" required
                                    style="appearance: auto;">
                                    <option value="">-- Pilih Instansi --</option>
                                    <option value="Fasilkom Unsri Indralaya">Fasilkom Unsri Indralaya</option>
                                    <option value="Fasilkom Unsri Bukit">Fasilkom Unsri Bukit</option>
                                </select>
                            </div>
                            <div class="p-form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="no_telp" id="edit_telp" class="p-input" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer-custom">
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-p-main">Perbarui Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Detail -->
    <div class="modal fade" id="modalDetailDosen" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-address-card"></i> Preview Profil Pembimbing Lapangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <div class="detail-grid">
                        <!-- Left Column: Personal -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-user-circle"></i> Informasi Personal</h6>
                            <div class="detail-p-item">
                                <label>NIP / ID</label>
                                <span id="det_id">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Nama Lengkap</label>
                                <span id="det_name">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Jabatan / NIDN</label>
                                <span id="det_jabatan">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Alamat Email</label>
                                <span id="det_email">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Nomor Telepon</label>
                                <span id="det_telp">-</span>
                            </div>
                        </div>

                        <!-- Right Column: Academy -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-university"></i> Instansi</h6>
                            <div class="detail-p-item">
                                <label>Asal Instansi</label>
                                <span id="det_instansi">-</span>
                            </div>
                        </div>

                        <!-- Bottom Column: Supervised Students List -->
                        <div class="detail-section-card full-width">
                            <h6 class="section-label"><i class="fas fa-users"></i> Daftar Siswa Bimbingan</h6>
                            <div id="supervised_students_list" class="supervised-list">
                                <!-- Will be populated by JS -->
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
            const editForm = document.getElementById('formEditDosen');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const email = this.getAttribute('data-email');
                    const jabatan = this.getAttribute('data-jabatan');
                    const instansi = this.getAttribute('data-instansi');
                    const telp = this.getAttribute('data-telp');

                    editForm.action = `/admin/dosen/${id}`;
                    document.getElementById('edit_id_display').value = id;
                    document.getElementById('edit_nama').value = nama;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_jabatan').value = jabatan;
                    document.getElementById('edit_instansi').value = instansi;
                    document.getElementById('edit_telp').value = telp;
                });
            });

            // Preview Detail Logic
            const detailButtons = document.querySelectorAll('.btn-detail');
            detailButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('det_id').textContent = this.closest('tr').querySelector('.btn-edit').getAttribute('data-id');
                    document.getElementById('det_nama').textContent = this.getAttribute('data-nama');
                    document.getElementById('det_jabatan').textContent = this.getAttribute('data-jabatan');
                    document.getElementById('det_email').textContent = this.getAttribute('data-email');
                    document.getElementById('det_telp').textContent = this.getAttribute('data-telp');
                    document.getElementById('det_instansi').textContent = this.getAttribute('data-instansi');

                    // Populate supervised students list
                    const siswas = JSON.parse(this.getAttribute('data-siswas'));
                    const listContainer = document.getElementById('supervised_students_list');
                    listContainer.innerHTML = '';

                    if (siswas.length > 0) {
                        siswas.forEach(s => {
                            const studentDiv = document.createElement('div');
                            studentDiv.className = 'student-card-mini';
                            studentDiv.innerHTML = `
                                                    <div class="student-info">
                                                        <div class="nama">${s.nama}</div>
                                                        <div class="meta">NISN: ${s.nisn}</div>
                                                    </div>
                                                    <i class="fas fa-user-graduate"></i>
                                                `;
                            listContainer.appendChild(studentDiv);
                        });
                    } else {
                        listContainer.innerHTML = '<div class="text-muted" style="padding: 1rem;">Belum ada siswa bimbingan.</div>';
                    }
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