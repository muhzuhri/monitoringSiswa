@extends('layouts.nav.guru')

@section('title', 'Penilaian Magang Siswa - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/penilaian.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Penilaian Akhir Magang</h3>
                <p class="page-subtitle">Kelola dan berikan nilai akhir untuk siswa bimbingan Anda.</p>
            </div>
            {{-- Search Bar --}}
            <div class="search-section">
            <form id="searchForm" class="search-form">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" 
                        placeholder="Cari nama siswa atau NISN..." value="{{ $search ?? '' }}" autocomplete="off">
                </div>
            </form>
            </div>
            @if(isset($siswa))
                <a href="{{ route('guru.penilaian') }}" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(isset($siswasPending) && isset($siswasDone))
            

            {{-- Tab Navigasi --}}
            <div class="tabs-wrapper mb-4">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending"
                        type="button" role="tab">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Menunggu Penilaian ({{ $siswasPending->count() }})</span>
                    </button>
                    <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">
                        <i class="fas fa-check-double"></i>
                        <span>Riwayat Penilaian ({{ $siswasDone->count() }}{{ isset($periodeId) && $periodeId ? ' • filtered' : '' }})</span>
                    </button>
                    <button class="tab-button" id="kriteria-tab" data-bs-toggle="pill" data-bs-target="#kriteria"
                        type="button" role="tab">
                        <i class="fas fa-sliders-h"></i>
                        <span>Kategori Penilaian</span>
                    </button>
                </div>
            </div>

            <div class="tab-content">
                {{-- Tab Menunggu Penilaian --}}
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswasPending as $s)
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td>{{ $s->perusahaan }}</td>
                                            <td><span class="badge-status status-pending">Belum Dinilai</span></td>
                                            <td class="text-end">
                                                <a href="{{ route('guru.penilaian.input', $s->nisn) }}" class="btn-action btn-primary">
                                                    <i class="fas fa-edit"></i> Input Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-clipboard-check mb-3 fa-3x text-muted"></i>
                                                <p>Tidak ada siswa yang menunggu penilaian.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr id="noResultsPending" style="display: none;">
                                        <td colspan="5" class="empty-state text-center py-4 text-muted">
                                            Tidak ada siswa yang cocok dengan pencarian di tab ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tab Riwayat Penilaian --}}
                <div class="tab-pane fade" id="history" role="tabpanel">

                    {{-- Filter Periode --}}
                    <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap" style="background:#ffffff; border:1px solid var(--border); border-radius:16px; padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                        <div class="filter-label" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-filter"></i>
                            <span>Filter Periode:</span>
                        </div>
                        <form id="periodeFilterForm" method="GET" action="{{ route('guru.penilaian') }}" class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0" style="gap:0.75rem;">
                            @if(isset($search))
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            <input type="hidden" name="tab" value="history">
                            <select name="periode" id="periodeSelect" class="form-select" style="min-width:220px; border-radius:12px; font-weight:600; font-size:0.85rem; border:1.5px solid var(--border); cursor:pointer;" onchange="document.getElementById('periodeFilterForm').submit()">
                                <option value="">-- Semua Periode --</option>
                                @if(isset($periodeOptions))
                                    @foreach($periodeOptions as $opt)
                                        <option value="{{ $opt->id_tahun_ajaran }}"
                                            {{ (isset($periodeId) && $periodeId == $opt->id_tahun_ajaran) ? 'selected' : '' }}>
                                            {{ $opt->tahun_ajaran }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if(isset($periodeId) && $periodeId)
                                <a href="{{ route('guru.penilaian', array_filter(['search' => $search ?? '', 'tab' => 'history'])) }}"
                                   class="btn btn-outline-danger btn-sm" style="border-radius:12px; font-weight:700; padding:0.45rem 1rem;" title="Hapus Filter">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            @endif
                        </form>
                        @if(isset($periodeId) && $periodeId && isset($periodeOptions))
                            @php $selectedPeriode = $periodeOptions->firstWhere('id_tahun_ajaran', $periodeId); @endphp
                            @if($selectedPeriode)
                                <span class="badge bg-primary ms-auto mt-2 mt-md-0" style="padding:0.5rem 1rem; border-radius:99px; font-weight:800; font-size:0.75rem;">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $selectedPeriode->tahun_ajaran }}
                                </span>
                            @endif
                        @endif
                    </div>

                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Rata-rata</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswasDone as $s)
                                        @php
                                            $p = $s->penilaians->where('pemberi_nilai', 'Guru Pembimbing')->first();
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td><span class="fw-bold text-primary">{{ number_format($p->rata_rata, 1) }}</span></td>
                                            <td><span class="badge-status status-done">Sudah Dinilai</span></td>
                                            <td class="col-aksi">
                                                <div class="table-aksi-flex">
                                                    <a href="{{ route('guru.penilaian.input', $s->nisn) }}" class="btn-action btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="{{ route('guru.penilaian.export', $s->nisn) }}" class="btn-action btn-success">
                                                        <i class="fas fa-print"></i> Cetak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-history mb-3 fa-3x text-muted"></i>
                                                <p>Belum ada riwayat penilaian.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr id="noResultsHistory" style="display: none;">
                                        <td colspan="5" class="empty-state text-center py-4 text-muted">
                                            Tidak ada data yang cocok dengan pencarian di tab ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Tab Kriteria Penilaian --}}
                <div class="tab-pane fade" id="kriteria" role="tabpanel">
                    <div class="kriteria-header">
                        <div>
                            <h4 class="kriteria-title"><i class="fas fa-sliders-h"></i> Pengaturan Kriteria Penilaian</h4>
                            <p class="kriteria-subtitle">Sesuaikan kriteria penilaian. Semua perubahan akan langsung diterapkan pada form penilaian siswa.</p>
                        </div>
                        <button class="btn-action btn-primary" data-bs-toggle="modal" data-bs-target="#addCriteriaModal">
                            <i class="fas fa-plus"></i> Tambah Kriteria
                        </button>
                    </div>

                    {{-- Section I: Kepribadian --}}
                    <div class="section-divider mb-3">
                        <h5 class="section-subtitle-styled"><i class="fas fa-user-check me-2"></i> I. KEPRIBADIAN / ETOS KERJA</h5>
                    </div>
                    <div class="ui-card mb-5">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="80">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="150" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countKepribadian = 0; @endphp
                                    @foreach($kriteriaKustom->where('tipe', 'guru_kepribadian') as $k)
                                        @php $countKepribadian++; @endphp
                                        <tr>
                                            <td class="col-urutan"><span class="fw-bold text-muted">#{{ $k->urutan }}</span></td>
                                            <td class="fw-semibold">{{ $k->nama_kriteria }}</td>
                                            <td class="col-aksi">
                                                <button class="btn btn-sm btn-outline-primary btn-table-action" data-bs-toggle="modal" data-bs-target="#editCriteriaModal"
                                                        data-id="{{ $k->id_kriteria }}"
                                                        data-nama="{{ $k->nama_kriteria }}"
                                                        data-tipe="{{ $k->tipe }}"
                                                        data-urutan="{{ $k->urutan }}"
                                                        onclick="setupEditCriteria(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('guru.kriteria.destroy', $k->id_kriteria) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kriteria ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($countKepribadian === 0)
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada kriteria kepribadian.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Section II: Kemampuan --}}
                    <div class="section-divider mb-3 mt-4">
                        <h5 class="section-subtitle-styled"><i class="fas fa-tools me-2"></i> II. KEMAMPUAN</h5>
                    </div>
                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="80">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="150" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countKemampuan = 0; @endphp
                                    @foreach($kriteriaKustom->where('tipe', 'guru_kemampuan') as $k)
                                        @php $countKemampuan++; @endphp
                                        <tr>
                                            <td class="col-urutan"><span class="fw-bold text-muted">#{{ $k->urutan }}</span></td>
                                            <td class="fw-semibold">{{ $k->nama_kriteria }}</td>
                                            <td class="col-aksi">
                                                <button class="btn btn-sm btn-outline-primary btn-table-action" data-bs-toggle="modal" data-bs-target="#editCriteriaModal"
                                                        data-id="{{ $k->id_kriteria }}"
                                                        data-nama="{{ $k->nama_kriteria }}"
                                                        data-tipe="{{ $k->tipe }}"
                                                        data-urutan="{{ $k->urutan }}"
                                                        onclick="setupEditCriteria(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('guru.kriteria.destroy', $k->id_kriteria) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kriteria ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($countKemampuan === 0)
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Belum ada kriteria kemampuan.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(isset($siswa))
            {{-- INPUT MODE --}}
            <div class="ui-card">
                <div class="card-header">
                    <h4 class="card-title">Form Penilaian Siswa</h4>
                    <p class="card-description">Nama: <strong>{{ $siswa->nama }}</strong> | NISN: <strong>{{ $siswa->nisn }}</strong></p>
                </div>

                <form action="{{ route('guru.penilaian.store', $siswa->nisn) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Kategori Penilaian</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Penilaian Akhir Magang" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Akhir Magang' ? 'selected' : '' }}>Penilaian Akhir Magang</option>
                            <option value="Penilaian Tengah Magang" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Tengah Magang' ? 'selected' : '' }}>Penilaian Tengah Magang</option>
                        </select>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">I. KEPRIBADIAN / ETOS KERJA</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'guru_kepribadian') as $k)
                                @php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                @endphp
                                <div class="form-group">
                                    <label class="form-label">{{ $k->nama_kriteria }} (0-100)</label>
                                    <input type="number" name="scores[{{ $k->id_kriteria }}]" class="form-control" min="0" max="100" 
                                        value="{{ old('scores.'.$k->id_kriteria, $score) }}" required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">II. KEMAMPUAN</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'guru_kemampuan') as $k)
                                @php
                                    $score = $penilaian ? $penilaian->penilaianDetails->where('id_kriteria', $k->id_kriteria)->first()->skor ?? '' : '';
                                @endphp
                                <div class="form-group">
                                    <label class="form-label">{{ $k->nama_kriteria }} (0-100)</label>
                                    <input type="number" name="scores[{{ $k->id_kriteria }}]" class="form-control" min="0" max="100" 
                                        value="{{ old('scores.'.$k->id_kriteria, $score) }}" required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group full-width mb-4">
                        <label class="form-label">Saran / Catatan</label>
                        <textarea name="saran" class="form-control" rows="4" placeholder="Masukkan saran pengembangan untuk siswa...">{{ old('saran', $penilaian ? $penilaian->saran : '') }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-action btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                            <i class="fas fa-save"></i> Simpan Penilaian
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Modal Tambah Kriteria -->
    <div class="modal fade" id="addCriteriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title custom-modal-title">Tambah Kriteria Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-modal-body">
                    <form action="{{ route('guru.kriteria.store') }}" method="POST">
                        @csrf
                        <div class="custom-form-group">
                            <label class="custom-form-label">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Kedisiplinan" required>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Tipe Kategori</label>
                            <select name="tipe" class="form-control" required>
                                <option value="guru_kepribadian">Kepribadian / Etos Kerja</option>
                                <option value="guru_kemampuan">Kemampuan</option>
                            </select>
                        </div>
                        <div class="custom-form-group-last">
                            <label class="custom-form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control" value="0">
                        </div>
                        <div class="custom-modal-actions">
                            <button type="button" class="btn-modal btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-modal btn-primary">Simpan Kriteria</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kriteria -->
    <div class="modal fade" id="editCriteriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h5 class="modal-title custom-modal-title">Edit Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-modal-body">
                    <form id="formEditKriteria" method="POST">
                        @csrf @method('PUT')
                        <div class="custom-form-group">
                            <label class="custom-form-label">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Tipe Kategori</label>
                            <select name="tipe" id="edit_tipe" class="form-control" required>
                                <option value="guru_kepribadian">Kepribadian / Etos Kerja</option>
                                <option value="guru_kemampuan">Kemampuan</option>
                            </select>
                        </div>
                        <div class="custom-form-group-last">
                            <label class="custom-form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control">
                        </div>
                        <div class="custom-modal-actions">
                            <button type="button" class="btn-modal btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-modal btn-primary">Update Kriteria</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/guru/penilaian.js') }}"></script>
    @endpush
@endsection
