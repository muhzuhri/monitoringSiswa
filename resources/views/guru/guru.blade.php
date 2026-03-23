@extends('layouts.nav.guru')

@section('title', 'Dashboard Guru - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/home-guru.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">

        {{-- Hero Section --}}
        <div class="hero-section">
            <div class="hero-content">
                <h2 class="hero-title">Selamat Datang, {{ $user->nama }}!</h2>
                <p class="hero-subtitle">Pantau progres dan aktivitas magang siswa bimbingan Anda hari ini.</p>
                <div class="hero-badge-group">
                    <span class="hero-badge">
                        <i class="fas fa-school"></i>{{ $user->sekolah }}
                    </span>
                </div>
            </div>
            <div class="hero-icon">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="stat-grid">
            <div class="stat-card stat-total">
                <div class="stat-body">
                    <div>
                        <p class="stat-label">Total Siswa</p>
                        <h3 class="stat-value">{{ $totalSiswa }}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="stat-card stat-missing">
                <div class="stat-body">
                    <div>
                        <p class="stat-label">Belum Absen/Logbook</p>
                        <h3 class="stat-value">{{ $belumAbsen + $belumLogbook }}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
            <div class="stat-card stat-progress">
                <div class="stat-body">
                    <div>
                        <p class="stat-label">Rata Progres PKL</p>
                        <h3 class="stat-value">{{ $rataProgress }}%</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
            <div class="stat-card stat-pending">
                <div class="stat-body">
                    <div>
                        <p class="stat-label">Butuh Verifikasi</p>
                        <h3 class="stat-value">{{ $pendingLogbookCount + $pendingLaporanCount }}</h3>
                    </div>
                    <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
                </div>
            </div>
        </div>

        {{-- Layout 2 Kolom --}}
        <div class="dashboard-layout">

            {{-- Kolom Utama --}}
            <div class="dashboard-main">

                {{-- Notifikasi --}}
                @if($pendingLogbookCount > 0 || $pendingLaporanCount > 0)
                    <div class="ui-card notif-card">
                        <div class="card-head">
                            <h6 class="card-title text-primary"><i class="fas fa-bell"></i> Notifikasi Penting</h6>
                        </div>
                        <div class="card-body-flush">
                            @if($pendingLogbookCount > 0)
                                <div class="notif-item notif-border">
                                    <div class="icon-circle icon-primary">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div class="notif-text">
                                        <h6 class="notif-title">Verifikasi Logbook</h6>
                                        <p class="notif-desc">Ada {{ $pendingLogbookCount }} logbook baru yang menunggu verifikasi Anda.</p>
                                    </div>
                                    <a href="{{ route('guru.verifikasi') }}" class="btn-notif btn-notif-primary">Periksa</a>
                                </div>
                            @endif
                            @if($pendingLaporanCount > 0)
                                <div class="notif-item">
                                    <div class="icon-circle icon-warning">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="notif-text">
                                        <h6 class="notif-title">Laporan Akhir Menunggu</h6>
                                        <p class="notif-desc">{{ $pendingLaporanCount }} siswa telah mengunggah laporan akhir.</p>
                                    </div>
                                    <a href="{{ route('guru.verifikasi') }}" class="btn-notif btn-notif-warning">Periksa</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Chart Section --}}
                <div class="chart-grid">
                    <div class="ui-card">
                        <div class="card-head">
                            <h6 class="card-title">Presensi Siswa (Bulan Ini)</h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="presensiChart"></canvas>
                        </div>
                    </div>
                    <div class="ui-card">
                        <div class="card-head">
                            <h6 class="card-title">Rata-rata Nilai Siswa</h6>
                        </div>
                        <div class="chart-container">
                            <canvas id="nilaiChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Tabel Siswa --}}
                <div class="ui-card table-card">
                    <div class="card-head card-head-flex">
                        <h6 class="card-title">Siswa Magang (Terbaru)</h6>
                        <a href="{{ route('guru.siswa') }}" class="link-more">Lihat Semua</a>
                    </div>
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Perusahaan</th>
                                    <th>Status Hari Ini</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswaPreviews as $s)
                                    <tr>
                                        <td class="td-padded">
                                            <div class="student-info">
                                                <div class="avatar-sm avatar-primary">
                                                    {{ strtoupper(substr($s->nama, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="student-name">{{ $s->nama }}</h6>
                                                    <small class="student-nisn">{{ $s->nisn }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><small class="text-bold-muted">{{ $s->perusahaan }}</small></td>
                                        <td>
                                            @php $a = $s->absensis->first(); @endphp
                                            @if($a)
                                                <span class="status-badge status-hadir">
                                                    <i class="fas fa-check-circle"></i> {{ ucfirst($a->status) }}
                                                </span>
                                            @else
                                                <span class="status-badge status-absen">
                                                    <i class="fas fa-times-circle"></i> Belum Absen
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="btn-icon-only"><i class="fas fa-chevron-right"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-row">Belum ada siswa bimbingan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom Samping --}}
            <div class="dashboard-sidebar">

                {{-- Pantau Absensi --}}
                <div class="ui-card card-gradient-primary">
                    <div class="gradient-body">
                        <i class="fas fa-calendar-check gradient-icon"></i>
                        <h5 class="gradient-title">Pantau Absensi</h5>
                        <p class="gradient-desc">Pastikan setiap siswa melakukan absensi dan logbook tepat waktu.</p>
                        <a href="{{ route('guru.siswa') }}" class="btn-light-full">Buka Laporan Absensi</a>
                    </div>
                </div>

                {{-- Aksi Cepat --}}
                <div class="ui-card">
                    <div class="card-head">
                        <h6 class="card-title">Aksi Cepat</h6>
                    </div>
                    <div class="quick-actions">
                        <a href="{{ route('guru.penilaian') }}" class="quick-action-btn action-primary">
                            <div class="action-icon-wrap">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <h6 class="action-title">Tambah Penilaian</h6>
                                <p class="action-desc">Beri nilai untuk kompetensi siswa.</p>
                            </div>
                        </a>
                        <a href="{{ route('guru.verifikasi') }}" class="quick-action-btn action-success">
                            <div class="action-icon-wrap">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <h6 class="action-title">Verifikasi Laporan</h6>
                                <p class="action-desc">Cek dan setujui laporan akhir.</p>
                            </div>
                        </a>
                        <a href="{{ route('guru.siswa') }}" class="quick-action-btn action-warning">
                            <div class="action-icon-wrap">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h6 class="action-title">Riwayat Bimbingan</h6>
                                <p class="action-desc">Lihat kembali catatan bimbingan.</p>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const presensiCtx = document.getElementById('presensiChart').getContext('2d');
        new Chart(presensiCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    data: [{{ $statsPresensi['hadir'] }}, {{ $statsPresensi['izin'] }}, {{ $statsPresensi['sakit'] }}, {{ $statsPresensi['alpa'] }}],
                    backgroundColor: ['#198754', '#ffc107', '#0dcaf0', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { size: 10 } } } }, maintainAspectRatio: false }
        });

        const nilaiCtx = document.getElementById('nilaiChart').getContext('2d');
        new Chart(nilaiCtx, {
            type: 'bar',
            data: {
                labels: ['Teknis', 'Non-Teknis'],
                datasets: [{ label: 'Rata-rata Nilai', data: [{{ $avgNilaiTeknis }}, {{ $avgNilaiNonTeknis }}], backgroundColor: ['#0d6efd', '#20c997'], borderRadius: 8 }]
            },
            options: { scales: { y: { beginAtZero: true, max: 100, ticks: { font: { size: 10 } } }, x: { ticks: { font: { size: 10 } } } }, plugins: { legend: { display: false } }, maintainAspectRatio: false }
        });
    </script>
@endpush
