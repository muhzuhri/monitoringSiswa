@extends('layouts.nav.guru')

@section('title', 'Daftar Siswa Bimbingan - SIM Magang')
@section('body-class', 'guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/daftarSiswa.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">

        {{-- Header Halaman --}}
        <div class="page-header">
            <div class="header-text">
                <h4 class="page-title">Siswa Bimbingan</h4>
                <p class="page-subtitle">Kelola dan pantau seluruh siswa magang di bawah bimbingan Anda.</p>
            </div>
            <div class="search-wrapper">
                <form id="headerSearchForm" class="search-form">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="headerSearchInput" value="{{ $search }}" class="search-input"
                        placeholder="Cari Nama, NISN, atau Perusahaan..." autocomplete="off">
                </form>
            </div>
        </div>

        {{-- Tab Navigasi --}}
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="bimbingan-tab" data-bs-toggle="pill" data-bs-target="#bimbingan"
                    type="button" role="tab">
                    <i class="fas fa-users"></i>
                    <span>Siswa Bimbingan ({{ $siswas->count() }})</span>
                </button>
                <button class="tab-button" id="search-tab" data-bs-toggle="pill" data-bs-target="#search-students"
                    type="button" role="tab">
                    <i class="fas fa-search-plus"></i>
                    <span>Cari Siswa ({{ $availableSiswas->count() }})</span>
                </button>
                <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#riwayat-history"
                    type="button" role="tab">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Siswa ({{ $riwayatSiswas->count() }}{{ $periodeId ? ' &bull; filtered' : '' }})</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="siswaTabContent">
            {{-- Tab Siswa Bimbingan --}}
            <div class="tab-pane fade show active" id="bimbingan" role="tabpanel">
                {{-- View Mode Toggle --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="view-mode-wrapper" style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
                        <button class="view-mode-btn active" data-view="grouped" data-target="bimbingan">
                            <i class="fas fa-th-large me-1"></i> Perkelompok
                        </button>
                        <button class="view-mode-btn" data-view="flat" data-target="bimbingan">
                            <i class="fas fa-list me-1"></i> Seluruh Siswa
                        </button>
                    </div>
                </div>

                {{-- Grouped View (Cards) --}}
                <div class="view-container" id="bimbingan-grouped-view">
                    <div class="siswa-grid">
                        @forelse($groupedSiswas as $groupKey => $g)
                            <div class="student-card {{ $g['is_group'] ? 'group-card' : '' }}">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        @if($g['is_group'])
                                            <i class="fas fa-layer-group"></i>
                                        @else
                                            {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="student-meta">
                                        <h6 class="student-name">
                                            {{ $g['leader']->nama }}
                                            @if($g['is_group'])
                                                <span class="badge bg-info-light text-info-dark ms-1" style="font-size: 0.65rem; border-radius: 50px;">{{ $g['members']->count() }} Anggota</span>
                                            @endif
                                        </h6>
                                        <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                        <div class="mt-2 text-start">
                                            @if($g['leader']->status === 'selesai')
                                                <span class="badge bg-secondary text-white" style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                    <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                                </span>
                                            @else
                                                <span class="badge bg-success text-white" style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                    <i class="fas fa-check-circle me-1"></i> AKTIF
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="status-wrapper">
                                        @php
                                            $hadirAll = $g['members']->every(fn($m) => $m->absen_hari_ini);
                                            $hadirCount = $g['members']->filter(fn($m) => $m->absen_hari_ini)->count();
                                        @endphp
                                        @if ($hadirAll)
                                            <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                                        @elseif($hadirCount > 0)
                                            <span class="status-badge status-warning"><i class="fas fa-user-clock"></i> {{ $hadirCount }}/{{ $g['members']->count() }}</span>
                                        @else
                                            <span class="status-badge status-absen"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                        <span class="info-value">{{ $g['leader']->sekolah }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                        <span class="info-value">{{ $g['leader']->perusahaan }}</span>
                                    </div>
                                </div>
                                <div class="action-grid">
                                    @if($g['is_group'])
                                        <button class="btn-action btn-detail-group btn-show-members" 
                                                data-name="{{ $g['leader']->nama }}"
                                                data-members="{{ $g['members']->toJson() }}"
                                                data-logbook-route="{{ route('guru.logbook', ['nisn' => ':nisn']) }}"
                                                data-absensi-route="{{ route('guru.absensi', ['nisn' => ':nisn']) }}"
                                                data-logbook-download="{{ route('guru.rekap.jurnal', ['nisn' => ':nisn']) }}"
                                                data-absensi-download="{{ route('guru.rekap.absensi', ['nisn' => ':nisn']) }}">
                                            <i class="fas fa-search"></i> Pantau Kelompok
                                        </button>
                                    @endif
                                    <div class="action-row">
                                        <a href="{{ route('guru.logbook', $g['leader']->nisn) }}" class="btn-action btn-logbook">
                                            <i class="fas fa-book"></i> Logbook
                                        </a>
                                        <a href="{{ route('guru.absensi', $g['leader']->nisn) }}" class="btn-action btn-absensi">
                                            <i class="fas fa-calendar-check"></i> Absensi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Belum ada siswa bimbingan.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Flat View (Table) --}}
                <div class="view-container d-none" id="bimbingan-flat-view">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Identitas Siswa</th>
                                        <th>Status Hari Ini</th>
                                        <th>Sekolah / Perusahaan</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswas as $idx => $s)
                                        <tr class="active-flat-row">
                                            <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="td-siswa-name">{{ $s->nama }}</div>
                                                <div class="td-siswa-nisn"><i class="fas fa-id-card-alt me-1 opacity-50"></i> {{ $s->nisn }}</div>
                                            </td>
                                            <td>
                                                @if($s->absen_hari_ini)
                                                    <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                                                @else
                                                    <span class="status-badge status-absen"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school small"><i class="fas fa-university me-1"></i> {{ $s->sekolah }}</span>
                                                    <small class="text-muted"><i class="fas fa-building me-1"></i> {{ $s->perusahaan }}</small>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('guru.logbook', $s->nisn) }}" class="btn-small" style="background: var(--primary-light); color: var(--primary);" title="Logbook">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                    <a href="{{ route('guru.absensi', $s->nisn) }}" class="btn-small" style="background: var(--warning-light); color: #92400e;" title="Absensi">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center p-4">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="noResultsBimbingan" class="empty-state" style="display: none; width: 100%;">Tidak ada siswa bimbingan yang cocok dengan pencarian.</div>
            </div>

            {{-- Tab Cari Siswa --}}
            <div class="tab-pane fade" id="search-students" role="tabpanel">
                <div class="filter-section shadow-sm">
                    <form action="{{ route('guru.siswa') }}" method="GET" class="row align-items-end g-3" id="npsnSearchForm">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div class="col-md-7">
                            <label class="filter-label"><i class="fas fa-filter me-2 text-primary"></i> Filter Berdasarkan NPSN Sekolah</label>
                            <div class="d-flex">
                                <div class="input-group-modern flex-grow-1">
                                    <span class="input-group-text-modern"><i class="fas fa-school"></i></span>
                                    <input type="text" name="npsn" id="npsnInput" value="{{ $npsn }}" class="form-control form-control-modern"
                                        placeholder="Masukkan NPSN Sekolah (Contoh: 10203040)..." autocomplete="off">
                                </div>
                                @if($npsn)
                                    <a href="{{ route('guru.siswa', ['search' => $search]) }}"
                                        class="btn btn-reset-modern">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="ui-card">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Identitas Siswa / Ketua</th>
                                    <th>Asal Sekolah</th>
                                    <th>Tipe Magang</th>
                                    <th>Kapasitas</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedAvailable as $asKey => $ga)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="td-siswa-name">{{ $ga['leader']->nama }}</div>
                                            <div class="td-siswa-nisn"><i class="fas fa-id-card-alt me-1 opacity-50"></i> {{ $ga['leader']->nisn }}</div>
                                        </td>
                                        <td><span class="badge-school"><i class="fas fa-university me-1"></i> {{ $ga['leader']->sekolah }}</span></td>
                                        <td>
                                            @if($ga['is_group'])
                                                <span class="badge bg-info-light text-info-dark px-3 py-2" style="border-radius: 50px;">
                                                    <i class="fas fa-layer-group me-1"></i> Kelompok
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-light text-muted px-3 py-2" style="border-radius: 50px; background: #f1f5f9;">
                                                    <i class="fas fa-user me-1"></i> Individu
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ga['is_group'])
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-stack me-2">
                                                        @foreach($ga['members']->take(3) as $member)
                                                            <div class="avatar-mini" title="{{ $member->nama }}">{{ strtoupper(substr($member->nama,0,1)) }}</div>
                                                        @endforeach
                                                    </div>
                                                    <small class="fw-bold text-primary">{{ $ga['members']->count() }} Orang</small>
                                                </div>
                                            @else
                                                <small class="text-muted">1 Orang</small>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('guru.siswa.claim', $ga['leader']->nisn) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-small btn-accept">
                                                    <i class="fas fa-plus-circle"></i> Pilih Jadi Bimbingan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-row text-center p-5">
                                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                            <p>Tidak ada siswa tersedia untuk kriteria ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab Riwayat Siswa --}}
            <div class="tab-pane fade" id="riwayat-history" role="tabpanel">

                {{-- View Mode Toggle --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="view-mode-wrapper" style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
                        <button class="view-mode-btn" data-view="grouped" data-target="history">
                            <i class="fas fa-th-large me-1"></i> Perkelompok
                        </button>
                        <button class="view-mode-btn active" data-view="flat" data-target="history">
                            <i class="fas fa-list me-1"></i> Seluruh Siswa
                        </button>
                    </div>
                </div>

                {{-- Filter Periode --}}
                <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap" style="background:#ffffff; border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                    <div class="filter-label" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em; margin-bottom: 0;">
                        <i class="fas fa-filter"></i>
                        <span>Filter Periode:</span>
                    </div>
                    <form id="periodeFilterForm" method="GET" action="{{ route('guru.siswa') }}" class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0" style="gap:0.75rem;">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <input type="hidden" name="tab" value="history">
                        <select name="periode" id="periodeSelect" class="form-select" style="min-width:220px; border-radius:var(--radius-sm); font-weight:600; font-size:0.85rem; border:1.5px solid var(--border); cursor:pointer;" onchange="document.getElementById('periodeFilterForm').submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeOptions as $opt)
                                <option value="{{ $opt->id_tahun_ajaran }}"
                                    {{ $periodeId == $opt->id_tahun_ajaran ? 'selected' : '' }}>
                                    {{ $opt->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        @if($periodeId)
                            <a href="{{ route('guru.siswa', array_filter(['search' => $search, 'tab' => 'history'])) }}"
                               class="btn btn-outline-danger btn-sm" style="border-radius:var(--radius-sm); font-weight:700; padding:0.45rem 1rem;" title="Hapus Filter">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        @endif
                    </form>
                    @if($periodeId)
                        @php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); @endphp
                        @if($selectedPeriode)
                            <span class="badge bg-primary ms-auto mt-2 mt-md-0" style="padding:0.5rem 1rem; border-radius:99px; font-weight:800; font-size:0.75rem;">
                                <i class="fas fa-calendar-alt me-1"></i>
                                {{ $selectedPeriode->tahun_ajaran }}
                            </span>
                        @endif
                    @endif
                </div>

                {{-- Grouped View (Cards) - Hidden by default for History --}}
                <div class="view-container d-none" id="history-grouped-view">
                    <div class="siswa-grid">
                        @forelse($groupedRiwayat as $groupKey => $g)
                            <div class="student-card group-card">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        @if($g['is_group'])
                                            <i class="fas fa-layer-group"></i>
                                        @else
                                            {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="student-meta">
                                        <h6 class="student-name">
                                            {{ $g['leader']->nama }}
                                            @if($g['is_group'])
                                                <span class="badge bg-info-light text-info-dark ms-1" style="font-size: 0.65rem; border-radius: 50px;">{{ $g['members']->count() }} Anggota</span>
                                            @endif
                                        </h6>
                                        <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                        <div class="mt-2 text-start">
                                            <span class="badge bg-secondary text-white" style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                            </span>
                                        </div>
                                    </div>
                                    <div class="status-wrapper">
                                        <span class="status-badge status-hadir" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
                                            <i class="fas fa-archive"></i> Arsip
                                        </span>
                                    </div>
                                </div>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                        <span class="info-value">{{ $g['leader']->sekolah }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                        <span class="info-value">{{ $g['leader']->perusahaan }}</span>
                                    </div>
                                </div>
                                <div class="action-grid">
                                    @if($g['is_group'])
                                        <button class="btn-action btn-detail-group btn-show-members" 
                                                data-name="{{ $g['leader']->nama }}"
                                                data-members="{{ $g['members']->toJson() }}"
                                                data-context="history"
                                                data-logbook-route="{{ route('guru.logbook', ['nisn' => ':nisn']) }}"
                                                data-absensi-route="{{ route('guru.absensi', ['nisn' => ':nisn']) }}"
                                                data-logbook-download="{{ route('guru.rekap.jurnal', ['nisn' => ':nisn']) }}"
                                                data-absensi-download="{{ route('guru.rekap.absensi', ['nisn' => ':nisn']) }}">
                                            <i class="fas fa-users"></i> Anggota Kelompok
                                        </button>
                                    @endif
                                    <div class="action-row">
                                        <button class="btn-action btn-absensi btn-preview-pdf" data-url="{{ route('guru.rekap.kelompok', $g['leader']->nisn) }}" style="width: 100%;">
                                            <i class="fas fa-file-signature"></i> Rekap Absensi Kelompok
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">Belum ada riwayat siswa.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Flat View (Table) --}}
                <div class="view-container" id="history-flat-view">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">No</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah / Perusahaan</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="riwayatTableBody">
                                    @forelse($riwayatSiswas as $index => $siswa)
                                        <tr class="history-row">
                                            <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="td-siswa-name fw-bold text-dark">{{ $siswa->nama }}</div>
                                                <div class="td-siswa-nisn small text-muted"><i class="fas fa-id-card-alt me-1 opacity-50"></i> {{ $siswa->nisn }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school bg-light text-dark px-2 py-1 rounded small" style="border: 1px solid #eee; font-size: 0.75rem;"><i class="fas fa-university me-1 text-primary"></i> {{ $siswa->sekolah }}</span>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-building me-1"></i> {{ $siswa->perusahaan }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted" style="line-height: 1.4;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-calendar-alt me-2 text-primary opacity-75" style="width: 14px;"></i>
                                                        <span style="font-size: 0.75rem;">{{ $siswa->tgl_mulai_magang ? \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->translatedFormat('d M Y') : '-' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-flag-checkered me-2 text-primary opacity-75" style="width: 14px;"></i>
                                                        <span style="font-size: 0.75rem;">{{ $siswa->tgl_selesai_magang ? \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->translatedFormat('d M Y') : '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Jurnal" data-url="{{ route('guru.rekap.jurnal', $siswa->nisn) }}"
                                                       style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; padding: 6px 10px; border-radius: 8px;">
                                                        <i class="fas fa-book"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Absensi" data-url="{{ route('guru.rekap.absensi', $siswa->nisn) }}"
                                                       style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 6px 10px; border-radius: 8px;">
                                                        <i class="fas fa-file-signature"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-row text-center p-5">
                                                <i class="fas fa-history fa-5x mb-3 text-muted opacity-25"></i>
                                                <p class="text-muted">Belum ada riwayat siswa binaan yang selesai.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- End of page-wrapper --}}

    {{-- Modal Anggota Kelompok --}}
    <div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fas fa-users-viewfinder me-3 text-primary"></i> <span id="modalGroupName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-4 py-3">
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Klik Logbook atau Absensi untuk melihat detail masing-masing siswa.</p>
                    </div>
                    <div class="table-responsive px-4 pb-4">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Identitas Siswa</th>
                                    <th class="text-center">Status Hari Ini</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modalGroupBody">
                                {{-- Rows via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pe-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>
                            <div class="fw-bold">Berhasil!</div>
                            <div class="small opacity-75">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-warning border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-4 text-dark"></i>
                        <div class="text-dark">
                            <div class="fw-bold">Peringatan</div>
                            <div class="small opacity-75">{{ session('warning') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Preview PDF -->
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
            <div class="modal-content border-0 shadow-2xl" style="border-radius: 20px; overflow: hidden; background: #f8fafc;">
                <div class="modal-header bg-white text-dark border-bottom py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger-light p-2 rounded-3 me-3">
                            <i class="fas fa-file-pdf text-danger fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" id="previewPdfModalLabel">Pratinjau Laporan</h6>
                            <small class="text-muted" style="font-size: 0.7rem;">Gunakan tombol di atas PDF untuk kontrol lebih lanjut</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a id="downloadPdfBtn" href="#" class="btn btn-sm btn-primary px-3 rounded-pill">
                            <i class="fas fa-download me-1"></i> Unduh
                        </a>
                        <button id="printPdfBtn" class="btn btn-sm btn-outline-secondary px-3 rounded-pill">
                            <i class="fas fa-print me-1"></i> Cetak
                        </button>
                        <div class="vr mx-2 opacity-10"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0 bg-light" style="height: 75vh;">
                    <iframe id="pdfIframe" src="" width="100%" height="100%" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/guru/daftarSiswa.js') }}"></script>
@endpush
