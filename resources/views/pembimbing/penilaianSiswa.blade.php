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
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $s->nama }}</strong></td>
                                            <td>{{ $s->nisn }}</td>
                                            <td><span class="fw-bold text-primary">{{ number_format($p->rata_rata ?? 0, 1) }}</span></td>
                                            <td><span class="badge-status status-done">Sudah Dinilai</span></td>
                                            <td class="col-aksi">
                                                <div class="table-aksi-flex">
                                                    <a href="{{ route('pembimbing.evaluasi.input', $s->nisn) }}" class="btn-action btn-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="{{ route('pembimbing.laporan.cetak', $s->nisn) }}" class="btn-action btn-success">
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Search Logic
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const pendingRows = document.querySelectorAll('#pending table tbody tr:not(#noResultsPending)');
            const historyRows = document.querySelectorAll('#history table tbody tr:not(#noResultsHistory)');
            const noResultsPending = document.getElementById('noResultsPending');
            const noResultsHistory = document.getElementById('noResultsHistory');

            // Auto-buka tab riwayat jika ada param ?tab=history atau ada filter periode aktif
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'history' || urlParams.get('periode')) {
                const historyTabBtn = document.getElementById('history-tab');
                if (historyTabBtn) {
                    const tab = new bootstrap.Tab(historyTabBtn);
                    tab.show();
                }
            } else if (urlParams.get('tab') === 'kriteria') {
                const kriteriaTabBtn = document.getElementById('kriteria-tab');
                if (kriteriaTabBtn) {
                    const tab = new bootstrap.Tab(kriteriaTabBtn);
                    tab.show();
                }
            }

            // Hide search on kriteria tab
            document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const searchSection = document.querySelector('.search-section');
                    if (searchSection) {
                        searchSection.style.display = e.target.id === 'kriteria-tab' ? 'none' : 'block';
                    }
                });
            });

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Filter Pending Table
                    let pendingMatchFound = false;
                    pendingRows.forEach(row => {
                        if (row.querySelector('strong') === null) return; // Skip empty state row
                        
                        const text = row.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) pendingMatchFound = true;
                    });
                    if (noResultsPending) {
                        noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'table-row';
                    }

                    // Filter History Table
                    let historyMatchFound = false;
                    historyRows.forEach(row => {
                        if (row.querySelector('strong') === null) return; // Skip empty state row
                        
                        const text = row.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) historyMatchFound = true;
                    });
                    if (noResultsHistory) {
                        noResultsHistory.style.display = (historyMatchFound || searchTerm === '') ? 'none' : 'table-row';
                    }
                });

                if (searchForm) {
                    searchForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                    });
                }
            }
        });

        function setupEditCriteria(el) {
            const form = document.getElementById('formEditKriteria');
            form.action = `/pembimbing/kriteria/${el.dataset.id}`;
            document.getElementById('edit_nama').value = el.dataset.nama;
            document.getElementById('edit_tipe').value = el.dataset.tipe;
            document.getElementById('edit_urutan').value = el.dataset.urutan;
        }
    </script>
    @endpush
@endsection
