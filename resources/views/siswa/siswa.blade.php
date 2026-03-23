@extends('layouts.nav.siswa')

@section('title', 'Dashboard Siswa - SIM Magang Fasilkom Unsri')
@section('body-class', 'dashboard-page siswa-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/siswa.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/siswa/home-siswa.js') }}"></script>
@endpush

@section('body')

    {{-- @include('layouts.nav.siswa', ['user' => $user]) --}}

    <div class="body-style">

        <div class="container">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-container">
                    <div class="hero-left">
                        <h1 class="hero-title">
                            Halo, {{ $user->nama }}!
                        </h1>
    
                        <p class="hero-subtitle">
                            Selamat datang kembali di Dashboard Monitoring Magang.
                        </p>
    
                        <p class="hero-company">
                            <i class="fas fa-building"></i>
                            {{ $user->perusahaan ?? 'Lokasi Magang Belum Diatur' }}
                        </p>
                    </div>
    
                    <div class="hero-right">
                        <div class="status-card">
                            <small class="status-label">Status Magang</small>
                            <span class="status-badge">
                                <i class="fas fa-check-circle"></i>
                                AKTIF
                            </span>
                        </div>
                    </div>
                </div>
            </div>
    
            <!-- Quick Stats & Notifications -->
            <div class="dashboard-layout">
                <div class="dashboard-main">
    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h5 class="card-title">Progres Magang</h5>
                            <span class="card-percentage">{{ $progress }}%</span>
                        </div>
    
                        <div class="progress-wrapper">
                            <div class="progress-bar-custom" style="width: {{ $progress }}%">
                            </div>
                        </div>
    
                        <div class="card-footer">
                            <span class="start-date">
                                <i class="fas fa-calendar-alt"></i>
                                Mulai:
                                {{ $user->tgl_mulai_magang ? \Carbon\Carbon::parse($user->tgl_mulai_magang)->translatedFormat('d M Y') : '-' }}
                            </span>
    
                            <span class="end-date">
                                Berakhir:
                                {{ $user->tgl_selesai_magang ? \Carbon\Carbon::parse($user->tgl_selesai_magang)->translatedFormat('d M Y') : '-' }}
                                <i class="fas fa-flag-checkered"></i>
                            </span>
                        </div>
                    </div>
                    <!-- Stats Grid -->
                    <div class="dashboard-card2">
                        <div class="stats-wrapper">
    
                            <div class="stat-card stat-blue">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>{{ $hariDijalani }}</h3>
                                    <span>Hari Dijalani</span>
                                </div>
                            </div>
    
                            <div class="stat-card stat-green">
                                <div class="stat-icon">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>{{ $logbookTerisi }}</h3>
                                    <span>Logbook Terisi</span>
                                </div>
                            </div>
    
                            <div class="stat-card stat-purple">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>{{ $totalHadir }}</h3>
                                    <span>Total Hadir</span>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
    
                <!-- Right Column: Notifications & Status -->
                <div class="sidebar-wrapper">
    
                    <div class="dashboard-card status-card 
                    {{ $logbookHariIni ? 'status-success' : 'status-warning' }}">
    
                        <h6 class="status-title">Status Hari Ini</h6>
    
                        <div class="status-content">
    
                            <div class="status-indicator">
                                @if($logbookHariIni)
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-exclamation"></i>
                                @endif
                            </div>
    
                            <div class="status-text">
                                @if($logbookHariIni)
                                    <h6>Logbook Sudah Diisi</h6>
                                @else
                                    <h6>Logbook Belum Diisi</h6>
                                @endif
    
                                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                            </div>
    
                        </div>
    
                        @if(!$logbookHariIni)
                            <a href="{{ route('siswa.absensi') }}#pills-logbook" class="status-button warning">
                                Isi Logbook Sekarang
                            </a>
                        @else
                            <a href="{{ route('siswa.absensi') }}" class="status-button success">
                                Lihat Riwayat
                            </a>
                        @endif
    
                    </div>
    
                </div>
            </div>
        </div>
    </div>
@endsection