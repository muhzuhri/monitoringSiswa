@extends('layouts.nav.guru')

@section('title', 'Logbook ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/daftarSiswa.css') }}">
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
                            <th class="ps-4" width="20%"><i class="far fa-calendar-alt me-1"></i> Tanggal</th>
                            <th width="45%"><i class="fas fa-tasks me-1"></i> Kegiatan / Pekerjaan</th>
                            <th width="15%" class="text-center"><i class="fas fa-info-circle me-1"></i> Status</th>
                            <th width="20%" class="text-end pe-4"><i class="fas fa-tools me-1"></i> Aksi</th>
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
                                    <div class="kegiatan-preview mb-1">{{ Str::limit($log->kegiatan, 150) }}</div>
                                    @if($log->catatan_pembimbing)
                                        <div class="catatan-badge small {{ $log->status == 'rejected' ? 'text-danger' : 'text-success' }}" style="font-style: italic;">
                                            <i class="fas fa-comment-dots me-1"></i> "{{ Str::limit($log->catatan_pembimbing, 50) }}"
                                        </div>
                                    @endif
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
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($log->foto)
                                            <a href="{{ asset('storage/' . $log->foto) }}" target="_blank" class="btn-small p-0 overflow-hidden" style="width: 32px; height: 32px; border-radius: 6px;" title="Lihat Foto">
                                                <img src="{{ asset('storage/' . $log->foto) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </a>
                                        @endif
                                        <button class="btn-small" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $log->id_kegiatan }}" 
                                                style="padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; background: var(--primary-light); color: var(--primary-color); border: none;">
                                            <i class="fas fa-clipboard-check"></i> Verifikasi
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Verifikasi (Moved inside forelse for context) -->
                            <div class="modal fade modal-overlay" id="modalVerifikasi{{ $log->id_kegiatan }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content modal-content-custom" style="border-radius: 20px; border: none; overflow: hidden;">
                                        <div class="modal-header modal-header-custom" style="background: var(--primary-color); color: white; border: none;">
                                            <h5 class="modal-title-custom" style="font-weight: 700;">Verifikasi Logbook</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('guru.logbook.verifikasi', $log->id_kegiatan) }}" method="POST">
                                            @csrf
                                            <div class="modal-body modal-body-custom p-4">
                                                <div class="bg-light p-3 rounded-4 mb-4" style="border-left: 4px solid var(--primary-color);">
                                                    <label class="section-label small fw-bold text-muted mb-1 text-uppercase">Isi Kegiatan:</label>
                                                    <p class="mb-0 small">{{ $log->kegiatan }}</p>
                                                </div>
                                                
                                                <div class="form-group-custom mb-4">
                                                    <label class="section-label small fw-bold text-muted mb-2 text-uppercase">Keputusan Verifikasi</label>
                                                    <div class="decision-grid d-flex gap-2">
                                                        <input type="radio" class="btn-check" name="status" id="approve{{ $log->id_kegiatan }}" value="verified" {{ $log->status == 'verified' ? 'checked' : '' }} required>
                                                        <label class="btn btn-outline-success flex-grow-1 rounded-3 py-2" for="approve{{ $log->id_kegiatan }}">
                                                            <i class="fas fa-check-circle me-1"></i> SETUJUI
                                                        </label>
            
                                                        <input type="radio" class="btn-check" name="status" id="reject{{ $log->id_kegiatan }}" value="rejected" {{ $log->status == 'rejected' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-danger flex-grow-1 rounded-3 py-2" for="reject{{ $log->id_kegiatan }}">
                                                            <i class="fas fa-times-circle me-1"></i> TOLAK
                                                        </label>
                                                    </div>
                                                </div>
            
                                                <div class="form-group-custom">
                                                    <label class="section-label small fw-bold text-muted mb-2 text-uppercase">Catatan / Feedback</label>
                                                    <textarea name="catatan" class="form-control rounded-3" style="min-height: 100px; font-size: 0.9rem;" placeholder="Berikan catatan perbaikan atau apresiasi...">{{ $log->catatan_pembimbing }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">Simpan Verifikasi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state text-center p-5">
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
