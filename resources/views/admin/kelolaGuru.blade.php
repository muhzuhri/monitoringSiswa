@extends('layouts.nav.admin')

@section('title', 'Manajemen Guru - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@section('body')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-guru.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
    @endpush

    <div class="management-container">
        
        <!-- Global Navigation Tabs: Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav">
                <a href="{{ route('admin.kelolaSiswa') }}" class="tab-button {{ Route::is('admin.kelolaSiswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('admin.kelolaGuru') }}" class="tab-button {{ Route::is('admin.kelolaGuru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('admin.kelolaPembimbing') }}" class="tab-button {{ Route::is('admin.kelolaPembimbing') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">
            <!-- Header -->
            <div class="management-header">
                <div class="header-title">
                    <h5>Daftar Guru Pembimbing</h5>
                    <small>Total {{ $guru->total() }} guru terdaftar.</small>
                </div>
                <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                    <i class="fas fa-plus"></i> Tambah Guru
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
                            <th class="col-w-50">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>NIP</th>
                            <th>Siswa Bimbingan</th>
                            <th class="col-w-160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru as $index => $item)
                                        <tr>
                                            <td data-label="#">{{ $guru->firstItem() + $index }}</td>
                                            <td data-label="Nama">{{ $item->nama }}</td>
                                            <td data-label="Email">{{ $item->email }}</td>
                                            <td data-label="NIP">{{ $item->id_guru }}</td>
                                            <td data-label="Siswa Bimbingan">
                                                <span
                                                    class="badge-custom {{ $item->siswas->count() > 0 ? 'badge-success-soft' : 'badge-secondary-soft' }}">
                                                    {{ $item->siswas->count() }} Siswa
                                                </span>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="action-group">
                                                    <button class="btn-icon btn-detail-soft btn-detail" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetailGuru" data-nama="{{ $item->nama }}"
                                                        data-email="{{ $item->email }}" data-id_guru="{{ $item->id_guru }}"
                                                        data-jabatan="{{ $item->jabatan }}" data-sekolah="{{ $item->sekolah }}" data-siswas="{{ json_encode($item->siswas->map(function ($s) {
                            return [
                                'nama' => $s->nama, 
                                'nisn' => $s->nisn, 
                                'id_periode' => $s->id_tahun_ajaran,
                                'periode' => $s->tahunAjaran->tahun_ajaran ?? 'N/A'
                            ]; })) }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-icon btn-edit-soft btn-edit" data-bs-toggle="modal"
                                                        data-bs-target="#modalEditGuru" data-id="{{ $item->id_guru }}"
                                                        data-nama="{{ $item->nama }}" data-email="{{ $item->email }}"
                                                        data-id_guru="{{ $item->id_guru }}" data-jabatan="{{ $item->jabatan }}"
                                                        data-sekolah="{{ $item->sekolah }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-icon btn-delete-soft btn-delete-trigger" data-bs-toggle="modal"
                                                        data-bs-target="#modalHapus"
                                                        data-url="{{ route('admin.destroyGuru', $item->id_guru) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted text-center p-12">
                                    Belum ada data guru. Klik tombol <strong>Tambah Guru</strong> untuk membuat akun baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($guru->hasPages())
                <div class="pagination-container">
                    {{ $guru->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Guru -->
    <div class="modal fade" id="modalTambahGuru" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.storeGuru') }}">
                    @csrf
                    <div class="modal-header-primary">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-header-icon on-primary">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="modal-header-title">
                                <h5>Registrasi Guru Pembimbing</h5>
                                <p>Lengkapi formulir untuk mendaftarkan guru baru.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-form-body">
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nama" class="p-input with-icon" required
                                    placeholder="Contoh: Budi Santoso, S.Pd.">
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Alamat Email Resmi</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" class="p-input with-icon" required placeholder="budi@sekolah.sch.id">
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Kata Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password" class="p-input with-icon" required
                                            placeholder="Minimal 6 karakter">
                                        <button class="btn-light-custom toggle-password" type="button" style="padding: 0 1rem; border-radius: 0 12px 12px 0;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Kata Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-shield-alt input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" class="p-input with-icon" required
                                            placeholder="Ulangi kata sandi">
                                        <button class="btn-light-custom toggle-password" type="button" style="padding: 0 1rem; border-radius: 0 12px 12px 0;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-form-group">
                            <label>Nomor Induk Pegawai (NIP)</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" name="id_guru" class="p-input with-icon" required placeholder="Masukkan NIP Resmi">
                            </div>
                        </div>

                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Jabatan / Bidang</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-briefcase input-icon"></i>
                                    <input type="text" name="jabatan" class="p-input with-icon" required
                                        placeholder="Contoh: Informatika">
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Asal Instansi Sekolah</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-school input-icon"></i>
                                    <input type="text" name="sekolah" class="p-input with-icon" required
                                        placeholder="Contoh: SMK Negeri 1 Palembang">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-form-footer">
                        <button type="button" class="btn-light-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom rounded-full px-5">Simpan Data Guru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Guru -->
    <div class="modal fade" id="modalEditGuru" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditGuru" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header-warning">
                        <div class="d-flex align-items-center gap-3">
                            <div class="modal-header-icon on-warning">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="modal-header-title">
                                <h5>Edit Profil Guru</h5>
                                <p>Perbarui informasi data guru pembimbing.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-form-body">
                        <div class="p-form-group">
                            <label>Nama Lengkap</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" name="nama" id="edit_nama" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>Email Resmi</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" name="email" id="edit_email" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Ganti Kata Sandi (Opsional)</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password" class="p-input with-icon" placeholder="Isi jika ingin diubah">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Konfirmasi Sandi</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-shield-alt input-icon"></i>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" class="p-input with-icon"
                                            placeholder="Isi jika ingin diubah">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-form-group">
                            <label>NIP</label>
                            <div class="p-input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" name="id_guru" id="edit_id_guru" class="p-input with-icon" required>
                            </div>
                        </div>
                        <div class="p-form-row">
                            <div class="p-form-group">
                                <label>Jabatan</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-briefcase input-icon"></i>
                                    <input type="text" name="jabatan" id="edit_jabatan" class="p-input with-icon" required>
                                </div>
                            </div>
                            <div class="p-form-group">
                                <label>Sekolah</label>
                                <div class="p-input-wrapper">
                                    <i class="fas fa-school input-icon"></i>
                                    <input type="text" name="sekolah" id="edit_sekolah" class="p-input with-icon" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn-light-custom" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-warning-custom">Perbarui Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Preview Detail -->
    <div class="modal fade" id="modalDetailGuru" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5><i class="fas fa-address-card"></i> Preview Profil Guru Pembimbing</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body-custom">
                    <div class="detail-grid">
                        <!-- Left Column: Personal -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-user-circle"></i> Informasi Personal</h6>
                            <div class="detail-p-item">
                                <label>Nama Lengkap</label>
                                <span id="det_nama">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Nomor Induk Pegawai (NIP)</label>
                                <span id="det_id_guru">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Alamat Email</label>
                                <span id="det_email">-</span>
                            </div>
                        </div>

                        <!-- Right Column: Academy -->
                        <div class="detail-section-card">
                            <h6 class="section-label"><i class="fas fa-school"></i> Instansi & Bidang</h6>
                            <div class="detail-p-item">
                                <label>Sekolah Asal</label>
                                <span id="det_sekolah">-</span>
                            </div>
                            <div class="detail-p-item">
                                <label>Jabatan</label>
                                <span id="det_jabatan">-</span>
                            </div>
                        </div>

                        <!-- Bottom Column: Supervised Students List -->
                        <div class="detail-section-card full-width">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="section-label mb-0"><i class="fas fa-users"></i> Daftar Siswa Bimbingan</h6>
                                <div class="filter-wrapper d-flex align-items-center gap-2">
                                    <label class="small text-muted mb-0">Filter Periode:</label>
                                    <select id="filter_periode" class="form-select form-select-sm" style="width: auto; border-radius: 8px;">
                                        <option value="all">Semua Periode</option>
                                        @foreach($periods as $p)
                                            <option value="{{ $p->id_tahun_ajaran }}">{{ $p->tahun_ajaran }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                <div class="modal-body-custom text-center pt-14">
                    <div class="delete-confirm-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h4 class="fw-800 mb-4-custom">Konfirmasi Hapus</h4>
                    <p class="text-muted mb-8-custom">Apakah Anda yakin ingin menghapus data ini?
                        Tindakan ini tidak dapat dibatalkan.</p>

                    <form id="formHapus" method="POST" class="flex-center-gap">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-custom">Ya, Hapus Data</button>
                        <button type="button" class="btn-secondary-custom" data-bs-dismiss="modal">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/admin/kelola-guru.js') }}"></script>
    @endpush
@endsection