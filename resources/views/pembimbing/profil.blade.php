@extends('layouts.nav.pembimbing')

@section('title', 'Profil Saya - Monitoring Siswa')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/profil.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4">
        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title"><i class="fas fa-user-circle text-primary me-2"></i>Pengaturan Profil</h2>
                <p class="page-subtitle">Kelola informasi pribadi, data profesional, dan keamanan akun Anda.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="custom-alert alert-success-soft mb-4">
                <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="custom-alert alert-danger-soft mb-4">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="profile-grid mt-4">
            <!-- Sidebar: Avatar & Summary -->
            <div class="profile-sidebar">
                <div class="content-card text-center p-4">
                    <div class="profile-avatar-container mx-auto mb-3">
                        <div class="avatar-xl bg-primary-light text-primary">
                            <span>{{ substr($pembimbing->name, 0, 1) }}</span>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $pembimbing->name }}</h4>
                    <p class="text-muted small mb-3">{{ $pembimbing->email }}</p>
                    <div class="badge bg-purple-light text-purple mb-3">Pembimbing</div>
                    <div class="sidebar-stats mt-3 pt-3 border-top">
                        <div class="col-12">
                            <span class="d-block fw-bold text-dark">{{ $pembimbing->instansi ?? '-' }}</span>
                            <span class="text-muted small">Instansi</span>
                        </div>
                    </div>
                </div>

                <div class="content-card mt-4 p-3 info-card">
                    <div class="d-flex align-items-center gap-2 text-warning mb-2">
                        <i class="fas fa-shield-alt"></i>
                        <span class="fw-bold small">Keamanan Akun</span>
                    </div>
                    <p class="text-muted x-small mb-0">Pastikan untuk menggunakan password yang kuat dan unik untuk
                        melindungi akun Anda.</p>
                </div>
            </div>

            <!-- Main: Forms -->
            <div class="profile-main">
                <form action="{{ route('pembimbing.profil.update') }}" method="POST">
                    @csrf

                    <!-- Box 1: Informasi Pribadi -->
                    <div class="content-card mb-4">
                        <div class="card-header">
                            <h4 class="card-title text-primary"><i class="fas fa-id-card me-2"></i>Informasi Pribadi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="name" class="custom-input"
                                            value="{{ old('name', $pembimbing->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Utama</label>
                                        <input type="email" name="email" class="custom-input"
                                            value="{{ old('email', $pembimbing->email) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" name="no_telp" class="custom-input"
                                            value="{{ old('no_telp', $pembimbing->no_telp) }}" placeholder="e.g 08123456789">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Data Profesional -->
                    <div class="content-card mb-4">
                        <div class="card-header">
                            <h4 class="card-title text-purple"><i class="fas fa-briefcase me-2"></i>Data Profesional</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Jabatan / Pangkat</label>
                                        <input type="text" name="jabatan" class="custom-input"
                                            value="{{ old('jabatan', $pembimbing->jabatan) }}" placeholder="e.g Lektor Kepala">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Instansi / Unit Kerja</label>
                                        <input type="text" name="instansi" class="custom-input"
                                            value="{{ old('instansi', $pembimbing->instansi) }}"
                                            placeholder="e.g Politeknik Negeri Sriwijaya">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Box 3: Ganti Password -->
                    <div class="content-card mb-4">
                        <div class="card-header">
                            <h4 class="card-title text-warning"><i class="fas fa-lock me-2"></i>Keamanan & Password</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Password Baru <span
                                                class="text-muted small fw-normal">(Opsional)</span></label>
                                        <input type="password" name="password" class="custom-input"
                                            placeholder="Minimal 8 karakter">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation"
                                            class="custom-input" placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-bottom text-end mt-4">
                        <button type="submit" class="btn-save-profile">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection