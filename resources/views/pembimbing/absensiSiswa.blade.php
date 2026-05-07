@extends('layouts.nav.pembimbing')

@section('title', 'Rekap Absensi ' . $siswa->nama . ' - SIM Magang')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/absensisiswa.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">

        {{-- Header --}}
        <div class="page-header">
            <div class="header-content">
                <nav class="breadcrumb-container mb-2" aria-label="breadcrumb">
                    <a href="{{ route('pembimbing.siswa') }}" class="breadcrumb-link">Daftar Siswa</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h2 class="page-title"><i class="fas fa-calendar-check text-primary me-2"></i>Rekap Absensi: {{ $siswa->nama }}</h2>
                <p class="page-subtitle">{{ $siswa->perusahaan }} &nbsp;|&nbsp; NISN: {{ $siswa->nisn }}</p>
            </div>

            <div class="header-actions">
                <div class="filter-group gap-2">
                    <span class="text-muted small fw-bold me-2">VERIFIKASI:</span>
                    <a href="{{ route('pembimbing.absensi', $siswa->nisn) }}" 
                       class="btn-filter-submit {{ !$statusVerifikasi ? '' : 'btn-outline' }}" style="{{ !$statusVerifikasi ? '' : 'background:transparent; color:var(--color-primary); border:1px solid var(--color-primary);' }}">Semua</a>
                    <a href="{{ route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'rejected']) }}" 
                       class="btn-filter-submit {{ $statusVerifikasi == 'rejected' ? '' : 'btn-outline' }}" style="{{ $statusVerifikasi == 'rejected' ? 'background:var(--color-warning);' : 'background:transparent; color:var(--color-warning); border:1px solid var(--color-warning);' }}">Rejected</a>
                    <a href="{{ route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'verified']) }}" 
                       class="btn-filter-submit {{ $statusVerifikasi == 'verified' ? '' : 'btn-outline' }}" style="{{ $statusVerifikasi == 'verified' ? 'background:var(--color-green);' : 'background:transparent; color:var(--color-green); border:1px solid var(--color-green);' }}">Verified</a>
                    
                    @if($absensis->where('verifikasi', 'pending')->count() > 0)
                        <form action="{{ route('pembimbing.absensi.validasi-semua', $siswa->nisn) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA absensi pending untuk siswa ini?')">
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

        @if(session('info'))
            <div class="custom-alert alert-info-soft mb-4" style="background:var(--color-blue-lt); color:var(--color-blue); border-left:4px solid var(--color-blue); padding:1rem 1.25rem; border-radius:var(--radius-sm); display:flex; align-items:flex-start; gap:.9rem;">
                <div class="alert-icon"><i class="fas fa-info-circle"></i></div>
                <div class="alert-content">{{ session('info') }}</div>
            </div>
        @endif
        
        {{-- Tabel Riwayat Presensi --}}
        <div class="content-card">
            <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);">
                <h4 class="card-title"><i class="fas fa-history text-primary me-2"></i>Riwayat Presensi</h4>
            </div>
            <div class="custom-table-wrapper">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th width="20%">Tanggal</th>
                            <th width="15%">Status</th>
                            <th width="15%">Jam Masuk</th>
                            <th width="15%">Jam Pulang</th>
                            <th width="15%">Bukti Foto</th>
                            <th width="20%" class="text-center">Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensis as $a)
                            <tr>
                                <td>
                                    <div class="date-badge">
                                        <span class="day">{{ \Carbon\Carbon::parse($a->tanggal)->format('d') }}</span>
                                        <div class="month-year">
                                            <span class="month">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('M') }}</span>
                                            <span class="year">{{ \Carbon\Carbon::parse($a->tanggal)->format('Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($a->status == 'hadir')
                                        <span class="status-badge status-approved">Hadir</span>
                                    @elseif($a->status == 'terlambat')
                                        <span class="status-badge status-pending" style="background:var(--color-orange-lt); color:var(--color-orange);">Terlambat</span>
                                    @elseif($a->status == 'izin' || $a->status == 'sakit')
                                        <span class="status-badge status-pending">{{ ucfirst($a->status) }}</span>
                                    @else
                                        <span class="status-badge status-rejected">Alpa</span>
                                    @endif
                                </td>
                                <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i') : '-' }}</td>
                                <td>{{ $a->jam_pulang ? \Carbon\Carbon::parse($a->jam_pulang)->format('H:i') : '-' }}</td>
                                 <td>
                                    <div class="action-buttons">
                                        @if($a->foto_masuk)
                                            <a href="{{ asset('storage/' . $a->foto_masuk) }}" target="_blank" class="attachment-badge" title="Foto Masuk">
                                                <i class="fas fa-image"></i> Masuk
                                            </a>
                                        @endif
                                        @if($a->foto_pulang)
                                            <a href="{{ asset('storage/' . $a->foto_pulang) }}" target="_blank" class="attachment-badge" title="Foto Pulang">
                                                <i class="fas fa-image"></i> Pulang
                                            </a>
                                        @endif
                                        @if(!$a->foto_masuk && !$a->foto_pulang)
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($a->verifikasi == 'verified')
                                        <span class="status-badge status-approved cursor-pointer btn-open-modal" data-modal="modalVer{{ $a->id_absensi }}">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @elseif($a->verifikasi == 'rejected')
                                        <span class="status-badge status-rejected cursor-pointer btn-open-modal" data-modal="modalVer{{ $a->id_absensi }}">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <button class="btn-action-icon btn-review btn-open-modal" data-modal="modalVer{{ $a->id_absensi }}">
                                            <i class="fas fa-clock"></i> Verifikasi
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times empty-icon"></i>
                                        <h4>Tidak Ada Data</h4>
                                        <p class="text-muted">Belum ada riwayat presensi yang tersedia.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination removed since history is now a full collection --}}
        </div>

        {{-- Section Modal --}}
        @foreach($absensis as $a)
            @php 
                $isDynamic = isset($a->is_dynamic) && $a->is_dynamic;
            @endphp
            <div class="custom-modal-overlay" id="modalVer{{ $a->id_absensi }}">
                <div class="custom-modal modal-sm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-calendar-check text-primary"></i> Verifikasi Absensi</h5>
                        <button type="button" class="modal-close btn-close-modal" data-modal="modalVer{{ $a->id_absensi }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('pembimbing.absensi.validasi', $a->id_absensi) }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_dynamic" value="{{ $isDynamic ? 1 : 0 }}">
                        <input type="hidden" name="siswa_nisn" value="{{ $siswa->nisn }}">
                        <div class="modal-body">
                            <div class="detail-section mb-4">
                                <div class="detail-content" style="background:var(--color-primary-lt); border-color:var(--color-primary); display:flex; align-items:center; gap:1rem;">
                                    <i class="fas fa-user-clock fs-3 text-primary"></i>
                                    <div>
                                        <div class="small fw-bold">{{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}</div>
                                        <div class="small text-muted">Status: {{ ucfirst($a->status) }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label class="detail-label">Keputusan Verifikasi</label>
                                <div class="validation-radios">
                                    <label class="radio-card btn-radio-approved">
                                        <input type="radio" name="status" value="verified" {{ $a->verifikasi == 'verified' ? 'checked' : '' }} required>
                                        <div class="radio-content">
                                            <i class="fas fa-check-circle"></i> SETUJUI
                                        </div>
                                    </label>
                                    <label class="radio-card btn-radio-rejected">
                                        <input type="radio" name="status" value="rejected" {{ $a->verifikasi == 'rejected' ? 'checked' : '' }}>
                                        <div class="radio-content">
                                            <i class="fas fa-times-circle"></i> TOLAK
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="keterangan" class="custom-textarea" rows="3" placeholder="Contoh: Bukti foto kurang jelas.">{{ $a->keterangan }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel btn-close-modal" data-modal="modalVer{{ $a->id_absensi }}">Batal</button>
                            <button type="submit" class="btn-submit">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

@push('scripts')
    <script src="{{ asset('assets/js/pembimbing/absensiSiswa.js') }}"></script>
@endpush
@endsection
