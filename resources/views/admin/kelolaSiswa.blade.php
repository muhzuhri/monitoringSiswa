@extends('layouts.nav.admin')

@section('title', 'Manajemen Siswa - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
@endpush

@section('body')
    <div class="management-container">
        
        <!-- Global Navigation Tabs: Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav d-flex w-100 gap-3">
                <a href="{{ route('admin.kelolaSiswa') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaSiswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('admin.kelolaGuru') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaGuru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('admin.kelolaPembimbing') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaPembimbing') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper">

            {{-- ============================================================
                 HEADER
            ============================================================ --}}
            <div class="management-header">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h5>Manajemen Siswa</h5>
                        <p>Kelola data seluruh siswa magang dan riwayat mereka.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <form action="{{ route('admin.kelolaSiswa') }}" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                class="p-input with-icon"
                                placeholder="Cari Siswa / Sekolah / NISN..."
                                onchange="this.form.submit()"
                            >
                        </div>
                    </form>                    
                </div>
            </div>

            {{-- ============================================================
                 NOTIFICATION
            ============================================================ --}}
            @if (session('success'))
                <div class="custom-alert alert-success-custom">
                    <span><i class="fas fa-check-circle me-2"></i> {{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ============================================================
                 TABS NAVIGATION
            ============================================================ --}}
            <div class="tabs-wrapper">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="siswa-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-siswa"
                        type="button" role="tab">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang ({{ $siswa->count() }})</span>
                    </button>
                    <button class="tab-button" id="history-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-riwayat"
                        type="button" role="tab">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Siswa ({{ $riwayatSiswas->count() }})</span>
                    </button>
                    <button class="tab-button" id="lokasi-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-lokasi"
                        type="button" role="tab">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Lokasi Absensi ({{ $lokasis->count() }})</span>
                    </button>
                </div>
            </div>

            {{-- ============================================================
                 TAB CONTENT
            ============================================================ --}}
            <div class="tab-content">

                {{-- ==================== TAB: SISWA AKTIF ==================== --}}
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                            <i class="fas fa-plus"></i>
                            <span>Tambah Siswa</span>
                        </button>
                    </div>

                    {{-- Toolbar --}}
                    <div class="tab-toolbar">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="active">
                                <i class="fas fa-th-large"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list"></i> Seluruh Siswa
                            </button>
                        </div>
                        <div class="tab-toolbar-info">
                            Menampilkan <strong>{{ $siswa->count() }}</strong> siswa aktif
                        </div>
                    </div>

                    {{-- View: Per Kelompok (Cards) --}}
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                            @forelse($groupedSiswas as $g)
                                <div class="col-xl-4 col-md-6">
                                    <div class="student-card">

                                        {{-- Quick Action Buttons --}}
                                        <div class="card-actions">
                                            <button class="action-round btn-detail"
                                                data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                data-nisn="{{ $g['leader']->nisn }}"
                                                data-nama="{{ $g['leader']->nama }}"
                                                data-email="{{ $g['leader']->email }}"
                                                data-no_hp="{{ $g['leader']->no_hp }}"
                                                data-kelas="{{ $g['leader']->kelas }}"
                                                data-jurusan="{{ $g['leader']->jurusan }}"
                                                data-sekolah="{{ $g['leader']->sekolah }}"
                                                data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}"
                                                data-guru-nip="{{ $g['leader']->guru->id_guru ?? '-' }}"
                                                data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}"
                                                data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
                                                data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                        </div>

                                        {{-- Identity --}}
                                        <div class="card-identity">
                                            <div class="card-avatar">
                                                @if($g['is_group'])
                                                    <div class="avatar-group-icon">
                                                        <i class="fas fa-user-friends"></i>
                                                    </div>
                                                @else
                                                    <span>{{ strtoupper(substr($g['leader']->nama, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="card-identity-info">
                                                <h6>{{ $g['leader']->nama }}</h6>
                                                <p>NISN: {{ $g['leader']->nisn }}</p>
                                                @if($g['is_group'])
                                                    <span class="badge-kelompok">Kelompok ({{ $g['members']->count() }})</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-info-list">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span class="card-info-value">{{ Str::limit($g['leader']->sekolah, 22) }}</span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span class="card-info-value">{{ Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22) }}</span>
                                            </div>
                                        </div>

                                        <div class="card-footer-bar">
                                            @if($g['leader']->absen_hari_ini)
                                                <span class="status-label hadir">
                                                    <span class="status-dot hadir"></span> Hadir
                                                </span>
                                            @else
                                                <span class="status-label belum">
                                                    <span class="status-dot belum"></span> Belum Absen
                                                </span>
                                            @endif
                                            <span class="card-guru-info">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                                {{ Str::limit($g['leader']->guru->nama ?? '-', 15) }}
                                            </span>
                                        </div>

                                        <div class="action-grid">
                                            @if($g['is_group'])
                                                <button class="btn-action btn-show-members" 
                                                        data-name="{{ $g['leader']->nama }}"
                                                        data-members="{{ $g['members']->toJson() }}"
                                                        data-show-actions="false">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota
                                                </button>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-user-slash"></i></div>
                                        <p>Tidak ada siswa aktif ditemukan.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- View: Seluruh Siswa (Table) --}}
                    <div class="view-container d-none" id="active-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah / Penempatan</th>
                                        <th>Status Hari Ini</th>
                                        <th>Pembimbing</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $s)
                                        <tr>
                                            <td>
                                                <div class="cell-name">{{ $s->nama }}</div>
                                                <div class="cell-sub">{{ $s->nisn }} &middot; {{ $s->kelas }}</div>
                                            </td>
                                            <td>
                                                <div class="cell-sub"><i class="fas fa-university me-1 text-primary opacity-75"></i> {{ $s->sekolah }}</div>
                                                <div class="cell-sub"><i class="fas fa-building me-1 opacity-50"></i> {{ $s->perusahaan ?? '-' }}</div>
                                            </td>
                                            <td>
                                                @if($s->absen_hari_ini)
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle"></i> Hadir
                                                    </span>
                                                @else
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-times-circle"></i> Belum Absen
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="cell-sub"><i class="fas fa-chalkboard-teacher me-1 opacity-50"></i> {{ $s->guru->nama ?? '-' }}</div>
                                                <div class="cell-sub"><i class="fas fa-user-tie me-1 opacity-50"></i> {{ $s->pembimbing->nama ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-icon btn-detail-soft btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $s->nisn }}" data-nama="{{ $s->nama }}" data-email="{{ $s->email }}"
                                                        data-no_hp="{{ $s->no_hp }}" data-kelas="{{ $s->kelas }}" data-jurusan="{{ $s->jurusan }}"
                                                        data-sekolah="{{ $s->sekolah }}" data-perusahaan="{{ $s->perusahaan }}"
                                                        data-mulai="{{ $s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                        data-selesai="{{ $s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                        data-guru-nama="{{ $s->guru->nama ?? '-' }}" data-guru-nip="{{ $s->guru->id_guru ?? '-' }}"
                                                        data-pl-nama="{{ $s->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $s->pembimbing->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $s->pembimbing->no_telp ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-icon btn-edit-soft btn-edit"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                                        data-id="{{ $s->nisn }}" data-nama="{{ $s->nama }}" data-email="{{ $s->email }}"
                                                        data-kelas="{{ $s->kelas }}" data-jurusan="{{ $s->jurusan }}" data-sekolah="{{ $s->sekolah }}"
                                                        data-perusahaan="{{ $s->perusahaan }}" data-guru-nip="{{ $s->id_guru }}"
                                                        data-pl-nip="{{ $s->id_pembimbing }}"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-icon btn-delete-soft btn-delete-trigger"
                                                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                        data-url="{{ route('admin.destroySiswa', $s->nisn) }}"
                                                        title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-5 text-muted">
                                                Tidak ada data siswa aktif.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB: RIWAYAT SISWA ==================== --}}
                <div class="tab-pane fade" id="pane-riwayat" role="tabpanel">

                    {{-- Filter Bar --}}
                    <div class="history-filter-bar">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <span class="filter-label">
                                <i class="fas fa-filter"></i> Periode:
                            </span>
                            <form action="{{ route('admin.kelolaSiswa') }}" method="GET" class="filter-form" id="filterPeriodeForm">
                                <input type="hidden" name="tab" value="history">
                                @if($search)
                                    <input type="hidden" name="search" value="{{ $search }}">
                                @endif
                                <select name="periode" class="filter-select" onchange="this.form.submit()">
                                    <option value="">-- Semua Periode --</option>
                                    @foreach($periodeOptions as $opt)
                                        <option value="{{ $opt->id_tahun_ajaran }}"
                                            {{ $periodeId == $opt->id_tahun_ajaran ? 'selected' : '' }}>
                                            {{ $opt->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($periodeId)
                                    <a href="{{ route('admin.kelolaSiswa', ['tab' => 'history', 'search' => $search]) }}"
                                        class="btn-reset-filter" title="Reset Filter">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn" data-view="grouped" data-target="riwayat" title="Per Kelompok">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button class="view-mode-btn active" data-view="flat" data-target="riwayat" title="Tampilan List">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    {{-- View: Riwayat Cards (Grouped) --}}
                    <div class="view-container d-none" id="riwayat-grouped-view">
                        <div class="row g-4">
                            @forelse($groupedRiwayat as $g)
                                <div class="col-xl-4 col-md-6">
                                    <div class="history-card">
                                        {{-- Header --}}
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
                                                        <span class="badge bg-info-light text-info-dark">{{ $g['members']->count() }} Anggota</span>
                                                    @endif
                                                </h6>
                                                <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                                <div class="mt-1">
                                                    <span class="badge-completed">
                                                        <i class="fas fa-flag-checkered"></i> SELESAI
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="status-wrapper">
                                                <span class="badge-archive">
                                                    <i class="fas fa-archive"></i> Arsip
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Info Grid --}}
                                        <div class="info-grid">
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-school"></i> SEKOLAH</label>
                                                <span class="info-value" title="{{ $g['leader']->sekolah }}">{{ $g['leader']->sekolah }}</span>
                                            </div>
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-building"></i> PERUSAHAAN</label>
                                                <span class="info-value" title="{{ $g['leader']->perusahaan }}">{{ $g['leader']->perusahaan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="action-grid">
                                            @if($g['is_group'])
                                                <button class="btn-action btn-detail-group btn-show-members" 
                                                        data-name="{{ $g['leader']->nama }}"
                                                        data-members="{{ $g['members']->toJson() }}"
                                                        data-show-actions="true">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota Kelompok
                                                </button>
                                            @else
                                                <button class="btn-action btn-detail-group btn-detail"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                    data-nisn="{{ $g['leader']->nisn }}" data-nama="{{ $g['leader']->nama }}"
                                                    data-email="{{ $g['leader']->email }}" data-no_hp="{{ $g['leader']->no_hp }}"
                                                    data-kelas="{{ $g['leader']->kelas }}" data-jurusan="{{ $g['leader']->jurusan }}"
                                                    data-sekolah="{{ $g['leader']->sekolah }}" data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                    data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                    data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                    data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}" data-guru-nip="{{ $g['leader']->guru->id_guru ?? '-' }}"
                                                    data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
                                                    data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}">
                                                    <i class="fas fa-user-circle"></i> Detail Profil
                                                </button>
                                            @endif                                                                                        
                                            <button class="btn-action btn-detail-group btn-preview-pdf" 
                                                    data-url="{{ route('admin.rekap.kelompok', $g['leader']->nisn) }}">
                                                <i class="fas fa-users"></i> Rekap Kelompok bulanan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>


                    {{-- View: Riwayat Table (Flat) --}}
                    <div class="view-container" id="riwayat-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Siswa Magang</th>
                                        <th>Sekolah / Instansi</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end">Aksi Rekap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatSiswas as $rs)
                                        <tr>
                                            <td>
                                                <div class="cell-name">{{ $rs->nama }}</div>
                                                <div class="cell-sub">NISN: {{ $rs->nisn }}</div>
                                            </td>
                                            <td>
                                                <div class="cell-name">{{ $rs->sekolah }}</div>
                                                <div class="cell-sub">{{ $rs->perusahaan ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="cell-sub">
                                                    <i class="fas fa-play-circle me-1 text-success"></i>
                                                    {{ $rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->translatedFormat('d M Y') : '-' }}
                                                </div>
                                                <div class="cell-sub">
                                                    <i class="fas fa-flag-checkered me-1 text-danger"></i>
                                                    {{ $rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->translatedFormat('d M Y') : '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-history-action btn-absensi btn-preview-pdf"
                                                        data-url="{{ route('admin.rekap.absensi', $rs->nisn) }}"
                                                        title="Lihat Rekap Absensi">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                    <button class="btn-history-action btn-kegiatan btn-preview-pdf"
                                                        data-url="{{ route('admin.rekap.jurnal', $rs->nisn) }}"
                                                        title="Lihat Rekap Jurnal">
                                                        <i class="fas fa-book"></i>
                                                    </button>                                
                                                    <button class="btn-history-action btn-detail-h btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $rs->nisn }}" data-nama="{{ $rs->nama }}"
                                                        data-email="{{ $rs->email }}" data-no_hp="{{ $rs->no_hp }}"
                                                        data-kelas="{{ $rs->kelas }}" data-jurusan="{{ $rs->jurusan }}"
                                                        data-sekolah="{{ $rs->sekolah }}" data-perusahaan="{{ $rs->perusahaan }}"
                                                        data-mulai="{{ $rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                        data-selesai="{{ $rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                        data-guru-nama="{{ $rs->guru->nama ?? '-' }}" data-guru-nip="{{ $rs->guru->id_guru ?? '-' }}"
                                                        data-pl-nama="{{ $rs->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $rs->pembimbing->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $rs->pembimbing->no_telp ?? '-' }}"
                                                        title="Detail Profil">
                                                        <i class="fas fa-info-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                Belum ada riwayat siswa tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ==================== TAB: LOKASI ABSENSI ==================== --}}
                <div class="tab-pane fade" id="pane-lokasi" role="tabpanel">
                    
                    {{-- Toolbar Lokasi --}}
                    <div class="tab-toolbar justify-content-between">
                        <div class="tab-toolbar-info">
                            <i class="fas fa-info-circle me-1 text-primary"></i>
                            Menampilkan <strong>{{ $lokasis->count() }}</strong> titik lokasi absensi terdaftar
                        </div>
                        <button class="btn-primary-custom btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahLokasi">
                            <i class="fas fa-plus me-1"></i> Tambah Lokasi
                        </button>
                    </div>

                    <div class="row g-4">
                        @forelse($lokasis as $l)
                            <div class="col-xl-4 col-md-6">
                                <div class="student-card p-4"> {{-- Reuse student-card style for consistency --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="card-avatar" style="background: rgba(13, 110, 253, 0.1); color: #0d6efd; width: 45px; height: 45px;">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div class="card-actions position-static">
                                            <button class="action-round btn-edit-loc" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditLokasi"
                                                data-id="{{ $l->id }}"
                                                data-nama="{{ $l->nama_lokasi }}"
                                                data-lat="{{ $l->latitude }}"
                                                data-lng="{{ $l->longitude }}"
                                                data-radius="{{ $l->radius }}"
                                                data-active="{{ $l->is_active }}"
                                                title="Edit Lokasi">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-round btn-delete-loc"
                                                data-bs-toggle="modal" data-bs-target="#modalHapusLokasi"
                                                data-url="{{ route('admin.destroyLokasi', $l->id) }}"
                                                title="Hapus Lokasi">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-1">{{ $l->nama_lokasi }}</h6>
                                    <p class="text-muted small mb-3">ID Lokasi: #LOK-{{ $l->id }}</p>

                                    <div class="card-info-list mb-3">
                                        <div class="card-info-row">
                                            <span class="card-info-label">Latitude</span>
                                            <span class="card-info-value fw-mono">{{ $l->latitude }}</span>
                                        </div>
                                        <div class="card-info-row">
                                            <span class="card-info-label">Longitude</span>
                                            <span class="card-info-value fw-mono">{{ $l->longitude }}</span>
                                        </div>
                                        <div class="card-info-row">
                                            <span class="card-info-label">Radius</span>
                                            <span class="card-info-value"><i class="fas fa-bullseye me-1 opacity-50"></i> {{ $l->radius }}m</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        @if($l->is_active)
                                            <span class="badge-status hadir" style="font-size: 0.75rem;">
                                                <i class="fas fa-check-circle"></i> Status Aktif
                                            </span>
                                        @else
                                            <span class="badge-status belum" style="font-size: 0.75rem;">
                                                <i class="fas fa-times-circle"></i> Nonaktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-map-marker-slash"></i></div>
                                    <p>Belum ada titik lokasi absensi yang terdaftar.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>{{-- /admin-content-wrapper --}}
    </div>{{-- /management-container --}}

    {{-- ============================================================
         MODAL: PREVIEW PDF (Premium Style)
    ============================================================ --}}
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title">Preview Laporan</h6>
                    </div>
                    
                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">                           
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" target="_blank">
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="pdf-viewer-body">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <div class="loader-logo-container">
                                <img src="{{ asset('images/unsri-pride.png') }}" alt="UNSRI">
                            </div>
                        </div>
                        <div id="pdfErrorMsg" class="d-none">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Gagal memuat file PDF.<br><small>Coba gunakan tombol Unduh.</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals: Tambah, Edit, Detail, Hapus --}}
    @include('admin.kelolaSiswaModals')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── View Mode Switching ────────────────────────────────────────
            document.querySelectorAll('.view-mode-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const view   = this.dataset.view;
                    const target = this.dataset.target;
                    const pane   = this.closest('.tab-pane');

                    pane.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const grouped = pane.querySelector(`#${target}-grouped-view`);
                    const flat    = pane.querySelector(`#${target}-flat-view`);

                    if (view === 'grouped') {
                        grouped.classList.remove('d-none');
                        flat.classList.add('d-none');
                    } else {
                        grouped.classList.add('d-none');
                        flat.classList.remove('d-none');
                    }
                });
            });

            // ── Tab Persistence from URL ───────────────────────────────────
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
                const historyTabBtn = document.getElementById('history-tab');
                if (historyTabBtn) {
                    new bootstrap.Tab(historyTabBtn).show();
                }
            }

            // ── Edit Modal ─────────────────────────────────────────────────
            const editForm = document.getElementById('formEditSiswa');
            document.querySelectorAll('.btn-edit').forEach(button => {
                button.addEventListener('click', function () {
                    editForm.action = `/admin/siswa/${this.dataset.id}`;
                    document.getElementById('edit_nama').value          = this.dataset.nama;
                    document.getElementById('edit_email').value         = this.dataset.email;
                    document.getElementById('edit_nisn').value          = this.dataset.id;
                    document.getElementById('edit_kelas').value         = this.dataset.kelas;
                    document.getElementById('edit_jurusan').value       = this.dataset.jurusan;
                    document.getElementById('edit_sekolah').value       = this.dataset.sekolah;
                    document.getElementById('edit_perusahaan').value    = this.dataset.perusahaan || '';
                    document.getElementById('edit_id_guru').value       = this.dataset.guruNip || '';
                    document.getElementById('edit_id_pembimbing').value = this.dataset.plNip || '';
                });
            });

            // ── Detail Modal ───────────────────────────────────────────────
            function initDetailModalListeners() {
                document.querySelectorAll('.btn-detail').forEach(button => {
                    button.onclick = null;
                    button.addEventListener('click', function () {
                        document.getElementById('det_name').textContent         = this.dataset.nama;
                        document.getElementById('det_nisn').textContent         = this.dataset.nisn;
                        document.getElementById('det_email').textContent        = this.dataset.email;
                        document.getElementById('det_hp').textContent           = this.dataset.no_hp || '-';
                        document.getElementById('det_kelas_jurusan').textContent = `${this.dataset.kelas} - ${this.dataset.jurusan}`;
                        document.getElementById('det_sekolah').textContent      = this.dataset.sekolah;
                        document.getElementById('det_perusahaan').textContent   = this.dataset.perusahaan || 'Belum ditugaskan';

                        const mulai   = this.dataset.mulai;
                        const selesai = this.dataset.selesai;
                        document.getElementById('det_periode').textContent = (mulai && selesai && mulai !== '-')
                            ? `${mulai} s/d ${selesai}` : 'Belum ditentukan';

                        document.getElementById('det_guru_nama').textContent = this.dataset.guruNama || '-';
                        document.getElementById('det_guru_nip').textContent  = this.dataset.guruNip || '-';
                        document.getElementById('det_pl_nama').textContent   = this.dataset.plNama || '-';
                        document.getElementById('det_pl_nip').textContent    = this.dataset.plNip || '-';
                        document.getElementById('det_pl_hp').textContent     = this.dataset.plHp || '-';
                    });
                });
            }
            initDetailModalListeners();

            // ── Delete Modal ───────────────────────────────────────────────
            const deleteForm = document.getElementById('formHapus');
            document.querySelectorAll('.btn-delete-trigger').forEach(button => {
                button.addEventListener('click', function () {
                    deleteForm.action = this.dataset.url;
                });
            });



            // ── Lokasi Modal Handlers ──────────────────────────────────────
            const editLocForm = document.getElementById('formEditLokasi');
            document.querySelectorAll('.btn-edit-loc').forEach(button => {
                button.addEventListener('click', function () {
                    editLocForm.action = `/admin/lokasi/${this.dataset.id}`;
                    document.getElementById('edit_loc_nama').value   = this.dataset.nama;
                    document.getElementById('edit_loc_lat').value    = this.dataset.lat;
                    document.getElementById('edit_loc_lng').value    = this.dataset.lng;
                    document.getElementById('edit_loc_radius').value = this.dataset.radius;
                    document.getElementById('edit_loc_active').value = this.dataset.active;
                });
            });

            const deleteLocForm = document.getElementById('formHapusLokasi');
            document.querySelectorAll('.btn-delete-loc').forEach(button => {
                button.addEventListener('click', function () {
                    deleteLocForm.action = this.dataset.url;
                });
            });

            // ── Modal Group Members (Riwayat) ─────────────────────────────
            const groupModalEl     = document.getElementById('groupMembersModal');
            const groupModal        = groupModalEl ? new bootstrap.Modal(groupModalEl) : null;
            const modalNameEl       = document.getElementById('modalGroupName');
            const modalBodyEl       = document.getElementById('modalGroupBody');

            document.querySelectorAll('.btn-show-members').forEach(button => {
                button.addEventListener('click', function() {
                    const name = this.dataset.name;
                    const members = JSON.parse(this.dataset.members);
                    const showActions = this.dataset.showActions === 'true';
                    
                    modalNameEl.innerText = name;
                    modalBodyEl.innerHTML = '';

                    // Adjust table head actions column visibility (Always show now)
                    const tableHead = groupModalEl.querySelector('thead tr');
                    const actionsTh = tableHead.querySelector('th:last-child');
                    actionsTh.classList.remove('d-none');
                    
                    // Route patterns
                    const logDownloadBase = "{{ route('admin.rekap.jurnal', ['nisn' => ':nisn']) }}";
                    const absDownloadBase = "{{ route('admin.rekap.absensi', ['nisn' => ':nisn']) }}";

                    members.forEach((member) => {
                        const logDownload = logDownloadBase.replace(':nisn', member.nisn);
                        const absDownload = absDownloadBase.replace(':nisn', member.nisn);
                        const kelDownload = "{{ route('admin.rekap.kelompok', ['nisn' => '__NISN__']) }}".replace('__NISN__', member.nisn);
                        
                        const statusBadge = showActions ? `
                            <span class="badge bg-secondary-light text-muted px-2 py-1 small rounded-pill" style="font-size: 0.65rem;">
                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
                            </span>
                        ` : `
                            <span class="badge bg-success-light text-success px-2 py-1 small rounded-pill" style="font-size: 0.65rem;">
                                <i class="fas fa-check-circle me-1"></i> AKTIF
                            </span>
                        `;

                        const row = `
                            <tr>
                                <td>
                                    <div class="cell-name mb-0" style="font-size: 0.95rem;">${member.nama}</div>
                                    <div class="cell-sub small text-muted">NISN: ${member.nisn}</div>
                                </td>
                                <td class="text-center">
                                    ${statusBadge}
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn-small btn-detail" title="Lihat Informasi Akun" 
                                            data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                            data-nisn="${member.nisn}" data-nama="${member.nama}" data-email="${member.email}"
                                            data-no_hp="${member.no_hp || ''}" data-kelas="${member.kelas}" data-jurusan="${member.jurusan}"
                                            data-sekolah="${member.sekolah}" data-perusahaan="${member.perusahaan || ''}"
                                            data-mulai="${member.tgl_mulai_magang || ''}" data-selesai="${member.tgl_selesai_magang || ''}"
                                            data-guru-nama="${member.guru ? member.guru.nama : '-'}" data-guru-nip="${member.id_guru || '-'}"
                                            data-pl-nama="${member.pembimbing ? member.pembimbing.nama : '-'}" data-pl-nip="${member.id_pembimbing || '-'}"
                                            data-pl-hp="${member.pembimbing ? member.pembimbing.no_telp : '-'}"
                                            style="background: #eef2ff; color: #4f46e5; padding: 8px 12px; border-radius: 10px; border: 1px solid #e0e7ff; transition: all 0.3s ease;">
                                            <i class="fas fa-user-circle"></i>
                                        </button>

                                        ${showActions ? `
                                            <button class="btn-small btn-preview-pdf" title="Cetak Jurnal" data-url="${logDownload}" 
                                                style="background: #f0f9ff; color: #0369a1; padding: 8px 12px; border-radius: 10px; border: 1px solid #bae6fd; transition: all 0.3s ease;">
                                                <i class="fas fa-book"></i>
                                            </button>
                                            <button class="btn-small btn-preview-pdf" title="Cetak Absensi" data-url="${absDownload}" 
                                                style="background: #f0fdf4; color: #15803d; padding: 8px 12px; border-radius: 10px; border: 1px solid #bbf7d0; transition: all 0.3s ease;">
                                                <i class="fas fa-calendar-check"></i>
                                            </button>
                                            <button class="btn-small btn-preview-pdf" title="Cetak Rekap Kelompok" data-url="${kelDownload}" 
                                                style="background: #fff7ed; color: #9a3412; padding: 8px 12px; border-radius: 10px; border: 1px solid #ffedd5; transition: all 0.3s ease;">
                                                <i class="fas fa-users"></i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                        modalBodyEl.innerHTML += row;
                    });
                    
                    // Re-init listeners for buttons
                    initPdfPreviewListeners();
                    initDetailModalListeners();
                    
                    if (groupModal) groupModal.show();
                });
            });

            // ── PDF Preview (PDF.js renderer — no browser toolbar) ────────
            const pdfModalEl  = document.getElementById('previewPdfModal');
            const pdfModal    = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
            const downloadBtn = document.getElementById('downloadPdfBtn');
            const printBtn    = document.getElementById('printPdfBtn');

            let currentPdfUrl = null;

            async function renderPDF(url) {
                const container  = document.getElementById('pdfCanvasContainer');
                const loadingEl  = document.getElementById('pdfLoadingIndicator');
                const errorEl    = document.getElementById('pdfErrorMsg');

                container.querySelectorAll('canvas').forEach(c => c.remove());
                loadingEl.style.display = 'flex';
                errorEl.classList.add('d-none');
                container.scrollTop = 0;

                try {
                    const pdfDoc = await pdfjsLib.getDocument(url).promise;
                    loadingEl.style.display = 'none';
                    const containerWidth = container.clientWidth - 40;
                    const outputScale   = window.devicePixelRatio || 1;

                    for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                        const page     = await pdfDoc.getPage(pageNum);
                        const baseScale = containerWidth / page.getViewport({ scale: 1 }).width;
                        const viewport  = page.getViewport({ scale: baseScale * outputScale });
                        const canvas    = document.createElement('canvas');
                        const ctx       = canvas.getContext('2d');
                        canvas.width    = viewport.width;
                        canvas.height   = viewport.height;
                        canvas.style.width  = (viewport.width  / outputScale) + 'px';
                        canvas.style.height = (viewport.height / outputScale) + 'px';
                        container.appendChild(canvas);
                        await page.render({ canvasContext: ctx, viewport }).promise;
                    }
                } catch (err) {
                    loadingEl.style.display = 'none';
                    errorEl.classList.remove('d-none');
                    console.error('PDF.js error:', err);
                }
            }

            function initPdfPreviewListeners() {
                document.querySelectorAll('.btn-preview-pdf').forEach(button => {
                    button.onclick = null;
                    button.addEventListener('click', function () {
                        const url = this.dataset.url;
                        if (!url) return;
                        currentPdfUrl     = url;
                        downloadBtn.href  = url + (url.includes('?') ? '&' : '?') + 'download=1';
                        if (pdfModal) pdfModal.show();
                        renderPDF(url);
                    });
                });
            }

            initPdfPreviewListeners();

            if (pdfModalEl) {
                // Cetak: buka tab baru lalu print
                printBtn.addEventListener('click', () => {
                    if (!currentPdfUrl) return;
                    const win = window.open(currentPdfUrl, '_blank');
                    win.addEventListener('load', () => win.print(), { once: true });
                });
                // Bersihkan canvas saat modal ditutup
                pdfModalEl.addEventListener('hidden.bs.modal', () => {
                    document.getElementById('pdfCanvasContainer').querySelectorAll('canvas').forEach(c => c.remove());
                    currentPdfUrl = null;
                });
            }

            // ── Password Toggle Logic ──────────────────────────────────────
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            });
        });
    </script>

    {{-- PDF.js library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
@endsection