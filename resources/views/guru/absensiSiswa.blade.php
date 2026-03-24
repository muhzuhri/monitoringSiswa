@extends('layouts.nav.guru')

@section('title', 'Rekap Absensi ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/absensiSiswa.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">

        {{-- Header --}}
        <div class="page-header">
            <div class="header-text">
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <a href="{{ route('guru.siswa') }}" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h4 class="page-title">Rekap Absensi: {{ $siswa->nama }}</h4>
                <p class="page-subtitle"><i class="fas fa-building me-1"></i> {{ $siswa->perusahaan }} &nbsp;|&nbsp; <i class="fas fa-id-card me-1"></i> NISN: {{ $siswa->nisn }}</p>
            </div>
            <a href="{{ route('guru.absensi.export', $siswa->nisn) }}" class="btn-export">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
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
                <h6 class="card-title">Riwayat Presensi</h6>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-day me-1"></i> Hari, Tanggal</th>
                            <th><i class="fas fa-info-circle me-1"></i> Status</th>
                            <th class="text-center"><i class="fas fa-clock me-1"></i> Masuk</th>
                            <th class="text-center"><i class="fas fa-clock me-1"></i> Pulang</th>
                            <th class="text-end pe-4"><i class="fas fa-camera me-1"></i> Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $a)
                            <tr>
                                <td class="td-date">
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
                                <td class="text-center td-jam">
                                    {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}
                                </td>
                                <td class="text-center td-jam">
                                    {{ $a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="photo-group justify-content-end">
                                        @if($a->foto_masuk)
                                            <a href="{{ asset('storage/' . $a->foto_masuk) }}" target="_blank" title="Foto Masuk" class="photo-link">
                                                <img src="{{ asset('storage/' . $a->foto_masuk) }}" class="photo-thumbnail rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #eee;" alt="M">
                                            </a>
                                        @endif
                                        @if($a->foto_pulang)
                                            <a href="{{ asset('storage/' . $a->foto_pulang) }}" target="_blank" title="Foto Pulang" class="photo-link">
                                                <img src="{{ asset('storage/' . $a->foto_pulang) }}" class="photo-thumbnail rounded" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #eee;" alt="P">
                                            </a>
                                        @endif
                                        @if(!$a->foto_masuk && !$a->foto_pulang)
                                            <span class="no-photo opacity-50 small">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">Belum ada riwayat presensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absensis->hasPages())
                <div class="table-pagination">
                    {{ $absensis->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
