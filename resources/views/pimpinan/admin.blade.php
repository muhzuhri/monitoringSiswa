@extends('layouts.nav.pimpinan')

@section('title', 'Manajemen Admin - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/modals.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/pimpinan/admin.js') }}"></script>
@endpush

@section('body')
    <div class="management-container">
        
        <!-- Global Navigation Tabs: Admin, Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper">
            <div class="tabs-nav">
                <a href="{{ route('pimpinan.admin') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.admin') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
                <a href="{{ route('pimpinan.siswa') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.siswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('pimpinan.guru') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.guru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('pimpinan.pembimbing') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.pembimbing') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">

            {{-- HEADER --}}
            <div class="management-header">
                <div class="header-title">
                    <h5>Manajemen Admin</h5>
                    <p>Kelola data akun admin sistem monitoring.</p>
                </div>
                <div class="header-actions d-flex gap-3 align-items-center">
                    <form action="{{ route('pimpinan.admin') }}" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                class="p-input with-icon"
                                placeholder="Cari Nama / Email..."
                                onchange="this.form.submit()"
                            >
                        </div>
                    </form>
                    <button class="btn-primary-custom rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
                        <i class="fas fa-plus"></i> Tambah Admin
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 12px; border: none; background: #ecfdf5; color: #065f46;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="data-table-wrapper">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Identitas Admin</th>
                            <th>Role</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $adm)
                            <tr>
                                <td class="ps-4">
                                    <div class="cell-name">{{ $adm->nama }}</div>
                                    <div class="cell-sub"><i class="fas fa-envelope text-primary me-1 opacity-50"></i> {{ $adm->email }}</div>
                                </td>
                                <td>
                                    <span class="status-badge" style="background:#e0e7ff; color:#4338ca; padding: 5px 12px; border-radius:50px; font-weight:700; font-size:0.75rem;">
                                        <i class="fas fa-shield-alt me-1"></i> Admin
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="action-group justify-content-end">
                                        <button class="btn-icon btn-edit-soft btn-edit"
                                            data-bs-toggle="modal" data-bs-target="#modalEditAdmin"
                                            data-id="{{ $adm->id_admin }}"
                                            data-nama="{{ $adm->nama }}"
                                            data-email="{{ $adm->email }}"
                                            title="Edit Admin">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('pimpinan.destroyAdmin', $adm->id_admin) }}" method="POST" class="d-inline form-delete">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn-icon btn-delete-soft btn-delete-trigger" title="Hapus Admin">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    Tidak ada data admin ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $admins->links() }}
            </div>

        </div>{{-- /admin-content-wrapper --}}
    </div>{{-- /management-container --}}

    {{-- MODAL TAMBAH ADMIN --}}
    <div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-primary">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5 class="mb-0">Tambah Akun Admin</h5>
                            <p class="mb-0">Daftarkan akses admin sistem baru.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pimpinan.storeAdmin') }}" method="POST">
                    @csrf
                    <div class="modal-form-body">
                        <div class="detail-card p-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Nama Lengkap</label>
                                <input type="text" name="nama" class="p-input w-100" required placeholder="Masukkan nama admin">
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Email</label>
                                <input type="email" name="email" class="p-input w-100" required placeholder="Masukkan email aktif">
                            </div>
                            <div class="form-group mb-0">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Password</label>
                                <input type="password" name="password" class="p-input w-100" required minlength="6" placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-save me-2"></i>Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT ADMIN --}}
    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-dark">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon on-dark">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="modal-header-title">
                            <h5 class="mb-0">Edit Akun Admin</h5>
                            <p class="mb-0">Perbarui informasi kredensial admin.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditAdmin" method="POST" data-update-url="{{ route('pimpinan.updateAdmin', ['id' => ':id']) }}">
                    @csrf @method('PUT')
                    <div class="modal-form-body">
                        <div class="detail-card p-4">
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Nama Lengkap</label>
                                <input type="text" name="nama" id="edit_nama" class="p-input w-100" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Email</label>
                                <input type="email" name="email" id="edit_email" class="p-input w-100" required>
                            </div>
                            <div class="form-group mb-0">
                                <label class="fw-bold small text-muted text-uppercase mb-2 d-block">Password <small class="text-muted fw-normal">(Opsional)</small></label>
                                <input type="password" name="password" class="p-input w-100" minlength="6" placeholder="Kosongkan jika tidak diubah">
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-primary-custom">
                            <i class="fas fa-sync-alt me-2"></i>Update Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
@endsection
