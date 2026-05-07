@extends('layouts.nav.guru')

@section('title', 'Logbook ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/logbookSiswa.css') }}">
@endpush

@section('body')
    <div class="logbook-wrapper">
        <!-- Header -->
        <header class="header-section">
            <div>
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <ul class="breadcrumb-list">
                        <li class="breadcrumb-item"><a href="{{ route('guru.siswa') }}">Daftar Siswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Logbook Siswa</li>
                    </ul>
                </nav>
                <h1 class="title-main">Logbook: {{ $siswa->nama }}</h1>
                <p class="subtitle-main">{{ $siswa->perusahaan }} | {{ $siswa->nisn }}</p>
            </div>
            
            <div class="filter-container">
                <span class="filter-label">FILTER STATUS:</span>
                <a href="{{ route('guru.logbook', $siswa->nisn) }}" 
                   class="filter-btn {{ !$status ? 'filter-btn-primary' : 'filter-btn-light' }}">Semua</a>
                <a href="{{ route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending']) }}" 
                   class="filter-btn {{ $status == 'pending' ? 'filter-btn-warning' : 'filter-btn-light' }}">Pending</a>
                <a href="{{ route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified']) }}" 
                   class="filter-btn {{ $status == 'verified' ? 'filter-btn-success' : 'filter-btn-light' }}">Approved</a>
                <a href="{{ route('guru.logbook', ['nisn' => $siswa->nisn, 'status' => 'rejected']) }}" 
                   class="filter-btn {{ $status == 'rejected' ? 'filter-btn-danger' : 'filter-btn-light' }}">Rejected</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ui-alert ui-alert-success" role="alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Logbook List -->
        <div class="ui-card mt-4">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="ps-4" width="25%"><i class="far fa-calendar-alt me-1"></i> Tanggal</th>
                            <th width="55%"><i class="fas fa-tasks me-1"></i> Kegiatan / Pekerjaan</th>
                            <th width="20%" class="text-center"><i class="fas fa-info-circle me-1"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('d F Y') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        @if($log->foto)
                                            <a href="{{ asset('storage/' . $log->foto) }}" target="_blank" class="flex-shrink-0" style="width: 48px; height: 48px; border-radius: 8px; overflow: hidden; display: block; border: 1px solid #eee;" title="Lihat Lampiran">
                                                <img src="{{ asset('storage/' . $log->foto) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </a>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div class="kegiatan-preview mb-1">{{ $log->kegiatan }}</div>
                                            @if($log->catatan_pembimbing)
                                                <div class="catatan-badge small {{ $log->status == 'rejected' ? 'text-danger' : 'text-success' }}" style="font-style: italic;">
                                                    <i class="fas fa-comment-dots me-1"></i> "{{ $log->catatan_pembimbing }}"
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($log->status == 'verified')
                                        <span class="status-badge status-approved small">Verified</span>
                                    @elseif($log->status == 'rejected')
                                        <span class="status-badge status-rejected small">Rejected</span>
                                    @else
                                        <span class="status-badge status-pending small">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="empty-state text-center p-5">
                                    <div class="empty-icon-box mb-3 opacity-25">
                                        <i class="fas fa-book-open fa-3x"></i>
                                    </div>
                                    <h2 class="empty-title h5">Belum ada Logbook</h2>
                                    <p class="empty-desc text-muted small">Siswa belum mengunggah kegiatan untuk kategori ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
