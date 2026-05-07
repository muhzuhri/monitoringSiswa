@extends('layouts.nav.pimpinan')

@section('title', 'Data Siswa - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/siswa.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/modals.css') }}">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="{{ asset('assets/js/pimpinan/siswa.js') }}"></script>
@endpush

@section('body')
    <div class="management-container" id="siswa-container"
        data-jurnal-url="{{ route('admin.rekap.jurnal', ['nisn' => ':nisn']) }}"
        data-absensi-url="{{ route('admin.rekap.absensi', ['nisn' => ':nisn']) }}">

        <div class="tabs-wrapper">
            <div class="tabs-nav">
                <a href="{{ route('pimpinan.admin') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.admin') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
                <a href="{{ route('pimpinan.siswa') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.siswa') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Siswa</span>
                </a>
                <a href="{{ route('pimpinan.guru') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.guru') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Guru</span>
                </a>
                <a href="{{ route('pimpinan.pembimbing') }}" class="tab-button text-decoration-none flex-fill justify-content-center text-center {{ Route::is('pimpinan.pembimbing') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>Pembimbing</span>
                </a>
            </div>
        </div>

        <div class="admin-content-wrapper shadow-sm" style="border-radius: 24px; background: #fff; overflow: hidden;">

            {{-- HEADER --}}
            <div class="management-header p-4" style="border-bottom: 1px solid rgba(0,0,0,0.05); background: #fdfdfd;">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 1.5rem;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Pemantauan Siswa Magang</h5>
                        <p class="text-muted small mb-0">Monitoring perkembangan dan riwayat seluruh siswa magang secara
                            real-time.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <form action="{{ route('pimpinan.siswa') }}" method="GET" class="search-form" id="searchForm">
                        <div class="p-input-wrapper">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" name="search" value="{{ $search }}" class="p-input with-icon"
                                style="border-radius: 12px; background: #f8fafc;"
                                placeholder="Cari Siswa / Sekolah / NISN..." onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABS NAVIGATION --}}
            <div class="px-4 pt-4">
                <div class="tabs-nav d-flex w-100 gap-2 p-1"
                    style="background: rgba(15, 23, 42, 0.03); border-radius: 16px;" role="tablist">
                    <button class="tab-button active flex-fill justify-content-center py-3" id="siswa-tab"
                        data-bs-toggle="pill" data-bs-target="#pane-siswa" type="button" role="tab"
                        style="border-radius: 12px;">
                        <i class="fas fa-users me-2"></i>
                        <span>Siswa Magang ({{ $siswa->total() }})</span>
                    </button>
                    <button class="tab-button flex-fill justify-content-center py-3" id="history-tab" data-bs-toggle="pill"
                        data-bs-target="#pane-riwayat" type="button" role="tab" style="border-radius: 12px;">
                        <i class="fas fa-history me-2"></i>
                        <span>Riwayat Siswa ({{ $riwayatSiswas->count() }})</span>
                    </button>
                </div>
            </div>

            {{-- TAB CONTENT --}}
            <div class="tab-content p-4">

                {{-- ==================== TAB: SISWA AKTIF ==================== --}}
                <div class="tab-pane fade show active" id="pane-siswa" role="tabpanel">

                    <div class="tab-toolbar mb-4">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="active">
                                <i class="fas fa-th-large me-1"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="active">
                                <i class="fas fa-list me-1"></i> Seluruh Siswa
                            </button>
                        </div>
                        <div class="tab-toolbar-info text-muted">
                            <i class="fas fa-info-circle me-1"></i> Menampilkan <strong>{{ $siswa->count() }}</strong>
                            siswa aktif pada halaman ini
                        </div>
                    </div>

                    {{-- View: Per Kelompok (Cards) --}}
                    <div class="view-container" id="active-grouped-view">
                        <div class="row g-4">
                            @forelse($groupedSiswas as $g)
                                <div class="col-xl-4 col-md-6">
                                    <div class="student-card border-0 shadow-sm hover-elevate"
                                        style="border-radius: 20px; transition: all 0.3s ease;">
                                        <div class="card-actions">
                                            <button class="btn-premium-circle btn-view-p btn-detail" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailSiswa" data-nisn="{{ $g['leader']->nisn }}"
                                                data-nama="{{ $g['leader']->nama }}"
                                                data-email="{{ $g['leader']->email }}"
                                                data-no_hp="{{ $g['leader']->no_hp }}"
                                                data-kelas="{{ $g['leader']->kelas }}"
                                                data-jurusan="{{ $g['leader']->jurusan }}"
                                                data-sekolah="{{ $g['leader']->sekolah }}"
                                                data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                data-jk="{{ $g['leader']->jenis_kelamin }}"
                                                data-npsn="{{ $g['leader']->npsn }}"
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
                                                data-pl-nip="{{ $g['leader']->pembimbing->id_pembimbing ?? '-' }}"
                                                data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                                title="Lihat Detail Profil">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <div class="card-identity">
                                            <div class="card-avatar"
                                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #fff;">
                                                @if ($g['is_group'])
                                                    <div class="avatar-group-icon">
                                                        <i class="fas fa-user-friends"></i>
                                                    </div>
                                                @else
                                                    <span
                                                        class="fw-bold">{{ strtoupper(substr($g['leader']->nama, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                            <div class="card-identity-info">
                                                <h6 class="fw-bold mb-1">{{ Str::limit($g['leader']->nama, 25) }}</h6>
                                                <p class="text-muted small mb-0">NISN: {{ $g['leader']->nisn }}</p>
                                                @if ($g['is_group'])
                                                    <span class="badge-kelompok mt-1">Kelompok
                                                        ({{ $g['members']->count() }} Siswa)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-info-list mt-3">
                                            <div class="card-info-row">
                                                <span class="card-info-label">Sekolah</span>
                                                <span class="card-info-value fw-bold"
                                                    title="{{ $g['leader']->sekolah }}">{{ Str::limit($g['leader']->sekolah, 22) }}</span>
                                            </div>
                                            <div class="card-info-row">
                                                <span class="card-info-label">Penempatan</span>
                                                <span
                                                    class="card-info-value text-primary fw-bold">{{ Str::limit($g['leader']->perusahaan ?? 'Belum ada', 22) }}</span>
                                            </div>
                                        </div>

                                        <div class="card-footer-bar mt-3 pt-3"
                                            style="border-top: 1px dashed rgba(0,0,0,0.1);">
                                            @if ($g['leader']->absen_hari_ini)
                                                <span class="status-label hadir">
                                                    <span class="status-dot hadir"></span> Hadir
                                                </span>
                                            @else
                                                <span class="status-label belum">
                                                    <span class="status-dot belum"></span> Menunggu
                                                </span>
                                            @endif
                                            <span class="card-guru-info text-muted extra-small">
                                                <i class="fas fa-chalkboard-teacher me-1"></i>
                                                {{ Str::limit($g['leader']->guru->nama ?? '-', 15) }}
                                            </span>
                                        </div>

                                        <div class="action-grid mt-3">
                                            @if ($g['is_group'])
                                                <button class="btn-action btn-show-members"
                                                    style="background: #f1f5f9; color: #475569; border: none; border-radius: 10px; width: 100%; padding: 10px; font-weight: 600;"
                                                    data-name="{{ $g['leader']->nama }}" data-type="active"
                                                    data-members="{{ $g['members']->map(function ($m) {
                                                            return [
                                                                'nism' => $m->nisn,
                                                                'nisn' => $m->nisn,
                                                                'nama' => $m->nama,
                                                                'email' => $m->email,
                                                                'no_hp' => $m->no_hp,
                                                                'kelas' => $m->kelas,
                                                                'jurusan' => $m->jurusan,
                                                                'sekolah' => $m->sekolah,
                                                                'perusahaan' => $m->perusahaan,
                                                                'mulai' => $m->tgl_mulai_magang ? \Carbon\Carbon::parse($m->tgl_mulai_magang)->format('d M Y') : '-',
                                                                'selesai' => $m->tgl_selesai_magang ? \Carbon\Carbon::parse($m->tgl_selesai_magang)->format('d M Y') : '-',
                                                                'guru_nama' => $m->guru->nama ?? '-',
                                                                'guru_nip' => $m->guru->id_guru ?? '-',
                                                                'guru_hp' => $m->guru->no_hp ?? '-',
                                                                'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                'jk' => $m->jenis_kelamin,
                                                                'npsn' => $m->npsn,
                                                                'tipe_magang' => $m->tipe_magang,
                                                                'nisn_ketua' => $m->nisn_ketua,
                                                                'surat_balasan' => $m->surat_balasan,
                                                                'tahun_ajaran' => $m->tahunAjaran->tahun_ajaran ?? '-',
                                                                'status' => $m->status,
                                                            ];
                                                        })->toJson() }}">
                                                    <i class="fas fa-users-viewfinder me-2"></i> Lihat Anggota
                                                </button>
                                            @else
                                                <button class="btn-action detail-btn btn-detail"
                                                    style="background: #e0f2fe; color: #0369a1; border: none; border-radius: 10px; width: 100%; padding: 10px; font-weight: 600;"
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
                        <div class="data-table-wrapper shadow-none border" style="border-radius: 16px; overflow: hidden;">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Pendidikan & Instansi</th>
                                        <th>Status Presensi</th>
                                        <th>Pembimbing</th>
                                        <th class="text-end">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswa as $s)
                                        <tr>
                                            <td>
                                                <div class="cell-name fw-bold">{{ $s->nama }}</div>
                                                <div class="cell-sub text-muted font-monospace small">{{ $s->nisn }}
                                                    &middot; {{ $s->kelas }}</div>
                                            </td>
                                            <td>
                                                <div class="cell-sub text-dark fw-medium"><i
                                                        class="fas fa-university me-1 text-primary"></i>
                                                    {{ Str::limit($s->sekolah, 30) }}</div>
                                                <div class="cell-sub text-muted small"><i
                                                        class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $s->perusahaan ?? '-' }}</div>
                                            </td>
                                            <td>
                                                @if ($s->absen_hari_ini)
                                                    <span class="badge-status hadir">
                                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                                    </span>
                                                @else
                                                    <span class="badge-status belum">
                                                        <i class="fas fa-clock me-1"></i> Menunggu
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="cell-sub small">
                                                    <i class="fas fa-graduation-cap me-1 opacity-50"></i>
                                                    {{ Str::limit($s->guru->nama ?? '-', 20) }}
                                                </div>
                                                <div class="cell-sub small">
                                                    <i class="fas fa-user-tie me-1 opacity-50"></i>
                                                    {{ Str::limit($s->pembimbing->nama ?? '-', 20) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end">
                                                    <button class="btn-premium-circle btn-view-p btn-detail"
                                                        data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                        data-nisn="{{ $s->nisn }}"
                                                        data-nama="{{ $s->nama }}"
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
                                                        style="background: var(--primary-light); color: var(--primary); border: none;">
                                                        <i class="fas fa-id-card"></i> 
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-5 text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                                                <p>Tidak ada data siswa aktif yang ditemukan.</p>
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

                    <div class="tab-toolbar mb-4">
                        <div class="view-mode-wrapper">
                            <button class="view-mode-btn active" data-view="grouped" data-target="riwayat">
                                <i class="fas fa-th-large me-1"></i> Per Kelompok
                            </button>
                            <button class="view-mode-btn" data-view="flat" data-target="riwayat">
                                <i class="fas fa-list me-1"></i> Seluruh Riwayat
                            </button>
                        </div>

                        {{-- Periode Filter --}}
                        <div class="ms-auto d-flex align-items-center gap-3">
                            <span class="filter-label text-muted small fw-bold">
                                <i class="fas fa-filter"></i> Periode:
                            </span>
                            <form action="{{ route('pimpinan.siswa') }}" method="GET" id="filterRiwayatForm">
                                <input type="hidden" name="search" value="{{ $search }}">
                                <select name="periode" class="p-input small-select" onchange="this.form.submit()"
                                    style="padding: 0.5rem 2.5rem 0.5rem 1rem; height: auto; border-radius: 10px; font-size: 0.85rem;">
                                    <option value="">Semua Periode</option>
                                    @foreach ($periodeOptions as $p)
                                        <option value="{{ $p->id_tahun_ajaran }}"
                                            {{ $periodeId == $p->id_tahun_ajaran ? 'selected' : '' }}>
                                            {{ $p->tahun_ajaran }}
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
                                    <div class="history-card border-0 shadow-sm"
                                        style="border-radius: 20px; background: #fff;">
                                        {{-- Header --}}
                                        <div class="student-header p-3 pb-0">
                                            <div class="student-avatar"
                                                style="background: #f1f5f9; color: #64748b; font-weight: 700;">
                                                @if ($g['is_group'])
                                                    <i class="fas fa-layer-group"></i>
                                                @else
                                                    {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div class="student-meta">
                                                <h6 class="student-name fw-bold mb-0">
                                                    {{ Str::limit($g['leader']->nama, 22) }}
                                                </h6>
                                                @if ($g['is_group'])
                                                    <span
                                                        class="badge bg-info-light text-info-dark extra-small mt-1">{{ $g['members']->count() }}
                                                        Anggota</span>
                                                @endif
                                                <p class="student-nisn small text-muted mt-1 mb-0">NISN:
                                                    {{ $g['leader']->nisn }}</p>
                                            </div>
                                            <div class="status-wrapper">
                                                <span class="badge-archive">
                                                    <i class="fas fa-archive"></i> Arsip
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Info Grid --}}
                                        <div class="info-grid mt-2">
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-school"></i> SEKOLAH</label>
                                                <span class="info-value fw-medium"
                                                    title="{{ $g['leader']->sekolah }}">{{ Str::limit($g['leader']->sekolah, 25) }}</span>
                                            </div>
                                            <div class="info-item">
                                                <label class="info-label"><i class="fas fa-building"></i> INSTANSI</label>
                                                <span
                                                    class="info-value text-success fw-bold">{{ $g['leader']->perusahaan ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="action-grid p-3">
                                            @if ($g['is_group'])
                                                <button class="btn-action btn-show-members"
                                                    style="background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
                                                    data-name="{{ $g['leader']->nama }}" data-type="history"
                                                    data-members="{{ $g['members']->map(function ($m) {
                                                            return [
                                                                'nism' => $m->nisn,
                                                                'nisn' => $m->nisn,
                                                                'nama' => $m->nama,
                                                                'email' => $m->email,
                                                                'no_hp' => $m->no_hp,
                                                                'kelas' => $m->kelas,
                                                                'jurusan' => $m->jurusan,
                                                                'sekolah' => $m->sekolah,
                                                                'perusahaan' => $m->perusahaan,
                                                                'mulai' => $m->tgl_mulai_magang ? \Carbon\Carbon::parse($m->tgl_mulai_magang)->format('d M Y') : '-',
                                                                'selesai' => $m->tgl_selesai_magang ? \Carbon\Carbon::parse($m->tgl_selesai_magang)->format('d M Y') : '-',
                                                                'guru_nama' => $m->guru->nama ?? '-',
                                                                'guru_nip' => $m->guru->id_guru ?? '-',
                                                                'guru_hp' => $m->guru->no_hp ?? '-',
                                                                'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                'jk' => $m->jenis_kelamin,
                                                                'npsn' => $m->npsn,
                                                                'tipe_magang' => $m->tipe_magang,
                                                                'nisn_ketua' => $m->nisn_ketua,
                                                                'surat_balasan' => $m->surat_balasan,
                                                                'tahun_ajaran' => $m->tahunAjaran->tahun_ajaran ?? '-',
                                                                'status' => $m->status,
                                                            ];
                                                        })->toJson() }}">
                                                    <i class="fas fa-users-viewfinder me-2"></i> Anggota
                                                </button>
                                            @else
                                                <button class="btn-action detail-btn btn-detail"
                                                    style="background: #f0f9ff; color: #036ae0; border: 1px solid #bae6fd; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
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
                                                    data-guru-hp="{{ $g['leader']->guru->no_hp ?? '-' }}"
                                                    data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}"
                                                    data-pl-nip="{{ $g['leader']->id_pembimbing ?? '-' }}"
                                                    data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}"
                                                    style="background: var(--primary-light); color: var(--primary); border: none;">
                                                    <i class="fas fa-id-card"></i> Detail Profil
                                                </button>
                                            @endif

                                            <button class="btn-action btn-preview-pdf"
                                                style="background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; border-radius: 10px; width: 100%; padding: 8px; font-weight: 600;"
                                                data-url="{{ route('admin.rekap.kelompok', $g['leader']->nisn) }}">
                                                <i class="fas fa-file-pdf me-2"></i> Rekap Bln
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                                        <p>Belum ada riwayat siswa pada periode ini.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- View: Riwayat Table (Flat) --}}
                    <div class="view-container d-none" id="riwayat-flat-view">
                        <div class="data-table-wrapper border shadow-none" style="border-radius: 16px; overflow: hidden;">
                            <table class="main-table">
                                <thead>
                                    <tr>
                                        <th>Identitas Siswa</th>
                                        <th>Pendidikan / Instansi</th>
                                        <th>Periode Magang</th>
                                        <th class="text-end">Opsi & Rekap</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($riwayatSiswas as $rs)
                                        <tr>
                                            <td>
                                                <div class="cell-name fw-bold">{{ $rs->nama }}</div>
                                                <div class="cell-sub text-muted small">NISN: {{ $rs->nisn }}</div>
                                            </td>
                                            <td>
                                                <div class="cell-name text-dark font-weight-600">
                                                    {{ Str::limit($rs->sekolah, 25) }}</div>
                                                <div class="cell-sub text-muted small"><i
                                                        class="fas fa-building me-1 opacity-50"></i>
                                                    {{ $rs->perusahaan ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="period-display">
                                                    <div class="period-item small">
                                                        <i class="fas fa-calendar-alt text-primary opacity-50 me-1"></i>
                                                        {{ \Carbon\Carbon::parse($rs->tgl_mulai_magang)->format('d/m/y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($rs->tgl_selesai_magang)->format('d/m/y') }}
                                                    </div>
                                                    <div class="cell-sub text-info extra-small fw-bold">
                                                        {{ $rs->tahunAjaran->tahun_ajaran ?? '-' }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="action-group justify-content-end gap-2">
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
                                                        data-pl-nip="{{ $rs->id_pembimbing ?? '-' }}"
                                                        data-pl-hp="{{ $rs->pembimbing->no_telp ?? '-' }}"
                                                        style="background: var(--primary-light); color: var(--primary); border: none;">
                                                        <i class="fas fa-id-card"></i> 
                                                    </button>
                                                    <button class="btn-premium-circle btn-jurnal-p btn-preview-pdf"
                                                        data-url="{{ route('admin.rekap.jurnal', $rs->nisn) }}"
                                                        title="Rekap Jurnal Kegiatan">
                                                        <i class="fas fa-book-open"></i>
                                                    </button>
                                                    <button class="btn-premium-circle btn-absensi-p btn-preview-pdf"
                                                        data-url="{{ route('admin.rekap.absensi', $rs->nisn) }}"
                                                        title="Rekap Presensi Harian">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                <i class="fas fa-history fa-3x mb-3 opacity-20"></i>
                                                <p>Belum ada riwayat siswa tersedia pada kriteria ini.</p>
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
    @include('pimpinan.modals')
@endsection
