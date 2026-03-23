@extends('layouts.nav.pembimbing')

@section('title', 'Laporan & Rekap Siswa')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/laporanSiswa.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">

        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title"><i class="fas fa-file-pdf text-danger me-2"></i>Laporan & Rekap Siswa</h2>
                <p class="page-subtitle">Generate laporan rekapitulasi nilai, absensi, dan logbook untuk keperluan
                    pengarsipan.</p>
            </div>
            <div class="header-actions">
                <form action="{{ route('pembimbing.laporan') }}" method="GET" class="search-form">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Cari Siswa..."
                        value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('pembimbing.laporan') }}" class="btn-clear-search" title="Clear Search"><i
                                class="fas fa-times"></i></a>
                    @endif
                    <button type="submit" class="btn-search">Cari</button>
                </form>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('error'))
            <div class="custom-alert alert-danger-soft mb-4">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        <div class="content-card mt-4">
            <div class="card-body p-0">
                <div class="table-responsive custom-table-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Siswa Binaan</th>
                                <th width="20%">Rekap Kehadiran</th>
                                <th width="20%">Rekap Logbook</th>
                                <th width="25%" class="text-center">Cetak Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($siswas as $index => $siswa)
                                <tr>
                                    <td>{{ $siswas->firstItem() + $index }}</td>
                                    <td>
                                        <div class="user-info-cell">
                                            @if($siswa->foto_profil)
                                                <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="Foto"
                                                    class="avatar-sm">
                                            @else
                                                <div class="avatar-sm-placeholder bg-danger-light text-danger">
                                                    <span>{{ substr($siswa->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div class="user-details">
                                                <span class="fw-bold text-dark">{{ $siswa->name }}</span>
                                                <span class="text-muted small">{{ $siswa->nisn }} |
                                                    {{ $siswa->sekolah ?? 'Sekolah tidak diketahui' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-badge bg-success-light">
                                            <i class="fas fa-calendar-check text-success"></i>
                                            <span><strong>{{ $siswa->total_hadir }}</strong> Hari Hadir</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="small text-muted"><strong>{{ $siswa->total_logbook }}</strong> Total
                                                Entri</span>
                                            <div class="progress-simple">
                                                @php
                                                    $pct = $siswa->total_logbook > 0 ? ($siswa->disetujui_logbook / $siswa->total_logbook) * 100 : 0;
                                                @endphp
                                                <div class="progress-bar-fill bg-info" style="width: {{ $pct }}%"
                                                    title="{{ $siswa->disetujui_logbook }} Disetujui"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons justify-content-center gap-2">
                                            <a href="{{ route('dosen.laporan.cetak', $siswa->nisn) }}" target="_blank"
                                                class="btn-action-primary" title="PDF Template Cetak">
                                                <i class="fas fa-print"></i> Cetak Laporan
                                            </a>
                                            <!-- Placeholder for excel export if needed
                                                <button type="button" class="btn-action-icon btn-export" title="Export Excel">
                                                    <i class="fas fa-file-excel text-success"></i>
                                                </button>
                                                -->
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-file-slash empty-icon"></i>
                                            <h4>Belum ada data untuk dilaporan</h4>
                                            <p class="text-muted">Tidak ditemukan data siswa binaan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($siswas->hasPages())
                    <div class="pagination-wrapper">
                        {{ $siswas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection