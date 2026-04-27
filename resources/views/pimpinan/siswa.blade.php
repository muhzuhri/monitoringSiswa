@extends('layouts.nav.pimpinan')

@section('title', 'Data Siswa - Pimpinan Dashboard')
@section('body-class', 'dashboard-page pimpinan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/siswa.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pimpinan/siswa-modals.css') }}">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="{{ asset('assets/js/pimpinan/siswa.js') }}"></script>
@endpush

@section('body')
    <div class="management-container" id="siswa-container" 
         data-jurnal-url="{{ route('admin.rekap.jurnal', ['nisn' => ':nisn']) }}"
         data-absensi-url="{{ route('admin.rekap.absensi', ['nisn' => ':nisn']) }}">
        <!-- Global Navigation Tabs: Admin, Siswa, Guru, Pembimbing -->
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

        <div class="admin-content-wrapper">

            {{-- HEADER --}}
            <div class="management-header">
                <div class="header-title d-flex align-items-center gap-3">
                    <div class="header-logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5>Data Siswa Magang</h5>
                        <p>Pantau perkembangan dan riwayat seluruh siswa magang.</p>
                    </div>
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
                                                 <i class="fas fa-eye"></i>
                                             </button>
                                         </div>
 
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
                                                 <h6>{{ Str::limit($g['leader']->nama, 25) }}</h6>
                                                 <p>NISN: {{ $g['leader']->nisn }}</p>
                                                 @if($g['is_group'])
                                                     <span class="badge-info-light">({{ $g['members']->count() }} Siswa)</span>
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
                                                         data-type="active"
                                                         data-members="{{ $g['members']->map(function($m) {
                                                             return [
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
                                                                 'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                 'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                 'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                 'status' => $m->status
                                                             ];
                                                         })->toJson() }}">
                                                     <i class="fas fa-users-viewfinder"></i> Anggota
                                                 </button>
                                             @else
                                                 <button class="btn-action detail-btn btn-detail"
                                                     data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                     data-nisn="{{ $g['leader']->nisn }}" data-nama="{{ $g['leader']->nama }}" data-email="{{ $g['leader']->email }}"
                                                     data-no_hp="{{ $g['leader']->no_hp }}" data-kelas="{{ $g['leader']->kelas }}" data-jurusan="{{ $g['leader']->jurusan }}"
                                                     data-sekolah="{{ $g['leader']->sekolah }}" data-perusahaan="{{ $g['leader']->perusahaan }}"
                                                     data-mulai="{{ $g['leader']->tgl_mulai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_mulai_magang)->format('d M Y') : '-' }}"
                                                     data-selesai="{{ $g['leader']->tgl_selesai_magang ? \Carbon\Carbon::parse($g['leader']->tgl_selesai_magang)->format('d M Y') : '-' }}"
                                                     data-guru-nama="{{ $g['leader']->guru->nama ?? '-' }}" data-guru-nip="{{ $g['leader']->id_guru ?? '-' }}"
                                                     data-pl-nama="{{ $g['leader']->pembimbing->nama ?? '-' }}" data-pl-nip="{{ $g['leader']->id_pembimbing ?? '-' }}"
                                                     data-pl-hp="{{ $g['leader']->pembimbing->no_telp ?? '-' }}">
                                                     <i class="fas fa-info-circle"></i> Detail
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
                                                         title="Lihat Detail Profil">
                                                         <i class="fas fa-id-card text-primary"></i>
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
                                                     {{ Str::limit($g['leader']->nama, 22) }}
                                                     @if($g['is_group'])
                                                         <span class="badge-kelompok">({{ $g['members']->count() }} Anggota)</span>
                                                     @endif
                                                 </h6>
                                                 <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                                 <div class="mt-1 d-flex gap-2">
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
                                         </div>
 
                                         {{-- Info Grid --}}
                                         <div class="info-grid">
                                             <div class="info-item">
                                                 <label class="info-label"><i class="fas fa-university"></i> SEKOLAH</label>
                                                 <span class="info-value" title="{{ $g['leader']->sekolah }}">{{ $g['leader']->sekolah }}</span>
                                             </div>
                                             <div class="info-item">
                                                 <label class="info-label"><i class="fas fa-building"></i> INSTANSI</label>
                                                 <span class="info-value" title="{{ $g['leader']->perusahaan }}">{{ $g['leader']->perusahaan ?? '-' }}</span>
                                             </div>
                                         </div>
 
                                         {{-- Action Buttons --}}
                                         <div class="action-grid">
                                             @if($g['is_group'])
                                                 <button class="btn-action btn-show-members" 
                                                         data-name="{{ $g['leader']->nama }}"
                                                         data-type="history"
                                                         data-members="{{ $g['members']->map(function($m) {
                                                             return [
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
                                                                 'pl_nama' => $m->pembimbing->nama ?? '-',
                                                                 'pl_nip' => $m->pembimbing->id_pembimbing ?? '-',
                                                                 'pl_hp' => $m->pembimbing->no_telp ?? '-',
                                                                 'status' => $m->status
                                                             ];
                                                         })->toJson() }}">
                                                     <i class="fas fa-users-viewfinder"></i> Anggota
                                                 </button>
                                             @else
                                                 <button class="btn-action btn-detail"
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
                                                     <i class="fas fa-user-circle"></i> Detail
                                                 </button>
                                             @endif
                                             
                                             <button class="btn-action btn-preview-pdf" 
                                                     data-url="{{ route('admin.rekap.kelompok', $g['leader']->nisn) }}">
                                                 <i class="fas fa-file-pdf"></i> Rekap
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
                                                         title="Lihat Detail Profil">
                                                         <i class="fas fa-id-card text-primary"></i>
                                                     </button>
                                                     <button class="btn-icon btn-detail-soft btn-preview-pdf" 
                                                         data-url="{{ route('admin.rekap.jurnal', $rs->nisn) }}"
                                                         title="Lihat Jurnal Individu">
                                                         <i class="fas fa-book text-info"></i>
                                                     </button>
                                                     <button class="btn-icon btn-detail-soft btn-preview-pdf" 
                                                         data-url="{{ route('admin.rekap.absensi', $rs->nisn) }}"
                                                         title="Lihat Absensi Individu">
                                                         <i class="fas fa-file-signature text-success"></i>
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
@endsection
