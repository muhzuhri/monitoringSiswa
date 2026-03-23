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
                <p class="page-subtitle">{{ $siswa->perusahaan }} &nbsp;|&nbsp; NISN: {{ $siswa->nisn }}</p>
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
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Bukti Foto</th>
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
                                <td class="td-jam">
                                    {{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}
                                </td>
                                <td class="td-jam">
                                    {{ $a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}
                                </td>
                                <td>
                                    <div class="photo-group">
                                        @if($a->foto_masuk)
                                            <a href="{{ asset('storage/' . $a->foto_masuk) }}" target="_blank" title="Foto Masuk">
                                                <img src="{{ asset('storage/' . $a->foto_masuk) }}" class="photo-thumbnail" alt="Foto Masuk">
                                            </a>
                                        @endif
                                        @if($a->foto_pulang)
                                            <a href="{{ asset('storage/' . $a->foto_pulang) }}" target="_blank" title="Foto Pulang">
                                                <img src="{{ asset('storage/' . $a->foto_pulang) }}" class="photo-thumbnail" alt="Foto Pulang">
                                            </a>
                                        @endif
                                        @if(!$a->foto_masuk && !$a->foto_pulang)
                                            <span class="no-photo">—</span>
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
