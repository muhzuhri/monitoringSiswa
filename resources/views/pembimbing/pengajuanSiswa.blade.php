@extends('layouts.nav.pembimbing')

@section('title', 'Persetujuan Pengajuan Siswa - SIM Magang')
@section('body-class', 'pengajuan-page pembimbing-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/pengajuanSiswa.css') }}">
@endpush

@section('body')
<div class="page-body">
    <div class="main-container">
        
        <div class="page-header animate-fade-in">
            <div class="header-content">
                <h3 class="header-title">Persetujuan Pengajuan<span class="dot-primary">.</span></h3>
                <p class="header-subtitle">Kelola request lupa absensi atau kegiatan dari siswa binaan Anda.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="status-alert alert-success animate-fade-in" role="alert">
                <i class="fas fa-check-circle alert-icon"></i>
                <span class="alert-message">{{ session('success') }}</span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="status-alert alert-danger animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <span class="alert-message">{{ session('error') }}</span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="filter-card animate-fade-in">
            <div class="filter-body">
                <form action="{{ route('pembimbing.pengajuan') }}" method="GET" class="filter-form">
                    <div class="filter-group">
                        <label class="filter-label">Filter Status</label>
                        <select name="status" class="premium-select filter-glow" onchange="this.form.submit()">
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                            <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Pengajuan</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-card animate-fade-in">
            <div class="table-header">
                <div class="header-title-wrapper">
                    <div class="icon-circle-premium">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h5 class="card-title-premium">Daftar Pengajuan</h5>
                </div>
            </div>
            <div class="table-body">
                @if($pengajuans->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon-wrapper">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <h6 class="empty-title">Tidak ada data</h6>
                        <p class="empty-subtitle">Belum ada pengajuan dengan filter yang dipilih.</p>
                    </div>
                @else
                    <div class="scrollable-table">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th class="col-student">Siswa & Waktu</th>
                                    <th class="col-info">Informasi Lupa</th>
                                    <th class="col-reason">Alasan Keterlambatan</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-action">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengajuans as $p)
                                    <tr class="table-row">
                                        <!-- Siswa & Waktu -->
                                        <td class="cell-student">
                                            <div class="student-profile">
                                                <div class="avatar-wrapper">
                                                    {{ strtoupper(substr($p->siswa->nama, 0, 1)) }}
                                                </div>
                                                <div class="student-info">
                                                    <div class="student-name">{{ $p->siswa->nama }}</div>
                                                    <div class="student-nisn">{{ $p->siswa->nisn }}</div>
                                                </div>
                                            </div>
                                            <div class="timestamp-info">
                                                <i class="fas fa-clock"></i> Diajukan: {{ $p->created_at->translatedFormat('d M, H:i') }}
                                            </div>
                                        </td>

                                        <!-- Informasi Lupa -->
                                        <td class="cell-info">
                                            <div class="lupa-summary">
                                                <div class="main-date">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</div>
                                                <span class="badge-jenis badge-{{ $p->jenis }}">
                                                    {{ ucfirst($p->jenis) }}
                                                </span>
                                            </div>
                                            @if($p->jenis == 'absensi')
                                                <div class="time-mini-card">
                                                    <div class="time-mini-item border-right-white">
                                                        <div class="time-mini-label">MASUK</div>
                                                        <div class="time-mini-value">{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '--:--' }}</div>
                                                    </div>
                                                    <div class="time-mini-item">
                                                        <div class="time-mini-label">PULANG</div>
                                                        <div class="time-mini-value">{{ $p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '--:--' }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="detail-text" title="{{ $p->deskripsi }}">
                                                    "{{ $p->deskripsi }}"
                                                </div>
                                            @endif
                                            
                                            @if($p->bukti)
                                                <a href="{{ asset('storage/'.$p->bukti) }}" target="_blank" class="attachment-link">
                                                    <i class="fas fa-paperclip"></i> Lihat Bukti
                                                </a>
                                            @endif
                                        </td>

                                        <!-- Alasan -->
                                        <td class="cell-reason">
                                            <div class="reason-box">
                                                {{ $p->alasan_terlambat }}
                                            </div>
                                        </td>

                                        <!-- Status -->
                                        <td class="cell-status">
                                            @if($p->status == 'pending')
                                                <div class="status-pill state-pending">
                                                    <i class="fas fa-hourglass-half"></i>Pending
                                                </div>
                                            @elseif($p->status == 'valid')
                                                <div class="status-pill state-valid">
                                                    <i class="fas fa-check-circle"></i>Valid
                                                </div>
                                            @else
                                                <div class="status-pill state-rejected">
                                                    <i class="fas fa-times-circle"></i>Ditolak
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="cell-action">
                                            @if($p->status == 'pending')
                                                <div class="action-group">
                                                    <form action="{{ route('pembimbing.pengajuan.update', $p->id_pengajuan) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn-icon-success" onclick="return confirm('Setujui pengajuan ini? Data akan otomatis masuk ke sistem.')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('pembimbing.pengajuan.update', $p->id_pengajuan) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="btn-icon-danger" onclick="return confirm('Tolak pengajuan ini?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-completed">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            
            <div class="table-footer">
                @if(method_exists($pengajuans, 'links'))
                    {{ $pengajuans->links() }}
                @endif
            </div>
            
        </div>
    </div>
</div>
@endsection