@extends('layouts.nav.admin')

@section('title', 'Riwayat Kegiatan ' . $siswa->nama . ' - Administrasi')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/logbookSiswa.css') }}">
    <style>
        :root {
            --primary: #0f172a;
            --primary-light: #f1f5f9;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }
        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            background: #fff;
            padding: 1.5rem 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .breadcrumb-nav { margin-bottom: 0.5rem; }
        .breadcrumb-link { color: var(--text-muted); text-decoration: none; font-size: 0.85rem; }
        .breadcrumb-sep { margin: 0 0.5rem; color: #cbd5e1; }
        .breadcrumb-current { color: var(--primary); font-weight: 600; font-size: 0.85rem; }
        .page-title { margin: 0; font-weight: 800; color: var(--primary); }
        .page-subtitle { margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem; }

        .filter-header {
            background: #fff;
            padding: 1.25rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .filter-title { font-size: 0.85rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0; white-space: nowrap; }

        .logbook-list { display: flex; flex-direction: column; gap: 1.25rem; }
        .logbook-item {
            background: #fff;
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
            position: relative;
            transition: all 0.2s;
        }
        .logbook-item:hover { transform: translateX(8px); border-color: #cbd5e1; }
        
        .date-badge {
            min-width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }
        .date-day { font-size: 1.5rem; font-weight: 800; line-height: 1; }
        .date-month { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; margin-top: 2px; }

        .log-content { flex: 1; }
        .log-meta { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; }
        .log-status { font-size: 0.7rem; font-weight: 800; padding: 0.25rem 0.75rem; border-radius: 50px; text-transform: uppercase; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-verified { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .log-title { font-size: 1.1rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem; }
        .log-desc { color: var(--text-main); font-size: 0.95rem; line-height: 1.6; white-space: pre-line; }

        .log-photo { width: 120px; height: 120px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); cursor: pointer; }
        
        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
            background: #fff;
            border-radius: 24px;
            border: 2px dashed #e2e8f0;
        }
        .btn-status {
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            color: var(--text-muted);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .btn-status.active { background: var(--primary); color: #fff; border-color: var(--primary); }
    </style>
@endpush

@section('body')
    <div class="management-container" style="padding: 2rem;">
        {{-- Header --}}
        <div class="page-header">
            <div class="header-text">
                <nav class="breadcrumb-nav">
                    <a href="{{ route('admin.kelolaSiswa') }}" class="breadcrumb-link">Manajemen Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Riwayat Kegiatan</span>
                </nav>
                <h4 class="page-title">Riwayat Kegiatan: {{ $siswa->nama }}</h4>
                <p class="page-subtitle"><i class="fas fa-university me-1"></i> {{ $siswa->sekolah }} &nbsp;|&nbsp; <i class="fas fa-building me-1"></i> {{ $siswa->perusahaan ?? 'Belum Penempatan' }}</p>
            </div>
        </div>

        {{-- Filter Status --}}
        <div class="filter-header">
            <p class="filter-title"><i class="fas fa-filter me-2 text-primary"></i> Filter Status:</p>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.siswa.logbook', $siswa->nisn) }}" class="btn-status {{ !$status ? 'active' : '' }}">Semua</a>
                <a href="{{ route('admin.siswa.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending']) }}" class="btn-status {{ $status == 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('admin.siswa.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified']) }}" class="btn-status {{ $status == 'verified' ? 'active' : '' }}">Diverifikasi</a>
                <a href="{{ route('admin.siswa.logbook', ['nisn' => $siswa->nisn, 'status' => 'rejected']) }}" class="btn-status {{ $status == 'rejected' ? 'active' : '' }}">Ditolak</a>
            </div>
        </div>

        {{-- Logbook Items --}}
        <div class="logbook-list">
            @forelse($logbooks as $log)
                <div class="logbook-item">
                    <div class="date-badge">
                        <span class="date-day">{{ \Carbon\Carbon::parse($log->tanggal)->format('d') }}</span>
                        <span class="date-month">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('M') }}</span>
                    </div>
                    <div class="log-content">
                        <div class="log-meta">
                            <span class="log-status status-{{ $log->status }}">
                                <i class="fas {{ $log->status == 'verified' ? 'fa-check-circle' : ($log->status == 'rejected' ? 'fa-times-circle' : 'fa-clock') }} me-1"></i>
                                {{ $log->status }}
                            </span>
                            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <h6 class="log-title">{{ $log->judul ?? 'Kegiatan Harian' }}</h6>
                        <p class="log-desc">{{ $log->kegiatan }}</p>
                        
                        @if($log->catatan_pembimbing)
                            <div class="mt-3 p-3 bg-light rounded-3 border-start border-4 border-primary">
                                <small class="fw-bold d-block mb-1 text-primary">Catatan Pembimbing:</small>
                                <p class="mb-0 small italic">"{{ $log->catatan_pembimbing }}"</p>
                            </div>
                        @endif
                    </div>
                    @if($log->foto)
                        <a href="{{ asset('storage/' . $log->foto) }}" target="_blank">
                            <img src="{{ asset('storage/' . $log->foto) }}" class="log-photo" alt="Lampiran">
                        </a>
                    @endif
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-book-open fa-4x mb-4 text-muted opacity-25"></i>
                    <h5 class="fw-bold">Belum Ada Catatan Kegiatan</h5>
                    <p class="text-muted">Siswa ini belum menginput catatan kegiatan untuk kriteria yang dipilih.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
