@extends('layouts.nav.admin')

@section('title', 'Dashboard Admin - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/management.css') }}">
@endpush

@section('body')
    <div class="dashboard-container">
        <div class="admin-content-wrapper">
            <!-- Welcome Section -->
            <div class="welcome-hero">
                <div class="welcome-text">
                    <h5>Selamat datang, {{ $user->name }}</h5>
                    <p>Anda login sebagai <strong>Admin Sistem</strong>.</p>
                </div>
                <div class="admin-badge">
                    <i class="fas fa-cog"></i>
                    <span>Mode Administrasi</span>
                </div>
            </div>

            <!-- Management Grid -->
            <div class="dashboard-grid">
                <!-- Management Dosen -->
                <div class="action-card">
                    <div class="card-icon icon-blue">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="card-category">Manajemen Pengguna</div>
                    <div class="card-info">
                        <h5>Manajemen Dosen</h5>
                        <p>Kelola akun dosen pembimbing yang akan memonitor aktivitas siswa magang.</p>
                        <a href="{{ route('admin.kelolaPembimbing') }}" class="btn-action btn-primary-custom">
                            <i class="fas fa-arrow-right"></i>
                            Kelola Dosen
                        </a>
                    </div>
                </div>

                <!-- Mitra Magang -->
                <div class="action-card">
                    <div class="card-icon icon-green">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="card-category">Perusahaan Mitra</div>
                    <div class="card-info">
                        <h5>Mitra Magang</h5>
                        <p>Atur dan pantau daftar perusahaan mitra yang bekerja sama dalam program magang.</p>
                        <button type="button" class="btn-action btn-disabled" disabled>
                            Segera Hadir
                        </button>
                    </div>
                </div>

                <!-- Konfigurasi Sistem -->
                <div class="action-card">
                    <div class="card-icon icon-yellow">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="card-category">Konfigurasi Lokasi</div>
                    <div class="card-info">
                        <h5>Lokasi Absensi</h5>
                        <p>Atur titik koordinat kampus/lokasi absensi (Latitude & Longitude) dan radius absen.</p>
                        <a href="{{ route('admin.kelolaLokasi') }}" class="btn-action btn-primary-custom" style="background: var(--warning-color); border:none;">
                            <i class="fas fa-arrow-right"></i>
                            Kelola Lokasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection