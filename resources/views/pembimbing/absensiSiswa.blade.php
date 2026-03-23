@extends('layouts.nav.pembimbing')

@section('title', 'Rekap Absensi ' . $siswa->nama . ' - SIM Magang')
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
                    <span class="breadcrumb-current">Rekap Absensi</span>
                </nav>
                <h4 class="page-title">Rekap Absensi: {{ $siswa->nama }}</h4>
                <p class="page-subtitle">{{ $siswa->perusahaan }} &nbsp;|&nbsp; NISN: {{ $siswa->nisn }}</p>
            </div>

            <div class="filter-wrapper d-flex align-items-center flex-wrap gap-2 mt-3 mt-md-0">
                <span class="text-muted small fw-bold me-2">VERIFIKASI:</span>
                <a href="{{ route('pembimbing.absensi', $siswa->nisn) }}" 
                   class="btn btn-sm {{ !$statusVerifikasi ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">Semua</a>
                <a href="{{ route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'pending']) }}" 
                   class="btn btn-sm {{ $statusVerifikasi == 'pending' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3">Pending</a>
                <a href="{{ route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'verified']) }}" 
                   class="btn btn-sm {{ $statusVerifikasi == 'verified' ? 'btn-success' : 'btn-outline-success' }} rounded-pill px-3">Verified</a>
                <a href="{{ route('pembimbing.absensi', ['nisn' => $siswa->nisn, 'status_verifikasi' => 'rejected']) }}" 
                   class="btn btn-sm {{ $statusVerifikasi == 'rejected' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">Rejected</a>

                @if($absensis->where('verifikasi', 'pending')->count() > 0)
                    <form action="{{ route('pembimbing.absensi.validasi-semua', $siswa->nisn) }}" method="POST" class="ms-md-auto" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui SEMUA absensi pending untuk siswa ini?')">
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

        @if(session('info'))
            <div class="alert alert-info border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 12px;">
                <i class="fas fa-info-circle me-3 fs-4"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif

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
                            <th>Verifikasi</th>
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
                                <td>
                                    @if($a->verifikasi == 'verified')
                                        <span class="badge bg-success-light text-success fw-bold px-3 py-2 rounded-pill shadow-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalVerifikasiAbsen{{ $a->id_absensi }}">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                    @elseif($a->verifikasi == 'rejected')
                                        <span class="badge bg-danger-light text-danger fw-bold px-3 py-2 rounded-pill shadow-sm" style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalVerifikasiAbsen{{ $a->id_absensi }}">
                                            <i class="fas fa-times-circle"></i> Rejected
                                        </span>
                                    @else
                                        <button class="btn btn-sm btn-outline-warning fw-bold px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalVerifikasiAbsen{{ $a->id_absensi }}">
                                            <i class="fas fa-clock me-1"></i> Verifikasi
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-row text-center py-5">
                                    <i class="fas fa-calendar-times fs-1 text-muted opacity-25 mb-3"></i>
                                    <div class="text-muted">Belum ada riwayat presensi yang sesuai.</div>
                                </td>
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

        {{-- Section Modal (Diletakkan di luar table agar tidak terpengaruh overflow/layout table) --}}
        @foreach($absensis as $a)
            <!-- Modal Verifikasi Absensi -->
            <div class="modal fade" id="modalVerifikasiAbsen{{ $a->id_absensi }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
                        <div class="modal-header border-0 pb-0 pe-4 pt-4">
                            <h5 class="fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i> Verifikasi Absensi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('pembimbing.absensi.validasi', $a->id_absensi) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-4">
                                    <div class="text-primary me-3 fs-3">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div>
                                        <div class="small text-muted">Tanggal: {{ \Carbon\Carbon::parse($a->tanggal)->translatedFormat('d F Y') }}</div>
                                        <div class="fw-bold">Status: {{ ucfirst($a->status) }}</div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark mb-3">Keputusan Verifikasi</label>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="status" id="approveAbs{{ $a->id_absensi }}" value="verified" {{ $a->verifikasi == 'verified' ? 'checked' : '' }} required>
                                            <label class="btn btn-outline-success w-100 py-3 rounded-4 fw-bold" for="approveAbs{{ $a->id_absensi }}">
                                                <i class="fas fa-check-circle me-1"></i> SETUJUI
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="status" id="rejectAbs{{ $a->id_absensi }}" value="rejected" {{ $a->verifikasi == 'rejected' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger w-100 py-3 rounded-4 fw-bold" for="rejectAbs{{ $a->id_absensi }}">
                                                <i class="fas fa-times-circle me-1"></i> TOLAK
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold text-dark mb-2">Keterangan / Catatan (Opsional)</label>
                                    <textarea name="keterangan" class="form-control bg-light border-0 p-3" rows="3" placeholder="Contoh: Bukti foto kurang jelas." style="border-radius: 12px; resize: none;">{{ $a->keterangan }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
