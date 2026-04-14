@extends('layouts.nav.guru')

@section('title', 'Verifikasi Laporan Akhir - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/guru/verifikasi.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">

        {{-- Header Halaman --}}
        <div class="page-header">
            <div class="header-text">
                <h3 class="page-title">Verifikasi Laporan Akhir</h3>
                <p class="page-subtitle">Tinjau dan proses verifikasi laporan akhir magang siswa bimbingan Anda.</p>
            </div>
            <!-- <div class="pending-badge">
                <span class="pending-label">PENDING VERIFIKASI</span>
                <span class="pending-count">{{ isset($laporanPending) ? $laporanPending->count() : 0 }}</span>
            </div> -->
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
        </div>

        @if(session('success'))
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ============================================================
             MODE DETAIL: Verifikasi satu laporan
        ============================================================ --}}
        @if(isset($laporan))
                <div class="detail-layout">

                    {{-- Kolom Kiri: Berkas & Form --}}
                    <div class="detail-main">
                        <div class="ui-card">
                            {{-- Header Berkas --}}
                            <div class="file-header">
                                <div class="file-info">
                                    <div class="file-icon-wrap">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="file-title">Berkas Laporan: Versi {{ $laporan->versi }}</h5>
                                        <small class="file-date">Diunggah pada {{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y, H:i') }}</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $laporan->file) }}" target="_blank" class="btn-download">
                                    <i class="fas fa-download"></i> Unduh File
                                </a>
                            </div>

                            {{-- Preview Placeholder --}}
                            <div class="report-preview-container">
                                <i class="fas fa-file-pdf file-icon"></i>
                                <h6 class="preview-title">Pratinjau Berkas Tidak Tersedia Langsung</h6>
                                <p class="preview-desc">Silakan unduh berkas untuk meninjau detail laporan secara menyeluruh di perangkat Anda.</p>
                                <a href="{{ asset('storage/' . $laporan->file) }}" target="_blank" class="btn-preview">
                                    <i class="fas fa-external-link-alt"></i> Buka Preview
                                </a>
                            </div>

                            <hr class="divider">

                            {{-- Form Verifikasi --}}
                            <form action="{{ route('guru.verifikasi.update', $laporan->id_laporan) }}" method="POST" class="verify-form">
                                @csrf
                                <label class="form-label">Keputusan Verifikasi</label>
                                <div class="decision-group">
                                    <input type="radio" class="decision-radio" name="status" id="approve" value="approved" required>
                                    <label class="decision-btn decision-approve" for="approve">
                                        <i class="fas fa-check-circle"></i>
                                        <span>SETUJUI LAPORAN</span>
                                    </label>
                                </div>                         
                                <div class="form-actions">
                                    <button type="submit" class="btn-verify">
                                        Simpan Verifikasi <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>

            {{-- ============================================================
                 MODE LIST: Daftar semua laporan pending & riwayat
            ============================================================ --}}
        @else
            

            {{-- Tab Navigasi --}}
            <div class="tabs-wrapper">
                <div class="tabs-nav" role="tablist">
                    <button class="tab-button active" id="pending-tab" data-bs-toggle="pill" data-bs-target="#pending"
                        type="button" role="tab">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Menunggu Verifikasi ({{ $laporanPending->count() }})</span>
                    </button>
                    <button class="tab-button" id="history-tab" data-bs-toggle="pill" data-bs-target="#history"
                        type="button" role="tab">
                        <i class="fas fa-check-double"></i>
                        <span>Riwayat Verifikasi</span>
                    </button>
                </div>
            </div>

            <div class="tab-content" id="verifikasiTabContent">

                {{-- Tab Pending --}}
                <div class="tab-pane fade show active" id="pending" role="tabpanel">
                    <div class="laporan-grid">
                        @forelse($laporanPending as $p)
                            <div class="ui-card laporan-card">
                                <div class="laporan-card-top">
                                    <span class="badge-versi">VERSI {{ $p->versi }}</span>
                                    <small class="laporan-date">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y') }}</small>
                                </div>
                                <h5 class="laporan-name">{{ $p->siswa->nama }}</h5>
                                <p class="laporan-perusahaan"><i class="fas fa-university"></i> {{ $p->siswa->perusahaan }}</p>

                                <div class="file-preview-box">
                                    <small class="file-preview-label">FILE LAPORAN:</small>
                                    <div class="file-preview-row">
                                        <i class="fas fa-file-pdf file-pdf-icon"></i>
                                        <span class="file-name">{{ basename($p->file) }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('guru.verifikasi.show', $p->id_laporan) }}" class="btn-periksa">
                                    Periksa Laporan <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-clipboard-check empty-icon"></i>
                                <h4 class="empty-title">Semua Beres!</h4>
                                <p class="empty-desc">Tidak ada laporan baru yang perlu diperiksa saat ini.</p>
                            </div>
                        @endforelse
                        <div id="noResultsPending" class="empty-state" style="display: none; width: 100%;">
                            <i class="fas fa-search empty-icon"></i>
                            <h4 class="empty-title">Tidak ada hasil</h4>
                            <p class="empty-desc">Tidak ada laporan pending yang cocok dengan pencarian.</p>
                        </div>
                    </div>
                </div>

                {{-- Tab Riwayat --}}
                <div class="tab-pane fade" id="history" role="tabpanel">

                    {{-- Filter Periode --}}
                    <div class="history-filter-bar mb-4 d-flex align-items-center flex-wrap" style="background:#ffffff; border:1px solid var(--border); border-radius:16px; padding:1rem 1.5rem; box-shadow:var(--shadow-sm);">
                        <div class="filter-label" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; font-weight:800; color:var(--primary); text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="fas fa-filter"></i>
                            <span>Filter Periode:</span>
                        </div>
                        <form id="periodeFilterForm" method="GET" action="{{ route('guru.verifikasi') }}" class="filter-form d-flex align-items-center flex-wrap ms-md-4 ms-2 mt-2 mt-md-0" style="gap:0.75rem;">
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
                                <a href="{{ route('guru.verifikasi', array_filter(['search' => $search ?? '', 'tab' => 'history'])) }}"
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
                                        <th class="th-padded">Siswa</th>
                                        <th>Tanggal Update</th>
                                        <th>Status</th>
                                        <th class="th-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($historyLaporan as $h)
                                        <tr>
                                            <td class="th-padded">
                                                <div class="td-siswa-name">{{ $h->siswa->nama }}</div>
                                                <div class="td-siswa-nisn">{{ $h->siswa->nisn }}</div>
                                            </td>
                                            <td class="td-date">{{ \Carbon\Carbon::parse($h->updated_at)->format('d M Y, H:i') }}</td>
                                            <td>
                                                @if($h->status == 'approved')
                                                    <span class="status-badge status-ok">Disetujui</span>
                                                @else
                                                    <span class="status-badge status-bad">Ditolak</span>
                                                @endif
                                            </td>
                                            <td class="th-end">
                                                <a href="{{ route('guru.verifikasi.show', $h->id_laporan) }}" class="btn-detail">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="empty-row text-center">Belum ada riwayat verifikasi.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="noResultsHistory" style="display: none;">
                                        <td colspan="5" class="empty-row text-center text-muted">
                                            Tidak ada riwayat yang cocok dengan pencarian.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if($historyLaporan->hasPages())
                            <div class="table-pagination">
                                {{ $historyLaporan->links() }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Live Search Logic
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');
            const pendingCards = document.querySelectorAll('#pending .laporan-card');
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
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Filter Pending Cards
                    let pendingMatchFound = false;
                    pendingCards.forEach(card => {
                        const text = card.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        card.style.display = isMatch ? 'flex' : 'none';
                        if (isMatch) pendingMatchFound = true;
                    });
                    if (noResultsPending) {
                        const isReallyEmpty = document.querySelector('#pending .laporan-grid').children.length === 1 && noResultsPending;
                        noResultsPending.style.display = (pendingMatchFound || searchTerm === '') ? 'none' : 'block';
                    }

                    // Filter History Table
                    let historyMatchFound = false;
                    historyRows.forEach(row => {
                        if (row.querySelector('.td-siswa-name') === null) return; // Skip empty state row
                        
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
    </script>
    @endpush
@endsection
