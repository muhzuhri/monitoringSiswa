@extends('layouts.nav.admin')

@section('title', 'Manajemen Siswa - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
@endpush

@section('body')
    <div class="management-container" 
        data-jurnal-url="{{ route('admin.rekap.jurnal', ['nisn' => ':nisn']) }}"
        data-absensi-url="{{ route('admin.rekap.absensi', ['nisn' => ':nisn']) }}"
        data-kelompok-url="{{ route('admin.rekap.kelompok', ['nisn' => ':nisn']) }}"
        data-nilai-guru-url="{{ route('admin.rekap.nilaiGuru', ['nisn' => ':nisn']) }}"
        data-nilai-pembimbing-url="{{ route('admin.rekap.nilaiPembimbing', ['nisn' => ':nisn']) }}"
        data-laporan-akhir-url="{{ route('admin.rekap.laporanAkhir', ['nisn' => ':nisn']) }}"
        data-sertifikat-url="{{ route('admin.rekap.sertifikat', ['nisn' => ':nisn']) }}"
        data-asset-loader="{{ asset('images/unsri-pride.png') }}">

        <!-- Global Navigation Tabs: Siswa, Guru, Pembimbing -->
        <div class="tabs-wrapper border-0 bg-transparent mb-4">
            <div class="tabs-nav d-flex w-100 gap-3">
                <a href="{{ route('admin.kelolaSiswa') }}"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaSiswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('admin.kelolaGuru') }}"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaGuru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('admin.kelolaPembimbing') }}"
                    class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('admin.kelolaPembimbing') ? 'active' : '' }}">
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
                            <input type="text" name="search" value="{{ $search }}" class="p-input with-icon"
                                placeholder="Cari Siswa / Sekolah / NISN..." onchange="this.form.submit()">
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
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1"
                    style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center" id="siswa-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-siswa" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang ({{ $siswa->count() }})</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="history-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-riwayat" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Siswa ({{ $riwayatSiswas->count() }})</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center" id="lokasi-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-lokasi" type="button" role="tab" style="border-radius: 12px;">
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
                                <i class="fas fa-th-large"></i> &nbsp;Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list"></i> &nbsp;Seluruh Siswa
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
                                            <button class="btn-premium-circle btn-view-p btn-detail" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailSiswa" data-nisn="{{ $g['leader']->nisn }}"
                                                data-nama="{{ $g['leader']->nama }}"
                                                data-email="{{ $g['leader']->email }}"
                                                data-no_hp="{{ $g['leader']->no_hp }}"
                                                data-jk="{{ $g['leader']->jenis_kelamin }}"
                                                data-kelas="{{ $g['leader']->kelas }}"
                                                data-jurusan="{{ $g['leader']->jurusan }}"
                                                data-sekolah="{{ $g['leader']->sekolah }}"
                                                data-npsn="{{ $g['leader']->npsn }}"
                                                data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                data-tipe_magang="{{ $g['leader']->tipe_magang }}"
                                                data-nisn_ketua="{{ $g['leader']->nisn_ketua }}"
                                                data-surat_balasan="{{ $g['leader']->surat_balasan }}"
                                                data-tahun_ajaran="{{ $g['leader']->tahunAjaran->tahun_ajaran ?? '-' }}"
                                                data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}"
                                                data-guru-nip="{{ $g['leader']->guru->id_guru ?? '-' }}"
                                                data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}"
                                                data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
                                                data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                                title="Lihat Detail Profil">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                        </div>

                                        {{-- Identity --}}
                                        <div class="card-identity">
                                            <div class="card-avatar">
                                                @if ($g['is_group'])
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
                                                @if ($g['is_group'])
                                                    <span class="badge-kelompok">Kelompok
                                                        ({{ $g['members']->count() }})</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-info-list">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span
                                                    class="card-info-value">{{ Str::limit($g['leader']->sekolah, 22) }}</span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span
                                                    class="card-info-value">{{ Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22) }}</span>
                                            </div>
                                        </div>

                                        <div class="card-footer-bar">
                                            @if ($g['leader']->absen_hari_ini)
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
                                            @if ($g['is_group'])
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
                                        <th class="col-w-50">#</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah</th>
                                        <th>Lokasi Magang</th>
                                        <th>Status Hari Ini</th>
                                        <th class="text-end col-w-160">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $index => $s)
                                        <tr>
                                            <td data-label="#">{{ $siswa->firstItem() + $index }}</td>
                                            <td data-label="Identitas">
                                                <div class="d-flex align-items-center gap-2">
                                                    
                                                    <div>
                                                        <div class="cell-name fw-bold">{{ $s->nama }}</div>
                                                        <div class="cell-sub">{{ $s->nisn }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-label="Sekolah">
                                                <div class="cell-name">{{ Str::limit($s->sekolah, 25) }}</div>
                                                {{-- <div class="cell-sub text-muted">NPSN: {{ $s->npsn ?? '-' }}</div> --}}
                                            </td>
                                            <td data-label="Instansi">
                                                <div class="cell-name">{{ $s->perusahaan ?? 'Belum Ditugaskan' }}</div>
                                            </td>
                                            <td data-label="Status">
                                                @if ($s->absen_hari_ini)
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle"></i> Hadir
                                                    </span>
                                                @else
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-clock"></i> Menunggu
                                                    </span>
                                                @endif
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-premium-circle btn-view-p btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $s->nisn }}" data-nama="{{ $s->nama }}"
                                                        data-email="{{ $s->email }}"
                                                        data-no_hp="{{ $s->no_hp }}"
                                                        data-jk="{{ $s->jenis_kelamin }}"
                                                        data-kelas="{{ $s->kelas }}"
                                                        data-jurusan="{{ $s->jurusan }}"
                                                        data-sekolah="{{ $s->sekolah }}"
                                                        data-npsn="{{ $s->npsn }}"
                                                        data-perusahaan="{{ $s->perusahaan }}"
                                                        data-tipe_magang="{{ $s->tipe_magang }}"
                                                        data-nisn_ketua="{{ $s->nisn_ketua }}"
                                                        data-surat_balasan="{{ $s->surat_balasan }}"
                                                        data-tahun_ajaran="{{ $s->tahunAjaran->tahun_ajaran ?? '-' }}"
                                                        data-mulai="{{ $s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                        data-selesai="{{ $s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                        data-guru-nama="{{ $s->guru->nama ?? '-' }}"
                                                        data-guru-nip="{{ $s->guru->id_guru ?? '-' }}"
                                                        data-guru-hp="{{ $s->guru->no_hp ?? '-' }}"
                                                        data-pl-nama="{{ $s->pembimbing->nama ?? '-' }}"
                                                        data-pl-nip="{{ $s->pembimbing->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $s->pembimbing->no_telp ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-edit-p btn-edit"
                                                        data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                                        data-id="{{ $s->nisn }}" data-nama="{{ $s->nama }}"
                                                        data-email="{{ $s->email }}"
                                                        data-no_hp="{{ $s->no_hp }}"
                                                        data-jk="{{ $s->jenis_kelamin }}"
                                                        data-kelas="{{ $s->kelas }}"
                                                        data-jurusan="{{ $s->jurusan }}"
                                                        data-sekolah="{{ $s->sekolah }}"
                                                        data-npsn="{{ $s->npsn }}"
                                                        data-id_tahun_ajaran="{{ $s->id_tahun_ajaran }}"
                                                        data-perusahaan="{{ $s->perusahaan }}"
                                                        data-tipe_magang="{{ $s->tipe_magang }}"
                                                        data-nisn_ketua="{{ $s->nisn_ketua }}"
                                                        data-surat_balasan="{{ $s->surat_balasan }}"
                                                        data-guruNip="{{ $s->id_guru }}"
                                                        data-plNip="{{ $s->id_pembimbing }}"
                                                        data-mulai_raw="{{ $s->tgl_mulai_magang ? \Carbon\Carbon::parse($s->tgl_mulai_magang)->format('Y-m-d') : '' }}"
                                                        data-selesai_raw="{{ $s->tgl_selesai_magang ? \Carbon\Carbon::parse($s->tgl_selesai_magang)->format('Y-m-d') : '' }}"
                                                        title="Edit Data">
                                                        <i class="fas fa-user-edit"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-delete-p btn-delete-trigger"
                                                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                        data-url="{{ route('admin.destroySiswa', $s->nisn) }}"
                                                        title="Hapus Akun">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-5 text-muted">
                                                Tidak ada data siswa aktif yang terdaftar.
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
                    <div class="history-toolbar mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="view-mode-wrapper">
                                <button class="view-mode-btn" data-view="grouped" data-target="riwayat"
                                    title="Per Kelompok">
                                    <i class="fas fa-th-large"></i>&nbsp; Per Kelompok
                                </button>
                                <button class="view-mode-btn active" data-view="flat" data-target="riwayat"
                                    title="Tampilan List">
                                    <i class="fas fa-list"></i>&nbsp; Seluruh Siswa
                                </button>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <span class="filter-label text-muted small fw-bold">
                                    <i class="fas fa-filter"></i> Periode:
                                </span>

                                <form action="{{ route('admin.kelolaSiswa') }}" method="GET" class="filter-form d-flex align-items-center gap-2"
                                    id="filterPeriodeForm">
                                    <input type="hidden" name="tab" value="history">
                                    @if ($search)
                                        <input type="hidden" name="search" value="{{ $search }}">
                                    @endif
                                    <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius: 10px; min-width: 160px; height: 38px;">
                                        <option value="">-- Semua Periode --</option>
                                        @foreach ($periodeOptions as $opt)
                                            <option value="{{ $opt->id_tahun_ajaran }}"
                                                {{ $periodeId == $opt->id_tahun_ajaran ? 'selected' : '' }}>
                                                {{ $opt->tahun_ajaran }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($periodeId)
                                        <a href="{{ route('admin.kelolaSiswa', ['tab' => 'history', 'search' => $search]) }}"
                                            class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" 
                                            title="Reset Filter" style="width: 32px; height: 32px;">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                    @endif
                                </form>
                            </div>
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
                                                @if ($g['is_group'])
                                                    <i class="fas fa-layer-group"></i>
                                                @else
                                                    {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div class="student-meta">
                                                <h6 class="student-name">
                                                    {{ $g['leader']->nama }}
                                                    @if ($g['is_group'])
                                                        <span
                                                            class="badge bg-info-light text-info-dark">{{ $g['members']->count() }}
                                                            Anggota</span>
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
                                                <span class="info-value"
                                                    title="{{ $g['leader']->sekolah }}">{{ $g['leader']->sekolah }}</span>
                                            </div>
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-building"></i>
                                                    PERUSAHAAN</label>
                                                <span class="info-value"
                                                    title="{{ $g['leader']->perusahaan }}">{{ $g['leader']->perusahaan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="action-grid">
                                            @if ($g['is_group'])
                                                <button class="btn-action btn-detail-group btn-show-members"
                                                    data-name="{{ $g['leader']->nama }}"
                                                    data-members="{{ $g['members']->toJson() }}"
                                                    data-show-actions="true">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota Kelompok
                                                </button>
                                            @else
                                                <button class="btn-action btn-detail-group btn-detail"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                    data-nisn="{{ $g['leader']->nisn }}"
                                                    data-nama="{{ $g['leader']->nama }}"
                                                    data-email="{{ $g['leader']->email }}"
                                                    data-no_hp="{{ $g['leader']->no_hp }}"
                                                    data-jk="{{ $g['leader']->jenis_kelamin }}"
                                                    data-kelas="{{ $g['leader']->kelas }}"
                                                    data-jurusan="{{ $g['leader']->jurusan }}"
                                                    data-sekolah="{{ $g['leader']->sekolah }}"
                                                    data-npsn="{{ $g['leader']->npsn }}"
                                                    data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                    data-tipe_magang="{{ $g['leader']->tipe_magang }}"
                                                    data-nisn_ketua="{{ $g['leader']->nisn_ketua }}"
                                                    data-surat_balasan="{{ $g['leader']->surat_balasan }}"
                                                    data-tahun_ajaran="{{ $g['leader']->tahunAjaran->tahun_ajaran ?? '-' }}"
                                                    data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                    data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                    data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}"
                                                    data-guru-nip="{{ $g['leader']->guru->id_guru ?? '-' }}"
                                                    data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}"
                                                    data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
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
                                        <th class="col-w-50">#</th>
                                        <th>Identitas Siswa</th>
                                        <th>Sekolah </th>
                                        <th>Lokasi Magang</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end col-w-200">Aksi & Rekap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatSiswas as $index => $rs)
                                        <tr>
                                            <td data-label="#">{{ $index + 1 }}</td>
                                            <td data-label="Siswa">
                                                <div class="cell-name fw-bold">{{ $rs->nama }}</div>
                                                <div class="cell-sub">{{ $rs->nisn }}
                                                </div>
                                            </td>
                                            <td data-label="Sekolah">
                                                <div class="cell-name">{{ Str::limit($rs->sekolah, 25) }}</div>
                                                {{-- <div class="cell-sub text-muted">NPSN: {{ $rs->npsn ?? '-' }}</div> --}}
                                            </td>
                                            <td data-label="Penempatan">
                                                <div class="cell-name">{{ $rs->perusahaan ?? '-' }}</div>
                                                {{-- <div class="cell-sub text-muted small">
                                                    {{ $rs->tipe_magang ?? 'individu' }}</div> --}}
                                            </td>
                                            <td data-label="Periode">
                                                <div class="period-display">
                                                    <div class="period-item">
                                                        <i class="fas fa-calendar-check text-primary opacity-50"></i>
                                                        {{ \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d/m/y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d/m/y') }}
                                                    </div>
                                                    <div class="cell-sub text-info">
                                                        {{ $rs->tahunAjaran->tahun_ajaran ?? '-' }}</div>
                                                </div>
                                            </td>
                                            <td data-label="Aksi">
                                                <div class="d-flex flex-column align-items-end gap-2 py-2">
                                                    <div class="action-group justify-content-end">
                                                        <button class="btn-premium-circle btn-view-p btn-detail"
                                                            data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                            data-nisn="{{ $rs->nisn }}"
                                                            data-nama="{{ $rs->nama }}"
                                                            data-email="{{ $rs->email }}"
                                                            data-no_hp="{{ $rs->no_hp }}"
                                                            data-jk="{{ $rs->jenis_kelamin }}"
                                                            data-kelas="{{ $rs->kelas }}"
                                                            data-jurusan="{{ $rs->jurusan }}"
                                                            data-sekolah="{{ $rs->sekolah }}"
                                                            data-npsn="{{ $rs->npsn }}"
                                                            data-perusahaan="{{ $rs->perusahaan }}"
                                                            data-tipe_magang="{{ $rs->tipe_magang }}"
                                                            data-nisn_ketua="{{ $rs->nisn_ketua }}"
                                                            data-surat_balasan="{{ $rs->surat_balasan }}"
                                                            data-tahun_ajaran="{{ $rs->tahunAjaran->tahun_ajaran ?? '-' }}"
                                                            data-mulai="{{ $rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                            data-selesai="{{ $rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                            data-guru-nama="{{ $rs->guru->nama ?? '-' }}"
                                                            data-guru-nip="{{ $rs->guru->id_guru ?? '-' }}"
                                                            data-guru-hp="{{ $rs->guru->no_hp ?? '-' }}"
                                                            data-pl-nama="{{ $rs->pembimbing->nama ?? '-' }}"
                                                            data-pl-nip="{{ $rs->pembimbing->id_pembimbing ?? '-' }}"
                                                            data-pl-hp="{{ $rs->pembimbing->no_telp ?? '-' }}"
                                                            title="Profil Lengkap">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-absensi-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.absensi', $rs->nisn) }}"
                                                            title="Rekap Absensi">
                                                            <i class="fas fa-calendar-check"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.jurnal', $rs->nisn) }}"
                                                            title="Rekap Jurnal / Kegiatan">
                                                            <i class="fas fa-book-open"></i>
                                                        </button>
                                                    </div>
                                                    <div class="action-group justify-content-end">
                                                        <button class="btn-premium-circle btn-star-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.nilaiGuru', $rs->nisn) }}"
                                                            title="Penilaian Guru">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-user-check-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.nilaiPembimbing', $rs->nisn) }}"
                                                            title="Penilaian Pembimbing">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-pdf-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.laporanAkhir', $rs->nisn) }}"
                                                            title="Laporan Akhir Siswa">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </button>
                                                        <button class="btn-premium-circle btn-cert-p btn-preview-pdf"
                                                            data-url="{{ route('admin.rekap.sertifikat', $rs->nisn) }}"
                                                            title="Sertifikat Magang">
                                                            <i class="fas fa-certificate"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center p-5 text-muted">
                                                Belum ada riwayat siswa yang tersedia.
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
                        <button class="btn-primary-custom btn-sm rounded-pill px-4" data-bs-toggle="modal"
                            data-bs-target="#modalTambahLokasi">
                            <i class="fas fa-plus me-1"></i> Tambah Lokasi
                        </button>
                    </div>

                    <div class="row g-4">
                        @forelse($lokasis as $l)
                            <div class="col-xl-4 col-md-6">
                                <div class="student-card p-4"> {{-- Reuse student-card style for consistency --}}
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="card-avatar"
                                            style="background: rgba(13, 110, 253, 0.1); color: #0d6efd; width: 45px; height: 45px;">
                                            <i class="fas fa-map-marked-alt"></i>
                                        </div>
                                        <div class="card-actions position-static">
                                            <button class="action-round btn-edit-loc" data-bs-toggle="modal"
                                                data-bs-target="#modalEditLokasi" data-id="{{ $l->id }}"
                                                data-nama="{{ $l->nama_lokasi }}" data-lat="{{ $l->latitude }}"
                                                data-lng="{{ $l->longitude }}" data-radius="{{ $l->radius }}"
                                                data-active="{{ $l->is_active }}" title="Edit Lokasi">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-round btn-delete-loc" data-bs-toggle="modal"
                                                data-bs-target="#modalHapusLokasi"
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
                                            <span class="card-info-value"><i class="fas fa-bullseye me-1 opacity-50"></i>
                                                {{ $l->radius }}m</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between">
                                        @if ($l->is_active)
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
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File"
                                target="_blank">
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body pdf-viewer-body">
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
                </div
            </div>
        </div>
    </div>

    {{-- Modals: Tambah, Edit, Detail, Hapus --}}
    @include('admin.kelolaSiswaModals')

    @push('scripts')
        <script src="{{ asset('assets/js/admin/kelolaSiswa.js') }}"></script>
    @endpush

    {{-- PDF.js library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
@endsection
