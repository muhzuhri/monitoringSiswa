@extends('layouts.nav.pembimbing')

@section('title', 'Logbook ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/style-dosen.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/absensiKegiatan.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">
        {{-- Header --}}
        <div class="page-header">
            <div class="header-content">
                <nav class="breadcrumb-container mb-2" aria-label="breadcrumb">
                    <a href="{{ route('pembimbing.siswa') }}" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Logbook Siswa</span>
                </nav>
                <h2 class="page-title"><i class="fas fa-book-open text-primary me-2"></i>Logbook: {{ $siswa->nama }}</h2>
                <p class="page-subtitle">{{ $siswa->perusahaan }} &nbsp;|&nbsp; NISN: {{ $siswa->nisn }}</p>
            </div>

            <div class="header-actions">
                <div class="filter-group gap-2">
                    <span class="text-muted small fw-bold me-2">FILTER STATUS:</span>
                    <a href="{{ route('pembimbing.logbook', $siswa->nisn) }}" 
                       class="btn-filter-submit {{ !$status ? '' : 'btn-outline' }}" style="{{ !$status ? '' : 'background:transparent; color:var(--color-primary); border:1px solid var(--color-primary);' }}">Semua</a>
                    <a href="{{ route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending']) }}" 
                       class="btn-filter-submit {{ $status == 'pending' ? '' : 'btn-outline' }}" style="{{ $status == 'pending' ? 'background:var(--color-warning);' : 'background:transparent; color:var(--color-warning); border:1px solid var(--color-warning);' }}">Pending</a>
                    <a href="{{ route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified']) }}" 
                       class="btn-filter-submit {{ $status == 'verified' ? '' : 'btn-outline' }}" style="{{ $status == 'verified' ? 'background:var(--color-green);' : 'background:transparent; color:var(--color-green); border:1px solid var(--color-green);' }}">Approved</a>
                    
                    @if($logbooks->where('status', 'pending')->count() > 0)
                        <form action="{{ route('pembimbing.logbook.validasi-semua', $siswa->nisn) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA logbook pending untuk siswa ini?')">
                            @csrf
                            <button type="submit" class="btn-filter-submit" style="background: var(--color-green);">
                                <i class="fas fa-check-double me-1"></i> Setujui Semua
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="custom-alert alert-success-soft mb-4">
                <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Tabel Logbook --}}
        <div class="content-card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
                <h4 class="card-title"><i class="fas fa-list-alt text-primary me-2"></i>Daftar Kegiatan Siswa</h4>
            </div>
            <div class="custom-table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="20%">Tanggal</th>
                            <th width="40%">Kegiatan / Pekerjaan</th>
                            <th width="15%">Bukti</th>
                            <th width="10%">Status</th>
                            <th width="15%" class="text-center">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                            <tr>
                                <td>
                                    <div class="date-badge">
                                        <span class="day">{{ \Carbon\Carbon::parse($log->tanggal)->format('d') }}</span>
                                        <div class="month-year">
                                            <span class="month">{{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('M') }}</span>
                                            <span class="year">{{ \Carbon\Carbon::parse($log->tanggal)->format('Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="activity-excerpt">
                                        <div class="fw-bold text-dark mb-1">{{ $log->kegiatan }}</div>
                                        @if($log->catatan_pembimbing)
                                            <div class="note-bubble mt-2 {{ $log->status == 'rejected' ? 'alert-danger-soft' : '' }}" style="padding: 0.5rem 0.75rem; font-size: 0.8rem; {{ $log->status == 'rejected' ? 'background:var(--color-red-lt); border-left:3px solid var(--color-red);' : 'background:var(--color-green-lt); border-left:3px solid var(--color-green);' }}">
                                                <i class="fas fa-comment-dots me-1"></i> {{ $log->catatan_pembimbing }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($log->foto)
                                        <a href="{{ asset('storage/' . $log->foto) }}" target="_blank" class="attachment-badge">
                                            <i class="fas fa-image"></i> Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status == 'verified')
                                        <span class="status-badge status-approved">Approved</span>
                                    @elseif($log->status == 'rejected')
                                        <span class="status-badge status-rejected">Rejected</span>
                                    @else
                                        <span class="status-badge status-pending">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($log->status == 'verified')
                                        <span class="status-badge status-approved cursor-pointer btn-open-modal" data-modal="modalLog{{ $log->id_kegiatan }}">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @elseif($log->status == 'rejected')
                                        <span class="status-badge status-rejected cursor-pointer btn-open-modal" data-modal="modalLog{{ $log->id_kegiatan }}">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <button class="btn-action-icon btn-review btn-open-modal" data-modal="modalLog{{ $log->id_kegiatan }}">
                                            <i class="fas fa-clock"></i> Verifikasi
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-book-open empty-icon"></i>
                                        <h4>Belum Ada Kegiatan</h4>
                                        <p class="text-muted">Siswa belum mengisi logbook atau tidak ada data yang sesuai.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logbooks->hasPages())
                <div class="pagination-wrapper">
                    {{ $logbooks->links() }}
                </div>
            @endif
        </div>

        {{-- Section Modal --}}
        @foreach($logbooks as $log)
            <div class="custom-modal-overlay" id="modalLog{{ $log->id_kegiatan }}">
                <div class="custom-modal modal-sm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-clipboard-check text-primary"></i> Verifikasi Logbook</h5>
                        <button type="button" class="modal-close btn-close-modal" data-modal="modalLog{{ $log->id_kegiatan }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('pembimbing.logbook.validasi', $log->id_kegiatan) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="detail-section mb-4">
                                <label class="detail-label">Kegiatan Siswa</label>
                                <div class="detail-content">
                                    {{ $log->kegiatan }}
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="detail-label">Keputusan Verifikasi</label>
                                <div class="validation-radios">
                                    <label class="radio-card btn-radio-approved">
                                        <input type="radio" name="status" value="verified" {{ $log->status == 'verified' ? 'checked' : '' }} required>
                                        <div class="radio-content">
                                            <i class="fas fa-check-circle"></i> SETUJUI
                                        </div>
                                    </label>
                                    <label class="radio-card btn-radio-rejected">
                                        <input type="radio" name="status" value="rejected" {{ $log->status == 'rejected' ? 'checked' : '' }}>
                                        <div class="radio-content">
                                            <i class="fas fa-times-circle"></i> TOLAK
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan / Feedback (Opsional)</label>
                                <textarea name="catatan_pembimbing" class="custom-textarea" rows="3" placeholder="Contoh: Deskripsi sudah bagus.">{{ $log->catatan_pembimbing }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel btn-close-modal" data-modal="modalLog{{ $log->id_kegiatan }}">Batal</button>
                            <button type="submit" class="btn-submit">Simpan Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Logic
            const openBtns = document.querySelectorAll('.btn-open-modal');
            const closeBtns = document.querySelectorAll('.btn-close-modal');
            const overlays = document.querySelectorAll('.custom-modal-overlay');

            openBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            closeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });

            overlays.forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
        });
    </script>
@endpush
@endsection
