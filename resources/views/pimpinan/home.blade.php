@extends('layouts.nav.pimpinan')

@section('title', 'Dashboard Pimpinan - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/dashboard.css') }}">
    <style>
        .pimpinan-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
        }
        .pimpinan-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .icon-siswa { background: #eff6ff; color: #3b82f6; }
        .icon-guru { background: #f0fdf4; color: #22c55e; }
        .icon-pembimbing { background: #fefce8; color: #eab308; }
        .stat-value { font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 5px; }
        .stat-label { color: #64748b; font-size: 14px; font-weight: 500; }
    </style>
@endpush

@section('body')
    <div class="dashboard-container py-4">
        <div class="container">
            <div class="pimpinan-hero">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-2">Selamat Datang, {{ $user->nama }}</h2>
                        <p class="opacity-90 mb-0">Monitor aktivitas magang siswa secara real-time dan pantau laporan rekapitulasi.</p>
                        <div class="mt-3">
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">
                                <i class="fas fa-shield-alt me-1"></i> Mode Pimpinan
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <i class="fas fa-chart-line fa-6x opacity-20"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon icon-siswa">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-value">{{ $stats['total_siswa'] }}</div>
                        <div class="stat-label">Total Siswa Terdaftar</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon icon-siswa" style="background: #ecfdf5; color: #10b981;">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-value">{{ $stats['siswa_aktif'] }}</div>
                        <div class="stat-label">Siswa Sedang Magang</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon icon-guru">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="stat-value">{{ $stats['total_guru'] }}</div>
                        <div class="stat-label">Total Guru Pembimbing</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon icon-pembimbing">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-value">{{ $stats['total_pembimbing'] }}</div>
                        <div class="stat-label">Total Pembimbing Lapangan</div>
                    </div>
                </div>
            </div>

            <!-- Management Section -->
            <h5 class="fw-bold mb-4">Navigasi Cepat</h5>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-icon m-auto mb-3" style="width:60px; height:60px; background: #e0f2fe; color:#0369a1; font-size:30px;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="fw-bold">Manajemen Siswa</h6>
                        <p class="small text-muted mb-4">Lihat data biodata, lokasi penempatan, dan riwayat magang seluruh siswa.</p>
                        <a href="{{ route('pimpinan.siswa') }}" class="btn btn-primary w-100 rounded-pill fw-bold">Buka Data Siswa</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-icon m-auto mb-3" style="width:60px; height:60px; background: #dcfce7; color:#15803d; font-size:30px;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <h6 class="fw-bold">Rekapitulasi Laporan</h6>
                        <p class="small text-muted mb-4">Unduh rekap data pendaftaran siswa per tahun ajaran dalam format PDF.</p>
                        <a href="{{ route('pimpinan.rekap') }}" class="btn btn-success w-100 rounded-pill fw-bold">Buka Menu Rekap</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-icon m-auto mb-3" style="width:60px; height:60px; background: #fef3c7; color:#b45309; font-size:30px;">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <h6 class="fw-bold">Pembimbing</h6>
                        <p class="small text-muted mb-4">Pantau persebaran guru pembimbing dan pembimbing lapangan mitra.</p>
                        <div class="d-flex gap-2">
                             <a href="{{ route('pimpinan.guru') }}" class="btn btn-outline-warning w-100 rounded-pill fw-bold small">Guru</a>
                             <a href="{{ route('pimpinan.pembimbing') }}" class="btn btn-outline-warning w-100 rounded-pill fw-bold small">PL</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
