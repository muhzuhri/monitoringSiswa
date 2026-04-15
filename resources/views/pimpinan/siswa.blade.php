@extends('layouts.nav.pimpinan')

@section('title', 'Data Siswa - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-siswa.css') }}">
    <style>
        .pimpinan-read-only-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        /* Override for Pimpinan specific constraints */
        .management-header { margin-bottom: 1.5rem; }
    </style>
@endpush

@section('body')
    <div class="management-container">
        <div class="admin-content-wrapper">

            {{-- HEADER --}}
            <div class="management-header">
                <div class="header-title">
                    <h5>Data Seluruh Siswa Magang</h5>
                    <p>Memantau biodata dan penempatan siswa magang secara real-time.</p>
                </div>
                <div class="header-actions">
                    <form action="{{ route('pimpinan.siswa') }}" method="GET" class="search-form" id="searchForm">
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
                    <div class="pimpinan-read-only-badge">
                        <i class="fas fa-eye"></i> Tampilan Baca-Saja
                    </div>
                </div>
            </div>

            {{-- TABS NAVIGATION --}}
            <div class="tabs-wrapper px-0">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="siswa-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-siswa"
                        type="button" role="tab" aria-controls="pane-siswa" aria-selected="true">
                        <i class="fas fa-users"></i>
                        <span>Siswa Magang ({{ $siswa->total() }})</span>
                    </button>
                    <button class="tab-button" id="history-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-riwayat"
                        type="button" role="tab" aria-controls="pane-riwayat" aria-selected="false">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Siswa ({{ $riwayatSiswas->count() }})</span>
                    </button>
                </div>
            </div>

            {{-- TAB CONTENT --}}
            <div class="tab-content">

                {{-- ==================== TAB: SISWA AKTIF ==================== --}}
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">

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
                            Menampilkan <strong>{{ $siswa->count() }}</strong> siswa aktif pada halaman ini
                        </div>
                    </div>

                    {{-- View: Per Kelompok (Cards) --}}
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                            @forelse($groupedSiswas as $g)
                                <div class="col-xl-4 col-md-6">
                                    <div class="student-card">
                                        <div class="card-actions">
                                            <button class="action-round btn-detail"
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
                                                title="Lihat Detail">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                        </div>

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
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-user-slash"></i></div>
                                        <p>Tidak ada siswa aktif ditemukan.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            {{ $siswa->links() }}
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
                                                        <i class="fas fa-eye text-primary"></i>
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

                    <div class="tab-toolbar">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="riwayat">
                                <i class="fas fa-th-large"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="riwayat">
                                <i class="fas fa-list"></i> Seluruh Riwayat
                            </button>
                        </div>
                        
                        <div class="tab-toolbar-info">
                            Menampilkan <strong>{{ $riwayatSiswas->count() }}</strong> riwayat siswa magang
                        </div>

                        {{-- Periode Filter --}}
                        <div class="ms-auto" style="min-width: 200px;">
                            <form action="{{ route('pimpinan.siswa') }}" method="GET" id="filterRiwayatForm" class="d-flex gap-2">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <select name="periode" class="p-input small-select" onchange="this.form.submit()" style="padding: 0.5rem 1rem; height: auto;">
                                    <option value="">Semua Periode</option>
                                    @foreach($periodeOptions as $p)
                                        <option value="{{ $p->id_tahun_ajaran }}" {{ $periodeId == $p->id_tahun_ajaran ? 'selected' : '' }}>
                                            {{ $p->nama_tahun_ajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    {{-- View: Riwayat Cards (Grouped) --}}
                    <div class="view-container" id="riwayat-grouped-view">
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
                                                    <span class="badge-status hadir" style="background: var(--p-bg-primary-light); color: var(--p-primary); font-size: 0.65rem; padding: 2px 10px;">
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
                                                        data-members="{{ $g['members']->toJson() }}">
                                                    <i class="fas fa-users-viewfinder"></i> Anggota Kelompok
                                                </button>
                                            @else
                                                <button class="btn-action btn-detail-group btn-detail"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                    ... (Data Attributes Same as Active Tab) ...
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
                                            
                                            <button class="btn-action btn-absensi-group btn-preview-pdf" 
                                                    data-url="{{ route('admin.rekap.kelompok', $g['leader']->nisn) }}">
                                                <i class="fas fa-file-signature"></i> Rekap Absensi Kelompok
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- View: Riwayat Table (Flat) --}}
                    <div class="view-container d-none" id="riwayat-flat-view">
                        <div class="data-table-wrapper">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Siswa Magang</th>
                                        <th>Sekolah / Instansi</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end">Aksi</th>
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
                                                    <i class="fas fa-calendar-alt me-1 text-primary opacity-50"></i>
                                                    {{ $rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-' }} - 
                                                    {{ $rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end gap-2">
                                                    <button class="btn-icon btn-detail-soft btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $rs->nisn }}" data-nama="{{ $rs->nama }}" data-email="{{ $rs->email }}"
                                                        data-no_hp="{{ $rs->no_hp }}" data-kelas="{{ $rs->kelas }}" data-jurusan="{{ $rs->jurusan }}"
                                                        data-sekolah="{{ $rs->sekolah }}" data-perusahaan="{{ $rs->perusahaan }}"
                                                        data-mulai="{{ $rs->tgl_mulai_magang ? \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                        data-selesai="{{ $rs->tgl_selesai_magang ? \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                        data-guru-nama="{{ $rs->guru->nama ?? '-' }}" data-guru-nip="{{ $rs->guru->id_guru ?? '-' }}"
                                                        data-pl-nama="{{ $rs->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $rs->pembimbing->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $rs->pembimbing->no_telp ?? '-' }}"
                                                        title="Lihat Detail">
                                                        <i class="fas fa-eye text-primary"></i>
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

    {{-- Modal Detail --}}
    @include('pimpinan.siswa_modals')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Tab Persistence ───────────────────────────────────────────
            const activeTab = localStorage.getItem('activeStudentTab_Pimpinan');
            if (activeTab) {
                const tabEl = document.querySelector(`button[data-bs-target="${activeTab}"]`);
                if (tabEl) {
                    bootstrap.Tab.getInstance(tabEl)?.show() || new bootstrap.Tab(tabEl).show();
                }
            }
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.addEventListener('shown.bs.tab', event => {
                    localStorage.setItem('activeStudentTab_Pimpinan', event.target.dataset.bsTarget);
                });
            });

            // ── View Mode Switching ────────────────────────────────────────
            function initViewMode() {
                document.querySelectorAll('.view-mode-btn').forEach(btn => {
                    const target = btn.dataset.target;
                    const view   = btn.dataset.view;
                    const savedView = localStorage.getItem(`viewMode_${target}_Pimpinan`);

                    if (savedView === view) {
                        btn.click();
                    }

                    btn.addEventListener('click', function () {
                        const v = this.dataset.view;
                        const t = this.dataset.target;
                        const pane = this.closest('.tab-pane');

                        pane.querySelectorAll('.view-mode-btn').forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        const grouped = pane.querySelector(`#${t}-grouped-view`);
                        const flat    = pane.querySelector(`#${t}-flat-view`);

                        if (v === 'grouped') {
                            grouped.classList.remove('d-none');
                            flat.classList.add('d-none');
                        } else {
                            grouped.classList.add('d-none');
                            flat.classList.remove('d-none');
                        }
                        localStorage.setItem(`viewMode_${t}_Pimpinan`, v);
                    });
                });
            }
            initViewMode();

            // ── Detail Modal Handler ───────────────────────────────────────
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

            // ── Modal Group Members (Riwayat) ─────────────────────────────
            const groupModalEl     = document.getElementById('groupMembersModal');
            const groupModal        = groupModalEl ? new bootstrap.Modal(groupModalEl) : null;
            const modalNameEl       = document.getElementById('modalGroupName');
            const modalBodyEl       = document.getElementById('modalGroupBody');

            document.querySelectorAll('.btn-show-members').forEach(button => {
                button.addEventListener('click', function() {
                    const name = this.dataset.name;
                    const members = JSON.parse(this.dataset.members);
                    
                    modalNameEl.innerText = name;
                    modalBodyEl.innerHTML = '';
                    
                    // Route patterns (Admin routes since they provide reports for Pimpinan)
                    const logDownloadBase = "{{ route('admin.rekap.jurnal', ['nisn' => ':nisn']) }}";
                    const absDownloadBase = "{{ route('admin.rekap.absensi', ['nisn' => ':nisn']) }}";

                    members.forEach((member) => {
                        const logDownload = logDownloadBase.replace(':nisn', member.nisn);
                        const absDownload = absDownloadBase.replace(':nisn', member.nisn);
                        
                        const statusBadge = `
                            <span class="badge bg-secondary-light text-muted px-2 py-1 small rounded-pill" style="font-size: 0.65rem;">
                                <i class="fas fa-flag-checkered me-1"></i> SELESAI
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
                                        <button class="btn-small btn-preview-pdf" title="Cetak Jurnal" data-url="${logDownload}" 
                                            style="background: #f0f9ff; color: #0369a1; padding: 6px 10px; border-radius: 8px; border: 1px solid #bae6fd;">
                                            <i class="fas fa-book"></i>
                                        </button>
                                        <button class="btn-small btn-preview-pdf" title="Cetak Absensi" data-url="${absDownload}" 
                                            style="background: #f0fdf4; color: #15803d; padding: 6px 10px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                            <i class="fas fa-file-signature"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        modalBodyEl.innerHTML += row;
                    });
                    
                    initPdfPreviewListeners();
                    if (groupModal) groupModal.show();
                });
            });

            // ── PDF Preview ────────────────────────────────────────────────
            const pdfModalEl   = document.getElementById('previewPdfModal');
            const pdfModal     = pdfModalEl ? new bootstrap.Modal(pdfModalEl) : null;
            const pdfIframe    = document.getElementById('pdfIframe');
            const downloadBtn  = document.getElementById('downloadPdfBtn');
            const printBtn     = document.getElementById('printPdfBtn');

            function initPdfPreviewListeners() {
                document.querySelectorAll('.btn-preview-pdf').forEach(button => {
                    button.onclick = null; 
                    button.addEventListener('click', function () {
                        const url = this.dataset.url;
                        if (!url) return;
                        pdfIframe.src          = url.includes('#') ? url : url + '#view=Fit';
                        downloadBtn.href       = url + (url.includes('?') ? '&' : '?') + 'download=1';
                        if (pdfModal) pdfModal.show();
                    });
                });
            }
            initPdfPreviewListeners();

            if (pdfModalEl) {
                printBtn.addEventListener('click', () => {
                    pdfIframe.contentWindow.print();
                });
            }
        });
    </script>
@endsection
