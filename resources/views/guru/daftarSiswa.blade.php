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
                    <div class="view-mode-wrapper"
                        style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
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
                                                <span class="badge bg-info-light text-info-dark ms-1"
                                                    style="font-size: 0.65rem; border-radius: 50px;">{{ $g['members']->count() }}
                                                    Anggota</span>
                                            @endif
                                        </h6>
                                        <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                        <div class="mt-2 text-start">
                                            @if ($g['leader']->status === 'selesai')
                                                <span class="badge bg-secondary text-white"
                                                    style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                    <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                                </span>
                                            @else
                                                <span class="badge bg-success text-white"
                                                    style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
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
                                            <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i>
                                                Hadir</span>
                                        @elseif($hadirCount > 0)
                                            <span class="status-badge status-warning"><i class="fas fa-user-clock"></i>
                                                {{ $hadirCount }}/{{ $g['members']->count() }}</span>
                                        @else
                                            <span class="status-badge status-absen"><i class="fas fa-times-circle"></i>
                                                Belum Absen</span>
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
                                    @if ($g['is_group'])
                                        <button class="btn-action btn-detail-group btn-show-members"
                                            data-name="{{ $g['leader']->nama }}"
                                            data-members="{{ $g['members']->toJson() }}"
                                            data-logbook-route="{{ route('guru.logbook', ['nisn' => ':nisn']) }}"
                                            data-absensi-route="{{ route('guru.absensi', ['nisn' => ':nisn']) }}"
                                            data-logbook-download="{{ route('guru.rekap.jurnal', ['nisn' => ':nisn']) }}"
                                            data-absensi-download="{{ route('guru.rekap.absensi', ['nisn' => ':nisn']) }}">
                                            <i class="fas fa-search"></i> Pantau Kelompok
                                        </button>
                                    @else
                                        <button class="btn-action btn-detail" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailSiswa" 
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
                                            data-guru-hp="{{ $g['leader']->guru->no_hp ?? '-' }}"
                                            data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}"
                                            data-pl-nip="{{ $g['leader']->id_pembimbing ?? '-' }}"
                                            data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                            style="background: var(--primary-light); color: var(--primary); border: none;">
                                            <i class="fas fa-id-card"></i> Detail Profil
                                        </button>
                                    @endif
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
                                                <div class="td-siswa-nisn"><i
                                                        class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                    {{ $s->nisn }}</div>
                                            </td>
                                            <td>
                                                @if ($s->absen_hari_ini)
                                                    <span class="status-badge status-hadir"><i
                                                            class="fas fa-check-circle"></i> Hadir</span>
                                                @else
                                                    <span class="status-badge status-absen"><i
                                                            class="fas fa-times-circle"></i> Belum Absen</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school small"><i
                                                            class="fas fa-university me-1"></i> {{ $s->sekolah }}</span>
                                                    <small class="text-muted"><i class="fas fa-building me-1"></i>
                                                        {{ $s->perusahaan }}</small>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button class="btn-small btn-preview-pdf"
                                                        style="background: rgba(15, 23, 42, 0.04); color: #64748b;"
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
                                                        data-pl-nip="{{ $s->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $s->pembimbing->no_telp ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-id-card"></i>
                                                    </button>
                                                    <a href="{{ route('guru.logbook', $s->nisn) }}" class="btn-small btn-preview-pdf"
                                                        style="background: var(--primary-light); color: var(--primary);"
                                                        title="Logbook">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                    <a href="{{ route('guru.absensi', $s->nisn) }}" class="btn-small btn-preview-pdf"
                                                        style="background: var(--warning-light); color: #92400e;"
                                                        title="Absensi">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-4">Tidak ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="noResultsBimbingan" class="empty-state" style="display: none; width: 100%;">Tidak ada siswa
                    bimbingan yang cocok dengan pencarian.</div>
            </div>

            {{-- Tab Cari Siswa --}}
            <div class="tab-pane fade" id="search-students" role="tabpanel">
                {{-- Filter NPSN dihapus sesuai request, filter otomatis ada di controller --}}


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
                                            <div class="td-siswa-nisn"><i class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                {{ $ga['leader']->nisn }}</div>
                                        </td>
                                        <td><span class="badge-school"><i class="fas fa-university me-1"></i>
                                                {{ $ga['leader']->sekolah }}</span></td>
                                        <td>
                                            @if ($ga['is_group'])
                                                <span class="badge bg-info-light text-info-dark px-3 py-2"
                                                    style="border-radius: 50px;">
                                                    <i class="fas fa-layer-group me-1"></i> Kelompok
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-light text-muted px-3 py-2"
                                                    style="border-radius: 50px; background: #f1f5f9;">
                                                    <i class="fas fa-user me-1"></i> Individu
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ga['is_group'])
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-stack me-2">
                                                        @foreach ($ga['members']->take(3) as $member)
                                                            <div class="avatar-mini" title="{{ $member->nama }}">
                                                                {{ strtoupper(substr($member->nama, 0, 1)) }}</div>
                                                        @endforeach
                                                    </div>
                                                    <small class="fw-bold text-primary">{{ $ga['members']->count() }}
                                                        Orang</small>
                                                </div>
                                            @else
                                                <small class="text-muted">1 Orang</small>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('guru.siswa.claim', $ga['leader']->nisn) }}"
                                                method="POST">
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
                    <div class="view-mode-wrapper"
                        style="background: rgba(15, 23, 42, 0.04); padding: 4px; border-radius: 12px; display: inline-flex; gap: 4px;">
                        <button class="view-mode-btn" data-view="grouped" data-target="history">
                            <i class="fas fa-th-large me-1"></i> Perkelompok
                        </button>
                        <button class="view-mode-btn active" data-view="flat" data-target="history">
                            <i class="fas fa-list me-1"></i> Seluruh Siswa
                        </button>
                    </div>
                </div>

                {{-- Filter Periode --}}
                <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap"
                    style="background:#ffffff; border:1px solid var(--border); border-radius:var(--radius-md); padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                    <div class="filter-label"
                        style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em; margin-bottom: 0;">
                        <i class="fas fa-filter"></i>
                        <span>Filter Periode:</span>
                    </div>
                    <form id="periodeFilterForm" method="GET" action="{{ route('guru.siswa') }}"
                        class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0"
                        style="gap:0.75rem;">
                        @if ($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        <input type="hidden" name="tab" value="history">
                        <select name="periode" id="periodeSelect" class="form-select"
                            style="min-width:220px; border-radius:var(--radius-sm); font-weight:600; font-size:0.85rem; border:1.5px solid var(--border); cursor:pointer;"
                            onchange="document.getElementById('periodeFilterForm').submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach ($periodeOptions as $opt)
                                <option value="{{ $opt->id_tahun_ajaran }}"
                                    {{ $periodeId == $opt->id_tahun_ajaran ? 'selected' : '' }}>
                                    {{ $opt->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        @if ($periodeId)
                            <a href="{{ route('guru.siswa', array_filter(['search' => $search, 'tab' => 'history'])) }}"
                                class="btn btn-outline-danger btn-sm"
                                style="border-radius:var(--radius-sm); font-weight:700; padding:0.45rem 1rem;"
                                title="Hapus Filter">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        @endif
                    </form>
                    @if ($periodeId)
                        @php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); @endphp
                        @if ($selectedPeriode)
                            <span class="badge bg-primary ms-auto mt-2 mt-md-0"
                                style="padding:0.5rem 1rem; border-radius:99px; font-weight:800; font-size:0.75rem;">
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
                                                <span class="badge bg-info-light text-info-dark ms-1"
                                                    style="font-size: 0.65rem; border-radius: 50px;">{{ $g['members']->count() }}
                                                    Anggota</span>
                                            @endif
                                        </h6>
                                        <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                        <div class="mt-2 text-start">
                                            <span class="badge bg-secondary text-white"
                                                style="font-size: 0.65rem; border-radius: 50px; padding: 2px 8px;">
                                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
                                            </span>
                                        </div>
                                    </div>
                                    <div class="status-wrapper">
                                        <span class="status-badge status-hadir"
                                            style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
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
                                    @if ($g['is_group'])
                                        <button class="btn-action btn-detail-group btn-show-members"
                                            data-name="{{ $g['leader']->nama }}"
                                            data-members="{{ $g['members']->toJson() }}" data-context="history"
                                            data-logbook-route="{{ route('guru.logbook', ['nisn' => ':nisn']) }}"
                                            data-absensi-route="{{ route('guru.absensi', ['nisn' => ':nisn']) }}"
                                            data-logbook-download="{{ route('guru.rekap.jurnal', ['nisn' => ':nisn']) }}"
                                            data-absensi-download="{{ route('guru.rekap.absensi', ['nisn' => ':nisn']) }}">
                                            <i class="fas fa-users"></i> Anggota Kelompok
                                        </button>
                                    @endif
                                    <div class="action-row">
                                        <button class="btn-action btn-absensi btn-preview-pdf"
                                            data-url="{{ route('guru.rekap.kelompok', $g['leader']->nisn) }}"
                                            style="width: 100%;">
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
                                                <div class="td-siswa-nisn small text-muted"><i
                                                        class="fas fa-id-card-alt me-1 opacity-50"></i>
                                                    {{ $siswa->nisn }}</div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge-school bg-light text-dark px-2 py-1 rounded small"
                                                        style="border: 1px solid #eee; font-size: 0.75rem;"><i
                                                            class="fas fa-university me-1 text-primary"></i>
                                                        {{ $siswa->sekolah }}</span>
                                                    <small class="text-muted" style="font-size: 0.7rem;"><i
                                                            class="fas fa-building me-1"></i>
                                                        {{ $siswa->perusahaan }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small text-muted" style="line-height: 1.4;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="fas fa-calendar-alt me-2 text-primary opacity-75"
                                                            style="width: 14px;"></i>
                                                        <span
                                                            style="font-size: 0.75rem;">{{ $siswa->tgl_mulai_magang ? \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->translatedFormat('d M Y') : '-' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-flag-checkered me-2 text-primary opacity-75"
                                                            style="width: 14px;"></i>
                                                        <span
                                                            style="font-size: 0.75rem;">{{ $siswa->tgl_selesai_magang ? \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->translatedFormat('d M Y') : '-' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex justify-content-end gap-2 mb-1">
                                                    <button class="btn-small btn-preview-pdf"
                                                        style="background: rgba(15, 23, 42, 0.04); color: #64748b;"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $siswa->nisn }}"
                                                        data-nama="{{ $siswa->nama }}"
                                                        data-email="{{ $siswa->email }}"
                                                        data-no_hp="{{ $siswa->no_hp }}"
                                                        data-jk="{{ $siswa->jenis_kelamin }}"
                                                        data-kelas="{{ $siswa->kelas }}"
                                                        data-jurusan="{{ $siswa->jurusan }}"
                                                        data-sekolah="{{ $siswa->sekolah }}"
                                                        data-npsn="{{ $siswa->npsn }}"
                                                        data-perusahaan="{{ $siswa->perusahaan }}"
                                                        data-tipe_magang="{{ $siswa->tipe_magang }}"
                                                        data-nisn_ketua="{{ $siswa->nisn_ketua }}"
                                                        data-surat_balasan="{{ $siswa->surat_balasan }}"
                                                        data-tahun_ajaran="{{ $siswa->tahunAjaran->tahun_ajaran ?? '-' }}"
                                                        data-mulai="{{ $siswa->tgl_mulai_magang ? \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                        data-selesai="{{ $siswa->tgl_selesai_magang ? \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                        data-guru-nama="{{ $siswa->guru->nama ?? '-' }}"
                                                        data-guru-nip="{{ $siswa->guru->id_guru ?? '-' }}"
                                                        data-guru-hp="{{ $siswa->guru->no_hp ?? '-' }}"
                                                        data-pl-nama="{{ $siswa->pembimbing->nama ?? '-' }}"
                                                        data-pl-nip="{{ $siswa->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $siswa->pembimbing->no_telp ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-id-card"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Jurnal"
                                                        data-url="{{ route('guru.rekap.jurnal', $siswa->nisn) }}"
                                                        style= "padding: 6px 19px 6px 16px;">
                                                        <i class="fas fa-book"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Cetak Absensi"
                                                        data-url="{{ route('guru.rekap.absensi', $siswa->nisn) }}">
                                                        <i class="fas fa-file-signature"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-end gap-2">
                                                    {{-- <button class="btn-small btn-preview-pdf" title="Penilaian Guru"
                                                        data-url="{{ route('guru.penilaian.export', $siswa->nisn) }}">
                                                        <i class="fas fa-star"></i>
                                                    </button> --}}
                                                    <button class="btn-small btn-preview-pdf" title="Penilaian Pembimbing"
                                                        data-url="{{ route('guru.siswa.cetakPenilaianPembimbing', $siswa->nisn) }}">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Laporan Akhir Siswa"
                                                        data-url="{{ route('guru.siswa.cetakLaporan', $siswa->nisn) }}">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                    <button class="btn-small btn-preview-pdf" title="Sertifikat Magang"
                                                        data-url="{{ route('guru.siswa.cetakSertifikat', $siswa->nisn) }}">
                                                        <i class="fas fa-certificate"></i>
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
                    <h5 class="modal-title"><i class="fas fa-users-viewfinder me-3 text-primary"></i> <span
                            id="modalGroupName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-4 py-3">
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Klik Logbook atau Absensi
                            untuk melihat detail masing-masing siswa.</p>
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

    @if (session('success'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-success border-0 shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
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

    @if (session('warning'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-warning border-0 shadow-lg"
                role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-4 text-dark"></i>
                        <div class="text-dark">
                            <div class="fw-bold">Peringatan</div>
                            <div class="small opacity-75">{{ session('warning') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    </div> {{-- End of page-wrapper --}}

    {{-- MODAL: DETAIL SISWA --}}
    <div class="modal fade" id="modalDetailSiswa" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px;">

                {{-- Header --}}
                <div class="modal-header-dark"
                    style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-bottom: none; border-radius: 24px 24px 0 0;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="modal-header-icon" style="background: rgba(255,255,255,0.1); color: #3b82f6;">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="modal-header-title text-start">
                            <h5 class="fw-bold text-white mb-0">Informasi Lengkap Siswa</h5>
                            <p class="mb-0 text-white-50 small text-start">Biodata diri dan riwayat penempatan magang
                                aktif.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Body: 2-Column Sectioned Layout --}}
                <div class="modal-form-body bg-light" style="padding: 2.5rem;">
                    <div class="row g-4">

                        {{-- ══ KOLOM KIRI: PERSONAL & AKADEMIK ════════════════ --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">

                                {{-- Section: Data Personal --}}
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-user-circle"></i> Data Personal & Kontak
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Nama Lengkap</label>
                                            <span class="detail-value text-primary" id="det_name">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">NISN Siswa</label>
                                            <span class="detail-value" id="det_nisn">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Jenis Kelamin</label>
                                            <span class="detail-value" id="det_jk">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">No. WhatsApp</label>
                                            <span class="detail-value" id="det_hp">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Alamat Email Resmi</label>
                                            <span class="detail-value fw-normal" id="det_email">-</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Data Akademik --}}
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-university"></i> Identitas Pendidikan
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Kelas & Jurusan</label>
                                            <span class="detail-value" id="det_kelas_jurusan">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item text-start">
                                            <label class="detail-label">Tahun Ajaran</label>
                                            <span class="detail-value text-info" id="det_tahun_ajaran">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">Lembaga Pendidikan Asal</label>
                                            <span class="detail-value" id="det_sekolah">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item text-start">
                                            <label class="detail-label">NPSN Sekolah</label>
                                            <span class="detail-value" id="det_npsn">-</span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ══ KOLOM KANAN: PENEMPATAN & PEMBIMBING ══════════════ --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-4">

                                {{-- Section: Penempatan Magang --}}
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-building-circle-check"></i> Penempatan & Tipe
                                    </div>
                                    <div class="row g-3 text-start">
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Instansi / Lokasi Magang</label>
                                            <span class="detail-value text-success" id="det_perusahaan">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item">
                                            <label class="detail-label">Tipe Magang</label>
                                            <span class="detail-value badge text-dark bg-light border px-3"
                                                id="det_tipe_magang"
                                                style="display:inline-block; font-size: 0.8rem; border-radius: 50px;">-</span>
                                        </div>
                                        <div class="col-md-6 detail-grid-item">
                                            <label class="detail-label">NISN Ketua (Jika Kelompok)</label>
                                            <span class="detail-value" id="det_nisn_ketua">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Durasi Waktu Magang</label>
                                            <span class="detail-value" id="det_periode">-</span>
                                        </div>
                                        <div class="col-12 detail-grid-item">
                                            <label class="detail-label">Surat Balasan / Referensi</label>
                                            <div id="det_surat_balasan_wrapper"
                                                class="mt-1 d-flex align-items-center gap-3">
                                                <span class="detail-value text-muted italic" id="det_surat_balasan">Belum
                                                    diunggah</span>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill d-none"
                                                    id="btn_view_surat">
                                                    <i class="fas fa-file-invoice me-1"></i> Preview
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Tim Pembimbing --}}
                                <div class="detail-section-block shadow-sm">
                                    <div class="detail-section-title">
                                        <i class="fas fa-user-shield"></i> Tim Pembimbing Resmi
                                    </div>
                                    <div class="d-flex flex-column gap-3">
                                        {{-- Guru --}}
                                        <div class="p-3 rounded-4 border bg-white shadow-sm">
                                            <div class="d-flex align-items-center gap-3 mb-3 text-start">
                                                <div class="icon-box-small bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 45px; height: 45px; flex-shrink: 0;">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="text-uppercase fw-bold text-primary"
                                                        style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing Sekolah
                                                    </div>
                                                    <h6 class="mb-0 fw-bold text-dark" id="det_guru_nama">-</h6>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <div class="small text-muted"><i class="fas fa-id-card me-1"></i> <span
                                                        id="det_guru_nip">-</span></div>
                                                <a href="#" id="det_guru_wa_btn"
                                                    class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    <i class="fab fa-whatsapp me-1"></i> Chat Guru
                                                </a>
                                            </div>
                                        </div>
                                        {{-- PL --}}
                                        <div class="p-3 rounded-4 border bg-white shadow-sm">
                                            <div class="d-flex align-items-center gap-3 mb-3 text-start">
                                                <div class="icon-box-small bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 45px; height: 45px; flex-shrink: 0;">
                                                    <i class="fas fa-user-tie"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="text-uppercase fw-bold text-info"
                                                        style="font-size: 0.65rem; letter-spacing: 1px;">Pembimbing
                                                        Lapangan</div>
                                                    <h6 class="mb-0 fw-bold text-dark" id="det_pl_nama">-</h6>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <div class="small text-muted"><i class="fas fa-fingerprint me-1"></i>
                                                    <span id="det_pl_nip">-</span></div>
                                                <a href="#" id="det_pl_wa_btn"
                                                    class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold"
                                                    style="font-size: 0.7rem;">
                                                    <i class="fab fa-whatsapp me-1"></i> Chat PL
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-form-footer border-0 pb-4 bg-light d-flex justify-content-center">
                    <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow"
                        data-bs-dismiss="modal">
                        Tutup Informasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL: PREVIEW PDF (Premium Style)
    ============================================================ --}}
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title">Preview Laporan</h6>
                    </div>

                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            {{-- Target removed to prevent new tab, download=1 ensures attachment response --}}
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" download>
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        <div class="vr mx-2 opacity-10"></div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>
                <div class="pdf-viewer-body bg-light">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <div class="loader-logo-container">
                                <i class="fas fa-circle-notch fa-spin fa-2x text-primary"></i>
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
@endsection

@push('scripts')
    {{-- PDF.js library --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <script src="{{ asset('assets/js/guru/daftarSiswa.js') }}?v={{ time() }}"></script>
@endpush
