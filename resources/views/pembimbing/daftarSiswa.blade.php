@extends('layouts.nav.pembimbing')

@section('title', 'Daftar Siswa Bimbingan - SIM Magang')
@section('body-class', 'pembimbing-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/daftarSiswa.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/detail-modal.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">
        {{-- Header Halaman --}}
        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title"><i class="fas fa-users text-primary me-2"></i>Daftar Siswa Bimbingan</h2>
                <p class="page-subtitle">Kelola dan pantau seluruh siswa magang di bawah bimbingan Anda.</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <form id="searchForm" class="search-form">
                        <span class="search-icon"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" value="{{ $search }}" class="search-input"
                            placeholder="Cari Nama, NISN, atau Perusahaan..." autocomplete="off">
                    </form>
                </div>
            </div>
        </div>

        {{-- Tab Navigasi --}}
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav">
                <button class="tab-button active btn-tab-trigger" data-target="active-students">
                    <i class="fas fa-user-clock"></i>
                    <span>Siswa Bimbingan ({{ $siswasActive->count() }})</span>
                </button>
                <button class="tab-button btn-tab-trigger" data-target="history-students" id="btnTabHistory">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Siswa ({{ $siswasHistory->count() }})</span>
                </button>
            </div>
        </div>

        <div class="tab-contents">
            {{-- Tab Siswa Bimbingan (Aktif) --}}
            <div class="tab-pane-content" id="active-students">
                <div class="content-card">
                    <div class="custom-table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Info Siswa</th>
                                    <th width="20%">Sekolah / Perusahaan</th>
                                    <th width="15%" class="text-center">Status Hari Ini</th>
                                    <th width="25%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="activeTableBody">
                                @forelse($siswasActive as $index => $siswa)
                                    <tr class="student-row">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="user-info-cell">
                                                @if($siswa->foto_profil)
                                                    <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="{{ $siswa->nama }}" class="avatar-sm">
                                                @else
                                                    <div class="avatar-sm-placeholder bg-blue-light text-primary">
                                                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="user-details">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                                                        <span class="badge bg-success text-white" style="font-size: 0.6rem; padding: 1px 6px; border-radius: 50px;">AKTIF</span>
                                                    </div>
                                                    <span class="text-muted small">NISN: {{ $siswa->nisn }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge-role bg-purple-light text-purple"><i class="fas fa-school me-1"></i> {{ $siswa->sekolah }}</span>
                                                <span class="small text-muted"><i class="fas fa-building me-1"></i> {{ $siswa->perusahaan }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($siswa->absen_hari_ini)
                                                <span class="status-badge status-hadir text-success"><i class="fas fa-check-circle"></i> Hadir</span>
                                            @else
                                                <span class="status-badge status-absen text-danger"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn-small btn-preview-pdf"
                                                    style="background: rgba(15, 23, 42, 0.04); color: #64748b;"
                                                    data-bs-toggle="modal" data-bs-target="#modalDetailSiswa"
                                                    data-nisn="{{ $siswa->nisn }}" data-nama="{{ $siswa->nama }}"
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
                                                <a href="{{ route('pembimbing.logbook', $siswa->nisn) }}" class="btn-small btn-preview-pdf"
                                                    style="background: var(--primary-light); color: var(--primary);"
                                                    title="Logbook">
                                                    <i class="fas fa-book"></i>
                                                </a>
                                                <a href="{{ route('pembimbing.absensi', $siswa->nisn) }}" class="btn-small btn-preview-pdf"
                                                    style="background: var(--warning-light); color: #92400e;"
                                                    title="Absensi">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-users-slash empty-icon"></i>
                                                <h4>Belum Ada Siswa</h4>
                                                <p class="text-muted">Tidak ditemukan siswa bimbingan yang aktif.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsActive" style="display: none;">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-search empty-icon"></i>
                                            <h4>Tidak Ditemukan</h4>
                                            <p class="text-muted">Tidak ada siswa yang cocok dengan pencarian di tab ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab Riwayat Siswa (Selesai/Sudah Dinilai) --}}
            <div class="tab-pane-content" id="history-students" style="display: none;">

                {{-- Filter Periode --}}
                <div class="history-filter-bar">
                    <div class="filter-label">
                        <i class="fas fa-filter"></i>
                        <span>Filter Periode:</span>
                    </div>
                    <form id="periodeFilterForm" method="GET" action="{{ route('pembimbing.siswa') }}" class="filter-form">
                        @if($search)
                            <input type="hidden" name="search" value="{{ $search }}">
                        @endif
                        {{-- hidden input agar tab riwayat terbuka otomatis --}}
                        <input type="hidden" name="tab" value="history">
                        <select name="periode" id="periodeSelect" class="filter-select" onchange="document.getElementById('periodeFilterForm').submit()">
                            <option value="">-- Semua Periode --</option>
                            @foreach($periodeOptions as $opt)
                                <option value="{{ $opt->id_tahun_ajaran }}"
                                    {{ $periodeId == $opt->id_tahun_ajaran ? 'selected' : '' }}>
                                    {{ $opt->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                        @if($periodeId)
                            <a href="{{ route('pembimbing.siswa', array_filter(['search' => $search, 'tab' => 'history'])) }}"
                               class="btn-reset-filter" title="Hapus Filter">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        @endif
                    </form>
                    @if($periodeId)
                        @php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); @endphp
                        @if($selectedPeriode)
                            <span class="filter-active-badge">
                                <i class="fas fa-calendar-alt"></i>
                                {{ $selectedPeriode->tahun_ajaran }}
                            </span>
                        @endif
                    @endif
                </div>

                <div class="content-card">
                    <div class="custom-table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="28%">Info Siswa</th>
                                    <th width="20%">Sekolah / Perusahaan</th>
                                    <th width="17%" class="text-center">Periode Magang</th>
                                    <th width="12%" class="text-center">Rata-rata Nilai</th>
                                    <th width="18%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="historyTableBody">
                                @forelse($siswasHistory as $index => $siswa)
                                    @php
                                        $penilaian = $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                    @endphp
                                    <tr class="student-row">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="user-info-cell">
                                                @if($siswa->foto_profil)
                                                    <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="{{ $siswa->nama }}" class="avatar-sm">
                                                @else
                                                    <div class="avatar-sm-placeholder bg-success-light text-success">
                                                        {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="user-details">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                                                        <span class="badge bg-secondary text-white" style="font-size: 0.6rem; padding: 1px 6px; border-radius: 50px;">SELESAI</span>
                                                    </div>
                                                    <span class="text-muted small">NISN: {{ $siswa->nisn }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge-role bg-purple-light text-purple"><i class="fas fa-school me-1"></i> {{ $siswa->sekolah }}</span>
                                                <span class="small text-muted"><i class="fas fa-building me-1"></i> {{ $siswa->perusahaan }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($siswa->tahunAjaran)
                                                <span class="periode-badge">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    {{ $siswa->tahunAjaran->tahun_ajaran }}
                                                </span>
                                            @elseif($siswa->tgl_mulai_magang && $siswa->tgl_selesai_magang)
                                                <span class="periode-badge periode-badge-plain">
                                                    {{ \Carbon\Carbon::parse($siswa->tgl_mulai_magang)->format('M Y') }}
                                                    &ndash;
                                                    {{ \Carbon\Carbon::parse($siswa->tgl_selesai_magang)->format('M Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($penilaian)
                                                <span class="eval-badge bg-purple-light text-purple">{{ number_format($penilaian->rata_rata, 1) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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
                                                    data-url="{{ route('pembimbing.siswa.cetakJurnal', $siswa->nisn) }}"
                                                    style= "padding: 6px 16px 6px 16px;">
                                                    <i class="fas fa-book"></i>
                                                </button>
                                                <button class="btn-small btn-preview-pdf" title="Cetak Absensi"
                                                    data-url="{{ route('pembimbing.siswa.cetakAbsensi', $siswa->nisn) }}">
                                                    <i class="fas fa-file-signature"></i>
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn-small btn-preview-pdf" title="Penilaian Guru"
                                                    data-url="{{ route('pembimbing.siswa.cetakPenilaianGuru', $siswa->nisn) }}">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                {{-- <button class="btn-small btn-preview-pdf" title="Penilaian Pembimbing"
                                                    data-url="{{ route('pembimbing.laporan.cetak', $siswa->nisn) }}">
                                                    <i class="fas fa-user-check"></i>
                                                </button> --}}
                                                <button class="btn-small btn-preview-pdf" title="Laporan Akhir Siswa"
                                                    data-url="{{ route('pembimbing.siswa.cetakLaporan', $siswa->nisn) }}">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                                <button class="btn-small btn-preview-pdf" title="Sertifikat Magang"
                                                    data-url="{{ route('pembimbing.siswa.cetakSertifikat', $siswa->nisn) }}">
                                                    <i class="fas fa-certificate"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-history empty-icon"></i>
                                                <h4>Belum Ada Riwayat</h4>
                                                <p class="text-muted">
                                                    @if($periodeId)
                                                        Tidak ada siswa yang menyelesaikan magang pada periode ini.
                                                    @else
                                                        Belum ada siswa yang menyelesaikan bimbingan/penilaian.
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsHistory" style="display: none;">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-search empty-icon"></i>
                                            <h4>Tidak Ditemukan</h4>
                                            <p class="text-muted">Tidak ada siswa yang cocok dengan pencarian di tab ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Siswa (Standard Premium) --}}
    <div class="modal fade" id="modalDetailSiswa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-content-premium">
                <div class="modal-header modal-header-premium border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-premium">
                    {{-- Header Card --}}
                    <div class="detail-header-card shadow">
                        <div class="d-flex align-items-center gap-4 flex-wrap flex-md-nowrap">
                            <div class="avatar-container-premium">
                                <div class="avatar-lg-placeholder bg-white text-primary" id="det_avatar_placeholder">
                                    S
                                </div>
                                <img id="det_avatar_img" src="" alt="Profile" class="avatar-img-premium" style="display: none;">
                            </div>
                            <div class="student-main-info">
                                <div class="text-uppercase fw-bold text-white-50 small mb-1" style="letter-spacing: 2px;">Data Profil Siswa</div>
                                <h4 class="text-white" id="det_name">-</h4>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="badge-nisn-premium" id="det_nisn">-</span>
                                    <span class="badge bg-white text-primary rounded-pill px-3 fw-bold" id="det_jk" style="font-size: 0.7rem;">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Data Personal --}}
                        <div class="col-lg-6">
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-user"></i> Data Personal & Kontak
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Alamat Email Resmi</label>
                                        <span class="detail-value text-break" id="det_email">-</span>
                                    </div>
                                    <div class="col-6 detail-grid-item">
                                        <label class="detail-label">No. WhatsApp</label>
                                        <span class="detail-value" id="det_hp">-</span>
                                    </div>
                                    <div class="col-6 detail-grid-item">
                                        <label class="detail-label">Kelas / Jurusan</label>
                                        <span class="detail-value" id="det_kelas_jurusan">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Akademik --}}
                        <div class="col-lg-6">
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-graduation-cap"></i> Instansi & Akademik
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Asal Sekolah</label>
                                        <span class="detail-value" id="det_sekolah">-</span>
                                        <div class="small text-muted mt-1">NPSN: <span id="det_npsn">-</span></div>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Tahun Ajaran</label>
                                        <span class="detail-value text-info" id="det_tahun_ajaran">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Penempatan --}}
                        <div class="col-lg-7">
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-building"></i> Penempatan & Tipe
                                </div>
                                <div class="row g-3">
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Instansi / Lokasi Magang</label>
                                        <span class="detail-value" id="det_perusahaan">-</span>
                                    </div>
                                    <div class="col-6 detail-grid-item">
                                        <label class="detail-label">Tipe Magang</label>
                                        <span class="detail-value" id="det_tipe_magang">-</span>
                                    </div>
                                    <div class="col-6 detail-grid-item">
                                        <label class="detail-label">NISN Ketua (Jika Kelompok)</label>
                                        <span class="detail-value" id="det_nisn_ketua">-</span>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Durasi Waktu Magang</label>
                                        <span class="detail-value text-primary" id="det_periode">-</span>
                                    </div>
                                    <div class="col-12 detail-grid-item">
                                        <label class="detail-label">Surat Balasan / Referensi</label>
                                        <div id="det_surat_balasan_wrapper" class="mt-1 d-flex align-items-center gap-3">
                                            <span class="detail-value text-muted italic" id="det_surat_balasan">Belum diunggah</span>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill d-none" id="btn_view_surat">
                                                <i class="fas fa-file-invoice me-1"></i> Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pembimbing --}}
                        <div class="col-lg-5">
                            <div class="detail-section-block shadow-sm">
                                <div class="detail-section-title">
                                    <i class="fas fa-chalkboard-teacher"></i> Tim Pembimbing
                                </div>
                                <div class="d-flex flex-column gap-3 mt-2">
                                    <div class="p-3 rounded-4 border bg-white shadow-sm">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-box-small bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-school"></i>
                                            </div>
                                            <div>
                                                <div class="text-uppercase fw-bold text-primary" style="font-size: 0.6rem; letter-spacing: 1px;">Guru Pembimbing</div>
                                                <h6 class="mb-0 fw-bold text-dark" id="det_guru_nama">-</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                            <small class="text-muted" id="det_guru_nip">-</small>
                                            <a href="#" id="det_guru_wa_btn" target="_blank" class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold d-none" style="font-size: 0.7rem;">
                                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-4 border bg-white shadow-sm">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="icon-box-small bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <div>
                                                <div class="text-uppercase fw-bold text-info" style="font-size: 0.6rem; letter-spacing: 1px;">Pembimbing Lapangan</div>
                                                <h6 class="mb-0 fw-bold text-dark" id="det_pl_nama">-</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                            <small class="text-muted" id="det_pl_nip">-</small>
                                            <a href="#" id="det_pl_wa_btn" target="_blank" class="btn btn-xs btn-success rounded-pill px-3 py-1 fw-bold d-none" style="font-size: 0.7rem;">
                                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 bg-light d-flex justify-content-center">
                    <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow" data-bs-dismiss="modal">
                        Tutup Informasi
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Preview PDF (Standard Premium) --}}
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
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" download>
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        <div class="vr opacity-10"></div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script src="{{ asset('assets/js/pembimbing/daftarSiswa.js') }}"></script>
    @endpush
@endsection
