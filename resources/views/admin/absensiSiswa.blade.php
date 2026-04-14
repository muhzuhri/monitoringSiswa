@extends('layouts.nav.admin')

@section('title', 'Rekap Absensi ' . $siswa->nama . ' - Administrasi')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/absensiSiswa.css') }}">
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
        
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: #fff; padding: 1.5rem; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border); }
        .stat-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.5rem; }
        .stat-number { font-size: 1.75rem; font-weight: 800; color: var(--primary); }
        
        .stat-hadir { border-left: 4px solid #10b981; }
        .stat-izin { border-left: 4px solid #f59e0b; }
        .stat-alpa { border-left: 4px solid #ef4444; }
        .stat-total { border-left: 4px solid #3b82f6; }

        .ui-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid var(--border); overflow: hidden; }
        .card-head { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); background: #f8fafc; }
        .card-title { margin: 0; font-weight: 700; font-size: 1rem; color: var(--primary); }
        
        .table-wrapper { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); }
        .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; vertical-align: middle; }
        
        .status-badge { padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; }
        .status-hadir { background: #ecfdf5; color: #065f46; }
        .status-terlambat { background: #fffbeb; color: #92400e; }
        .status-izin { background: #eff6ff; color: #1e40af; }
        .status-alpa { background: #fef2f2; color: #991b1b; }
        
        .empty-row { padding: 4rem !important; text-align: center; color: var(--text-muted); }
        .btn-export { background: var(--primary); color: #fff; padding: 0.6rem 1.25rem; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
        .btn-export:hover { opacity: 0.9; transform: translateY(-1px); color: #fff; }
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
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h4 class="page-title">Rekap Absensi: {{ $siswa->nama }}</h4>
                <p class="page-subtitle"><i class="fas fa-university me-1"></i> {{ $siswa->sekolah }} &nbsp;|&nbsp; <i class="fas fa-building me-1"></i> {{ $siswa->perusahaan ?? 'Belum Penempatan' }}</p>
            </div>
            {{-- <a href="#" class="btn-export">
                <i class="fas fa-file-pdf"></i> Export PDF (Coming Soon)
            </a> --}}
        </div>

        {{-- Statistik Rekap --}}
        <div class="stat-grid">
            <div class="stat-card stat-hadir">
                <h6 class="stat-label">Hadir</h6>
                <div class="stat-number">{{ $rekap['hadir'] }}</div>
            </div>
            <div class="stat-card stat-izin">
                <h6 class="stat-label">Izin / Sakit</h6>
                <div class="stat-number">{{ $rekap['izin'] + $rekap['sakit'] }}</div>
            </div>
            <div class="stat-card stat-alpa">
                <h6 class="stat-label">Alpa</h6>
                <div class="stat-number">{{ $rekap['alpa'] }}</div>
            </div>
            <div class="stat-card stat-total">
                <h6 class="stat-label">Total Hari</h6>
                <div class="stat-number">{{ $rekap['total'] }}</div>
            </div>
        </div>

        {{-- Tabel Riwayat Presensi --}}
        <div class="ui-card">
            <div class="card-head">
                <h6 class="card-title">Riwayat Presensi Keseluruhan</h6>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Hari, Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Masuk</th>
                            <th class="text-center">Pulang</th>
                            <th class="text-end pe-4">Bukti Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $a)
                            <tr>
                                <td class="fw-bold text-dark">
                                    {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td>
                                    @if($a->status == 'hadir')
                                        <span class="status-badge status-hadir">Hadir</span>
                                    @elseif($a->status == 'terlambat')
                                        <span class="status-badge status-terlambat">Terlambat</span>
                                    @elseif($a->status == 'izin' || $a->status == 'sakit')
                                        <span class="status-badge status-izin">{{ ucfirst($a->status) }}</span>
                                    @else
                                        <span class="status-badge status-alpa">Alpa</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    {{ $a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if($a->foto_masuk)
                                            <a href="{{ asset('storage/' . $a->foto_masuk) }}" target="_blank" class="p-1">
                                                <img src="{{ asset('storage/' . $a->foto_masuk) }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;" title="Foto Masuk">
                                            </a>
                                        @endif
                                        @if($a->foto_pulang)
                                            <a href="{{ asset('storage/' . $a->foto_pulang) }}" target="_blank" class="p-1">
                                                <img src="{{ asset('storage/' . $a->foto_pulang) }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;" title="Foto Pulang">
                                            </a>
                                        @endif
                                        @if(!$a->foto_masuk && !$a->foto_pulang)
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">Belum ada riwayat presensi yang terekam.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absensis->hasPages())
                <div class="p-4 border-top">
                    {{ $absensis->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
