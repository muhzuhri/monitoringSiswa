@extends('layouts.nav.pembimbing')

@section('title', 'Dashboard Dosen - Monitoring Siswa')
@section('body-class', 'dosen-dashboard-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/style-dosen.css') }}">
@endpush

@section('body')
    <div class="dashboard-container">

        <!-- Welcome & Alerts -->
        <div class="welcome-box">
            <div class="welcome-text">
                <h2 class="greeting">Selamat datang, {{ $user->name ?? 'Dosen' }}</h2>
                <p class="role-desc">Anda login sebagai <strong>Dosen Pembimbing Lapangan</strong>.</p>
            </div>
            <div class="status-badge">
                <i class="fas fa-check-circle"></i> Sistem Aktif
            </div>
        </div>

        <!-- Alerts Section -->
        <!-- <div class="alert-section">
            <div class="custom-alert alert-danger">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">
                    <strong>Peringatan!</strong> Siswa <span>Budi Santoso</span> (NIM: 0902128) tercatat Alpha 3 hari
                    berturut-turut.
                </div>
                <button class="alert-action">Lihat Detail</button>
            </div>
        </div> -->

        <!-- Overview Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">12</h3>
                    <p class="stat-label">Siswa Binaan</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-green">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">94%</h3>
                    <p class="stat-label">Kehadiran Keseluruhan</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-orange">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">8</h3>
                    <p class="stat-label">Pending Validasi</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">85/100</h3>
                    <p class="stat-label">Evaluasi Sementara</p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="dashboard-main-grid">

            <!-- Left Column: Chart & Notifications -->
            <div class="main-left">
                <!-- Grafik Progres -->
                <div class="content-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-chart-bar text-blue"></i> Grafik Progres Mingguan</h4>
                        <button class="btn-filter">Minggu Ini <i class="fas fa-chevron-down"></i></button>
                    </div>
                    <div class="card-body">
                        <div class="chart-placeholder">
                            <!-- Simulated Chart with CSS -->
                            <div class="bar-chart">
                                <div class="bar-group">
                                    <div class="bar-fill" style="height: 60%;"></div>
                                    <span class="bar-label">Senin</span>
                                </div>
                                <div class="bar-group">
                                    <div class="bar-fill" style="height: 80%;"></div>
                                    <span class="bar-label">Selasa</span>
                                </div>
                                <div class="bar-group">
                                    <div class="bar-fill" style="height: 100%;"></div>
                                    <span class="bar-label">Rabu</span>
                                </div>
                                <div class="bar-group">
                                    <div class="bar-fill" style="height: 40%;"></div>
                                    <span class="bar-label">Kamis</span>
                                </div>
                                <div class="bar-group">
                                    <div class="bar-fill" style="height: 90%;"></div>
                                    <span class="bar-label">Jum'at</span>
                                </div>
                            </div>
                            <p class="chart-desc text-center mt-3">Persentase penyelesaian logbook & kehadiran</p>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi Submit Baru -->
                <div class="content-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-bell text-orange"></i> Notifikasi Terbaru</h4>
                        <a href="#" class="link-view-all">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <ul class="notification-list">
                            <li class="notification-item unread">
                                <div class="notif-icon bg-blue-light"><i class="fas fa-file-alt"></i></div>
                                <div class="notif-content">
                                    <p class="notif-text"><strong>Andi Darmawan</strong> mengirim logbook harian baru.</p>
                                    <span class="notif-time">10 Menit yang lalu</span>
                                </div>
                                <button class="btn-action-small">Review</button>
                            </li>
                            <li class="notification-item unread">
                                <div class="notif-icon bg-orange-light"><i class="fas fa-clock"></i></div>
                                <div class="notif-content">
                                    <p class="notif-text"><strong>Siti Aminah</strong> meminta persetujuan izin sakit.</p>
                                    <span class="notif-time">1 Jam yang lalu</span>
                                </div>
                                <button class="btn-action-small">Review</button>
                            </li>
                            <li class="notification-item">
                                <div class="notif-icon bg-green-light"><i class="fas fa-check"></i></div>
                                <div class="notif-content">
                                    <p class="notif-text">Evaluasi bulanan <strong>Budi Santoso</strong> berhasil disimpan.
                                    </p>
                                    <span class="notif-time">Kemarin, 14:30</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Quick Links & Summary -->
            <div class="main-right">

                <!-- Evaluasi Summary -->
                <div class="content-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-clipboard-list text-purple"></i> Ringkasan Evaluasi</h4>
                    </div>
                    <div class="card-body">
                        <div class="eval-summary-item">
                            <div class="eval-info">
                                <span class="eval-name">Disiplin & Kehadiran</span>
                                <span class="eval-score">A- (90)</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 90%; background: #22c55e;"></div>
                            </div>
                        </div>
                        <div class="eval-summary-item mt-3">
                            <div class="eval-info">
                                <span class="eval-name">Keterampilan Teknis</span>
                                <span class="eval-score">B+ (85)</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 85%; background: #3b82f6;"></div>
                            </div>
                        </div>
                        <div class="eval-summary-item mt-3">
                            <div class="eval-info">
                                <span class="eval-name">Sikap Kerja</span>
                                <span class="eval-score">A (95)</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: 95%; background: #8b5cf6;"></div>
                            </div>
                        </div>
                        <div class="eval-action-container mt-4">
                            <button class="btn-primary-full">Isi Form Evaluasi</button>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="content-card">
                    <div class="card-header">
                        <h4 class="card-title"><i class="fas fa-bolt text-warning"></i> Aksi Cepat</h4>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions-grid">
                            <a href="{{ route('pembimbing.siswa') }}" class="quick-action-btn">
                                <i class="fas fa-user-graduate"></i>
                                <span>Daftar Siswa</span>
                            </a>
                            <a href="{{ route('pembimbing.siswa') }}" class="quick-action-btn">
                                <i class="fas fa-calendar-check"></i>
                                <span>Validasi Absen</span>
                            </a>
                            <a href="{{ route('pembimbing.siswa') }}" class="quick-action-btn">
                                <i class="fas fa-book"></i>
                                <span>Review Logbook</span>
                            </a>
                            <a href="{{ route('pembimbing.siswa') }}" class="quick-action-btn">
                                <i class="fas fa-file-pdf"></i>
                                <span>Cetak Laporan</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection