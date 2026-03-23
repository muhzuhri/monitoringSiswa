@extends('layouts.nav.pembimbing')

@section('title', 'Logbook ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dashboard-page pembimbing-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/absensiSiswa.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">
        {{-- Header --}}
        <div class="page-header">
            <div class="header-text">
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <a href="{{ route('pembimbing.siswa') }}" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Logbook Siswa</span>
                </nav>
                <h4 class="page-title">Logbook: {{ $siswa->nama }}</h4>
                <p class="page-subtitle">{{ $siswa->perusahaan }} &nbsp;|&nbsp; NISN: {{ $siswa->nisn }}</p>
            </div>

            <div class="filter-wrapper d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
                <span class="text-muted small fw-bold me-2">FILTER STATUS:</span>
                <a href="{{ route('pembimbing.logbook', $siswa->nisn) }}" 
                   class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">Semua</a>
                <a href="{{ route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'pending']) }}" 
                   class="btn btn-sm {{ $status == 'pending' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3">Pending</a>
                <a href="{{ route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'verified']) }}" 
                   class="btn btn-sm {{ $status == 'verified' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3">Approved</a>
                <a href="{{ route('pembimbing.logbook', ['nisn' => $siswa->nisn, 'status' => 'rejected']) }}" 
                   class="btn btn-sm {{ $status == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">Rejected</a>

                @if($logbooks->where('status', 'pending')->count() > 0)
                    <form action="{{ route('pembimbing.logbook.validasi-semua', $siswa->nisn) }}" method="POST" class="ms-md-auto" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA logbook pending untuk siswa ini?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                            <i class="fas fa-check-double me-1"></i> Setujui Semua
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px;">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        {{-- Tabel Logbook --}}
        <div class="ui-card">
            <div class="card-head">
                <h6 class="card-title">Daftar Kegiatan Siswa</h6>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Tanggal</th>
                            <th>Kegiatan / Pekerjaan</th>
                            <th style="width: 120px;">Bukti</th>
                            <th style="width: 150px;">Status</th>
                            <th style="width: 150px;">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logbooks as $log)
                            <tr>
                                <td class="td-date">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('l, d F Y') }}
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">{{ Str::limit($log->kegiatan, 100) }}</div>
                                    @if($log->catatan_pembimbing)
                                        <div class="small p-2 rounded {{ $log->status == 'rejected' ? 'bg-danger-light text-danger' : 'bg-success-light text-success' }}" style="border-left: 3px solid currentColor;">
                                            <i class="fas fa-comment-dots me-1"></i> {{ $log->catatan_pembimbing }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($log->foto)
                                        <a href="{{ asset('storage/' . $log->foto) }}" target="_blank" class="photo-group">
                                            <img src="{{ asset('storage/' . $log->foto) }}" class="photo-thumbnail" alt="Bukti">
                                        </a>
                                    @else
                                        <span class="text-muted small">No Photo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status == 'verified')
                                        <span class="status-badge status-hadir">Approved</span>
                                    @elseif($log->status == 'rejected')
                                        <span class="status-badge status-alpa">Rejected</span>
                                    @else
                                        <span class="status-badge status-izin">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->status == 'verified')
                                        <span class="badge bg-success-light text-success fw-bold px-3 py-2 rounded-pill shadow-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $log->id_kegiatan }}">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @elseif($log->status == 'rejected')
                                        <span class="badge bg-danger-light text-danger fw-bold px-3 py-2 rounded-pill shadow-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $log->id_kegiatan }}">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning fw-bold px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $log->id_kegiatan }}">
                                            <i class="fas fa-clock me-1"></i> Verifikasi
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row text-center py-5">
                                    <i class="fas fa-book-open fs-1 text-muted opacity-25 mb-3"></i>
                                    <div class="text-muted">Belum ada catatan kegiatan yang sesuai.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logbooks->hasPages())
                <div class="table-pagination">
                    {{ $logbooks->links() }}
                </div>
            @endif
        </div>

        {{-- Modals Section --}}
        @foreach($logbooks as $log)
            <!-- Modal Verifikasi -->
            <div class="modal fade" id="modalVerifikasi{{ $log->id_kegiatan }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                        <div class="modal-header border-0 pb-0 pe-4 pt-4">
                            <h5 class="fw-bold"><i class="fas fa-clipboard-check text-primary me-2"></i> Verifikasi Logbook</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('pembimbing.logbook.validasi', $log->id_kegiatan) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-4 p-3 bg-light rounded-4">
                                    <div class="small text-muted mb-1">Kegiatan Siswa:</div>
                                    <div class="fw-bold text-dark">{{ $log->kegiatan }}</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark mb-3">Keputusan Verifikasi</label>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="status" id="approve{{ $log->id_kegiatan }}" value="verified" {{ $log->status == 'verified' ? 'checked' : '' }} required>
                                            <label class="btn btn-outline-success w-100 py-3 rounded-4 fw-bold" for="approve{{ $log->id_kegiatan }}">
                                                <i class="fas fa-check-circle me-1"></i> SETUJUI
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="status" id="reject{{ $log->id_kegiatan }}" value="rejected" {{ $log->status == 'rejected' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold" for="reject{{ $log->id_kegiatan }}">
                                                <i class="fas fa-times-circle me-1"></i> TOLAK
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold text-dark mb-2">Catatan / Feedback (Opsional)</label>
                                    <textarea name="catatan_pembimbing" class="form-control bg-light border-0 p-3" rows="3" placeholder="Contoh: Deskripsi sudah bagus, lanjutkan." style="border-radius: 12px; resize: none;">{{ $log->catatan_pembimbing }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Simpan Verifikasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
