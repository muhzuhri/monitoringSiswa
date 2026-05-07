@extends('layouts.nav.pembimbing')

@section('title', 'Evaluasi & Penilaian - SIM Magang')
@section('body-class', 'dashboard-page pembimbing-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/penilaianSiswa-dosen.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Evaluasi & Penilaian Siswa</h3>
                <p class="page-subtitle">Kelola dan berikan evaluasi berkala untuk siswa bimbingan Anda.</p>
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
                <a href="{{ route('pembimbing.evaluasi') }}" class="btn-action btn-back">
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
                                                <a href="{{ route('pembimbing.evaluasi.input', $s->nisn) }}" class="btn-action btn-primary">
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
                                            $p = $s->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                            
                                            // Hitung nilai kumulatif (rata-rata sikap + rata-rata kompetensi) / 2
                                            $sikapAvg = $p ? ($p->penilaianDetails->where('kriteria.tipe', 'sikap_kerja')->avg('skor') ?? 0) : 0;
                                            $kompetensiAvg = $p ? ($p->penilaianDetails->where('kriteria.tipe', 'kompetensi_keahlian')->avg('skor') ?? 0) : 0;
                                            $cumulative = ($sikapAvg + $kompetensiAvg) / 2;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td><span class="fw-bold text-primary">{{ number_format($cumulative, 1) }}</span></td>
                                            <td><span class="badge-status status-done">Sudah Dinilai</span></td>
                                            <td class="col-aksi">
                                                <div class="table-aksi-flex">
                                                    <a href="{{ route('pembimbing.evaluasi.input', $s->nisn) }}" class="btn-action btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button type="button" class="btn-action btn-success btn-preview-pdf"
                                                        data-url="{{ route('pembimbing.laporan.cetak', $s->nisn) }}">
                                                        <i class="fas fa-print"></i> Cetak
                                                    </button>
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

                    <div class="ui-card">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="80">Urutan</th>
                                        <th>Nama Kriteria</th>
                                        <th width="200">Kategori</th>
                                        <th width="150" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kriteriaKustom as $k)
                                        <tr>
                                            <td class="col-urutan"><span class="fw-bold text-muted">#{{ $k->urutan }}</span></td>
                                            <td class="fw-semibold">{{ $k->nama_kriteria }}</td>
                                            <td>
                                                @if($k->tipe == 'sikap_kerja')
                                                    <span class="badge-type-1"><i class="fas fa-user-check me-1"></i> Sikap Kerja</span>
                                                @else
                                                    <span class="badge-type-2"><i class="fas fa-tools me-1"></i> Kompetensi</span>
                                                @endif
                                            </td>
                                            <td class="col-aksi">
                                                <button class="btn btn-sm btn-outline-primary btn-table-action" data-bs-toggle="modal" data-bs-target="#editCriteriaModal"
                                                        data-id="{{ $k->id_kriteria }}"
                                                        data-nama="{{ $k->nama_kriteria }}"
                                                        data-tipe="{{ $k->tipe }}"
                                                        data-urutan="{{ $k->urutan }}"
                                                        onclick="setupEditCriteria(this)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('pembimbing.kriteria.destroy', $k->id_kriteria) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kriteria ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-table-action">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
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

                <form action="{{ route('pembimbing.evaluasi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="nisn" value="{{ $siswa->nisn }}">
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Kategori Penilaian</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Penilaian Akhir Magang" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Penilaian Akhir Magang' ? 'selected' : '' }}>Penilaian Akhir Magang</option>
                            <option value="Evaluasi Bulan 1" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Evaluasi Bulan 1' ? 'selected' : '' }}>Evaluasi Bulan 1</option>
                            <option value="Evaluasi Bulan 2" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Evaluasi Bulan 2' ? 'selected' : '' }}>Evaluasi Bulan 2</option>
                            <option value="Evaluasi Bulan 3" {{ old('kategori', $penilaian ? $penilaian->kategori : '') == 'Evaluasi Bulan 3' ? 'selected' : '' }}>Evaluasi Bulan 3</option>
                        </select>
                    </div>

                    <div class="criteria-section mb-4">
                        <h5 class="section-title">I. SIKAP KERJA</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'sikap_kerja') as $k)
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
                        <h5 class="section-title">II. KOMPETENSI KEAHLIAN</h5>
                        <div class="form-grid">
                            @foreach($kriteria->where('tipe', 'kompetensi_keahlian') as $k)
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

                    <div class="row">
                        <div class="col-md-6 form-group full-width mb-4">
                            <label class="form-label">Ekspektasi & Catatan</label>
                            <textarea name="komentar" class="form-control" rows="4" placeholder="Masukkan catatan atau ekspektasi untuk siswa...">{{ old('komentar', $penilaian ? $penilaian->komentar : '') }}</textarea>
                        </div>

                        <div class="col-md-6 form-group full-width mb-4">
                            <label class="form-label">Saran Pengembangan</label>
                            <textarea name="saran" class="form-control" rows="4" placeholder="Masukkan saran pengembangan untuk siswa...">{{ old('saran', $penilaian ? $penilaian->saran : '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions mb-4">
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
                    <form action="{{ route('pembimbing.kriteria.store') }}" method="POST">
                        @csrf
                        <div class="custom-form-group">
                            <label class="custom-form-label">Nama Kriteria</label>
                            <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Kejujuran" required>
                        </div>
                        <div class="custom-form-group">
                            <label class="custom-form-label">Tipe Kategori</label>
                            <select name="tipe" class="form-control" required>
                                <option value="sikap_kerja">Sikap Kerja</option>
                                <option value="kompetensi_keahlian">Kompetensi Keahlian</option>
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
                                <option value="sikap_kerja">Sikap Kerja</option>
                                <option value="kompetensi_keahlian">Kompetensi Keahlian</option>
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

    {{-- Modal Preview PDF (Standard Premium) --}}
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true" style="z-index: 2000;">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title" style="color: white !important; margin: 0;">Preview Penilaian</h6>
                    </div>

                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" download>
                                <i class="fas fa-download"></i> <span>Unduh Penilaian</span>
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
        <script src="{{ asset('assets/js/pembimbing/penilaian.js') }}"></script>
    @endpush
@endsection
