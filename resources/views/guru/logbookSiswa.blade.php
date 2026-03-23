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
        <main class="logbook-grid">
            @forelse($logbooks as $log)
                <article class="logbook-card">
                    <div class="card-content-grid">
                        <!-- Info Section -->
                        <div class="info-section">
                            <header class="info-header">
                                <div class="date-badge">
                                    <i class="far fa-calendar-alt"></i> 
                                    {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                                </div>
                                @if($log->status == 'verified')
                                    <span class="status-badge status-approved">Disetujui</span>
                                @elseif($log->status == 'rejected')
                                    <span class="status-badge status-rejected">Ditolak</span>
                                @else
                                    <span class="status-badge status-pending">Menunggu Verifikasi</span>
                                @endif
                            </header>

                            <h2 class="section-label">Kegiatan / Pekerjaan:</h2>
                            <p class="kegiatan-text">{{ $log->kegiatan }}</p>

                            @if($log->catatan_pembimbing)
                                <div class="pembimbing-note {{ $log->status == 'rejected' ? 'note-danger' : 'note-success' }}">
                                    <span class="note-label">CATATAN PEMBIMBING:</span>
                                    <p class="note-text">"{{ $log->catatan_pembimbing }}"</p>
                                </div>
                            @endif

                            <div class="action-footer">
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $log->id_kegiatan }}">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Verifikasi</span>
                                </button>
                            </div>
                        </div>

                        <!-- Photo Section -->
                        <aside class="photo-container">
                            <span class="section-label">BUKTI KEGIATAN:</span>
                            @if($log->foto)
                                <a href="{{ asset('storage/' . $log->foto) }}" target="_blank" class="photo-wrapper">
                                    <img src="{{ asset('storage/' . $log->foto) }}" class="logbook-photo" alt="Bukti Kegiatan">
                                </a>
                            @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-image"></i>
                                    <span>Tidak ada foto bukti</span>
                                </div>
                            @endif
                        </aside>
                    </div>
                </article>

                <!-- Modal Verifikasi -->
                <div class="modal fade modal-overlay" id="modalVerifikasi{{ $log->id_kegiatan }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content modal-content-custom">
                            <div class="modal-header modal-header-custom">
                                <h5 class="modal-title-custom">Verifikasi Logbook</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('guru.logbook.verifikasi', $log->id_kegiatan) }}" method="POST">
                                @csrf
                                <div class="modal-body modal-body-custom">
                                    <p class="modal-kegiatan-summary"><strong>Kegiatan:</strong> {{ Str::limit($log->kegiatan, 100) }}</p>
                                    
                                    <div class="form-group-custom">
                                        <label class="section-label">Keputusan Verifikasi</label>
                                        <div class="decision-grid">
                                            <input type="radio" class="btn-check" name="status" id="approve{{ $log->id_kegiatan }}" value="verified" {{ $log->status == 'verified' ? 'checked' : '' }} required>
                                            <label class="decision-btn-custom decision-btn-success" for="approve{{ $log->id_kegiatan }}">
                                                <i class="fas fa-check-circle"></i> SETUJUI
                                            </label>

                                            <input type="radio" class="btn-check" name="status" id="reject{{ $log->id_kegiatan }}" value="rejected" {{ $log->status == 'rejected' ? 'checked' : '' }}>
                                            <label class="decision-btn-custom decision-btn-danger" for="reject{{ $log->id_kegiatan }}">
                                                <i class="fas fa-times-circle"></i> TOLAK
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="section-label">Catatan / Feedback (Opsional)</label>
                                        <textarea name="catatan" class="custom-textarea" placeholder="Contoh: Deskripsi sudah bagus, lanjutkan.">{{ $log->catatan_pembimbing }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer modal-footer-custom">
                                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-save">Simpan Verifikasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon-box">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h2 class="empty-title">Belum ada Logbook</h2>
                    <p class="empty-desc">Siswa belum mengunggah kegiatan untuk kategori yang dipilih.</p>
                </div>
            @endforelse
        </main>
    </div>
@endsection
