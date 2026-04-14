@extends('layouts.nav.admin')

@section('title', 'Manajemen Siswa - Monitoring Siswa Magang')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
@endpush

@section('body')
    <div class="management-container">
        <div class="admin-content-wrapper">

            {{-- ============================================================
                 HEADER
            ============================================================ --}}
            <div class="management-header">
                <div class="header-title">
                    <h5>Manajemen Siswa</h5>
                    <p>Kelola data seluruh siswa magang dan riwayat mereka.</p>
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
                    <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Siswa</span>
                    </button>
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
                </div>
            </div>

            {{-- ============================================================
                 TAB CONTENT
            ============================================================ --}}
            <div class="tab-content">

                {{-- ==================== TAB: SISWA AKTIF ==================== --}}
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">

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
                                            <button class="action-round btn-edit"
                                                data-bs-toggle="modal" data-bs-target="#modalEditSiswa"
                                                data-id="{{ $g['leader']->nisn }}"
                                                data-nama="{{ $g['leader']->nama }}"
                                                data-email="{{ $g['leader']->email }}"
                                                data-kelas="{{ $g['leader']->kelas }}"
                                                data-jurusan="{{ $g['leader']->jurusan }}"
                                                data-sekolah="{{ $g['leader']->sekolah }}"
                                                data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                data-guru-nip="{{ $g['leader']->id_guru }}"
                                                data-pl-nip="{{ $g['leader']->id_pembimbing }}"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-round btn-delete-trigger"
                                                data-bs-toggle="modal" data-bs-target="#modalHapus"
                                                data-url="{{ route('admin.destroySiswa', $g['leader']->nisn) }}"
                                                title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>

                                        {{-- Identity --}}
                                        <div class="card-identity">
                                            <div class="card-avatar">
                                                @if($g['is_group'])
                                                    <i class="fas fa-users"></i>
                                                @else
                                                    {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
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

                                        {{-- Info List --}}
                                        <div class="card-info-list">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span class="card-info-value">{{ $g['leader']->sekolah }}</span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span class="card-info-value">{{ $g['leader']->perusahaan ?? 'Belum ada' }}</span>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
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
                                                {{ Str::limit($g['leader']->guru->nama ?? '-', 18) }}
                                            </span>
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
                                        <div class="history-card-identity">
                                            <div class="history-avatar">
                                                <i class="fas {{ $g['is_group'] ? 'fa-layer-group' : 'fa-user' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6>{{ $g['leader']->nama }}</h6>
                                                <small>{{ $g['leader']->nisn }}</small>
                                            </div>
                                            <span class="badge-selesai">Selesai</span>
                                        </div>

                                        <div class="card-info-list mb-3">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Asal Sekolah</span>
                                                <span class="card-info-value">{{ $g['leader']->sekolah }}</span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Perusahaan</span>
                                                <span class="card-info-value">{{ $g['leader']->perusahaan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="history-actions">
                                            <button class="btn-history-action btn-absensi btn-preview-pdf"
                                                data-url="{{ route('admin.rekap.kelompok', $g['leader']->nisn) }}"
                                                title="Rekap Absensi Kelompok">
                                                <i class="fas fa-calendar-check"></i>
                                            </button>
                                            <button class="btn-history-action btn-kegiatan btn-preview-pdf"
                                                data-url="{{ route('admin.rekap.jurnal', $g['leader']->nisn) }}"
                                                title="Rekap Kegiatan">
                                                <i class="fas fa-book"></i>
                                            </button>
                                            <button class="btn-history-action btn-detail-h btn-detail"
                                                data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                data-nisn="{{ $g['leader']->nisn }}" data-nama="{{ $g['leader']->nama }}"
                                                data-email="{{ $g['leader']->email }}" data-no_hp="{{ $g['leader']->no_hp }}"
                                                data-kelas="{{ $g['leader']->kelas }}" data-jurusan="{{ $g['leader']->jurusan }}"
                                                data-sekolah="{{ $g['leader']->sekolah }}" data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}" data-guru-nip="{{ $g['leader']->guru->id_guru ?? '-' }}"
                                                data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
                                                data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                                title="Detail Profil">
                                                <i class="fas fa-info-circle"></i>
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
                                                        title="Lihat Rekap Kegiatan">
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

            </div>{{-- /tab-content --}}
        </div>{{-- /admin-content-wrapper --}}
    </div>{{-- /management-container --}}

    {{-- ============================================================
         MODAL: PREVIEW PDF
    ============================================================ --}}
    <div class="modal fade" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
            <div class="modal-content h-100">
                <div class="pdf-modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="pdf-modal-icon-wrap">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div>
                            <p class="pdf-modal-title">Pratinjau Dokumen</p>
                            <p class="pdf-modal-subtitle">Gunakan tombol di atas untuk cetak atau unduh.</p>
                        </div>
                    </div>
                    <div class="pdf-modal-actions">
                        <button type="button" id="printPdfBtn" class="btn-pdf-print">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                        <a id="downloadPdfBtn" href="#" class="btn-pdf-download" target="_blank">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                        <button type="button" class="btn-close ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="pdf-modal-body flex-grow-1">
                    <iframe id="pdfIframe" src="" width="100%" height="100%" frameborder="0"></iframe>
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
            document.querySelectorAll('.btn-detail').forEach(button => {
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

                    document.getElementById('det_guru_nama').textContent = this.dataset.guruNama;
                    document.getElementById('det_guru_nip').textContent  = this.dataset.guruNip;
                    document.getElementById('det_pl_nama').textContent   = this.dataset.plNama;
                    document.getElementById('det_pl_nip').textContent    = this.dataset.plNip;
                    document.getElementById('det_pl_hp').textContent     = this.dataset.plHp;
                });
            });

            // ── Delete Modal ───────────────────────────────────────────────
            const deleteForm = document.getElementById('formHapus');
            document.querySelectorAll('.btn-delete-trigger').forEach(button => {
                button.addEventListener('click', function () {
                    deleteForm.action = this.dataset.url;
                });
            });

            // ── PDF Preview ────────────────────────────────────────────────
            const pdfModalEl   = document.getElementById('previewPdfModal');
            const pdfModal     = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
            const pdfIframe    = document.getElementById('pdfIframe');
            const downloadBtn  = document.getElementById('downloadPdfBtn');
            const printBtn     = document.getElementById('printPdfBtn');

            document.querySelectorAll('.btn-preview-pdf').forEach(button => {
                button.addEventListener('click', function () {
                    const url = this.dataset.url;
                    if (!url) return;
                    pdfIframe.src          = url.includes('#') ? url : url + '#view=Fit';
                    downloadBtn.href       = url + (url.includes('?') ? '&' : '?') + 'download=1';
                    if (pdfModal) pdfModal.show();
                });
            });

            if (pdfModalEl) {
                printBtn.addEventListener('click', () => {
                    pdfIframe.contentWindow.focus();
                    pdfIframe.contentWindow.print();
                });
                pdfModalEl.addEventListener('hidden.bs.modal', () => {
                    pdfIframe.src = '';
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
@endsection