@extends('layouts.nav.pembimbing')

@section('title', 'Evaluasi & Penilaian Siswa')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/penilaianSiswa-dosen.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">

        <div class="page-header">
            <div class="header-content">
                <h2 class="page-title"><i class="fas fa-star-half-alt text-purple me-2"></i>Evaluasi & Penilaian Siswa</h2>
                <p class="page-subtitle">Berikan evaluasi berkala dan penilaian akhir magang kepada siswa binaan Anda.</p>
            </div>
        </div>

        <!-- Alert Messages (Success/Error) -->
        @if(session('success'))
            <div class="custom-alert alert-success-soft mb-4">
                <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                <div class="alert-content">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="custom-alert alert-danger-soft mb-4">
                <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">{{ session('error') }}</div>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="search-section mb-4">
            <form id="searchForm" class="search-form">
                <div class="input-group search-group">
                    <span class="input-group-text">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput" class="form-control" 
                        placeholder="Cari nama siswa, NISN, atau sekolah..." value="{{ $search ?? '' }}" autocomplete="off">
                </div>
            </form>
        </div>

        {{-- Tab Navigasi --}}
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav">
                <button class="tab-button active btn-tab-trigger" data-target="pending">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Menunggu Penilaian ({{ $siswasPending->count() }})</span>
                </button>
                <button class="tab-button btn-tab-trigger" data-target="history">
                    <i class="fas fa-check-double"></i>
                    <span>Riwayat Penilaian ({{ $siswasDone->count() }})</span>
                </button>
            </div>
        </div>

        <div class="tab-contents">
            {{-- Tab Menunggu Penilaian --}}
            <div class="tab-pane-content" id="pending">
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="custom-table w-100">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Asal Sekolah</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasPending as $siswa)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md-placeholder bg-purple-light text-purple me-2">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $siswa->nisn }}</td>
                                        <td><span class="text-muted">{{ $siswa->sekolah ?? '-' }}</span></td>
                                        <td><span class="badge-status status-pending">Belum Dinilai</span></td>
                                        <td class="text-end">
                                            <button class="btn-action-icon btn-review btn-open-modal" 
                                                data-modal="evaluationModal"
                                                onclick="setupEvaluationForm('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->jurusan }}', {{ json_encode($siswa->penilaians) }})">
                                                <i class="fas fa-edit me-1"></i> Input Nilai
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-clipboard-check empty-icon"></i>
                                                <p class="text-muted mb-0">Tidak ada siswa yang menunggu penilaian.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsPending" style="display: none;">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        Tidak ada siswa yang cocok dengan pencarian di tab ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab Riwayat Penilaian --}}
            <div class="tab-pane-content" id="history" style="display: none;">
                <div class="content-card">
                    <div class="table-responsive">
                        <table class="custom-table w-100">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Rata-rata</th>
                                    <th>Update Terakhir</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasDone as $siswa)
                                    @php
                                        $p = $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-md-placeholder bg-success-light text-success me-2">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="fw-bold text-dark">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $siswa->nisn }}</td>
                                        <td>
                                            <span class="eval-badge bg-purple-light text-purple">{{ number_format($p->rata_rata ?? 0, 1) }}</span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $p ? \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn-action-icon btn-review btn-open-modal" 
                                                    data-modal="evaluationModal"
                                                    onclick="setupEvaluationForm('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->jurusan }}', {{ json_encode($siswa->penilaians) }})">
                                                    <i class="fas fa-history me-1"></i> Edit
                                                </button>
                                                <a href="{{ route('pembimbing.laporan.cetak', $siswa->nisn) }}" class="btn-action-icon btn-review" style="background:var(--color-green-lt); color:var(--color-green);">
                                                    <i class="fas fa-print me-1"></i> Cetak
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-history empty-icon"></i>
                                                <p class="text-muted mb-0">Belum ada riwayat penilaian.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="noResultsHistory" style="display: none;">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        Tidak ada data yang cocok dengan pencarian di tab ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penilaian (Custom Overlay System) -->
    <div class="custom-modal-overlay" id="evaluationModal">
        <div class="custom-modal modal-lg">
            <div class="modal-header">
                <div class="active-student-info">
                    <h4 id="activeStudentName">Nama Siswa</h4>
                    <p><span id="activeStudentNisn">NISN</span> | <span id="activeStudentJurusan">Jurusan</span></p>
                </div>
                <button type="button" class="modal-close btn-close-modal" data-modal="evaluationModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="workspace-tabs border-bottom">
                    <button class="tab-btn w-50 active" onclick="switchModalTab('form')"><i class="fas fa-edit me-2"></i>Form Penilaian</button>
                    <button class="tab-btn w-50" onclick="switchModalTab('history')"><i class="fas fa-history me-2"></i>Riwayat Evaluasi</button>
                </div>

                <!-- Modal Content: Form Penilaian -->
                <div id="tabFormContent" class="tab-content active p-4">
                    <form action="{{ route('pembimbing.evaluasi.store') }}" method="POST" id="evaluationForm">
                        @csrf
                        <input type="hidden" name="nisn" id="formSiswaNisn">

                        <div class="form-section p-0">
                            <h6 class="section-heading mb-4">Input Skor Kompetensi & Sikap (Skala 0 - 100)</h6>
                            
                            <div class="rating-grid">
                                <div class="col-sikap pe-3" style="border-right: 1px solid var(--border);">
                                    <h6 class="text-purple fw-bold mb-3"><i class="fas fa-user-check me-2"></i>Sikap Kerja</h6>
                                    @foreach($kriteria->where('tipe', 'sikap_kerja') as $item)
                                        <div class="form-group mb-3">
                                            <label class="form-label d-flex justify-content-between">
                                                <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                                <span class="text-muted small val-score" id="val_score_{{ $item->id_kriteria }}">0</span>
                                            </label>
                                            <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                                   class="rating-input score-sikap" 
                                                   data-id="{{ $item->id_kriteria }}"
                                                   min="0" max="100" placeholder="0 - 100" required
                                                   oninput="calculateAverages()">
                                        </div>
                                    @endforeach
                                    <div class="avg-display rounded-4 text-center mt-3" style="background:var(--color-primary-lt); padding:1rem;">
                                        <span class="d-block small text-purple fw-bold mb-1">Rata-rata Sikap</span>
                                        <div class="h3 mb-0 fw-bold text-purple" id="avgSikap">0.0</div>
                                    </div>
                                </div>
                                <div class="col-kompetensi ps-3">
                                    <h6 class="text-primary fw-bold mb-3"><i class="fas fa-tools me-2"></i>Kompetensi Keahlian</h6>
                                    @foreach($kriteria->where('tipe', 'kompetensi_keahlian') as $item)
                                        <div class="form-group mb-3">
                                            <label class="form-label d-flex justify-content-between">
                                                <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                                <span class="text-muted small val-score" id="val_score_{{ $item->id_kriteria }}">0</span>
                                            </label>
                                            <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                                   class="rating-input score-kompetensi" 
                                                   data-id="{{ $item->id_kriteria }}"
                                                   min="0" max="100" placeholder="0 - 100" required
                                                   oninput="calculateAverages()">
                                        </div>
                                    @endforeach
                                    <div class="avg-display rounded-4 text-center mt-3" style="background:var(--color-blue-lt); padding:1rem;">
                                        <span class="d-block small text-primary fw-bold mb-1">Rata-rata Kompetensi</span>
                                        <div class="h3 mb-0 fw-bold text-primary" id="avgKompetensi">0.0</div>
                                    </div>
                                </div>
                            </div>

                            <div class="cumulative-score-box mt-4 p-4 text-center" style="background:rgba(124, 58, 237, 0.08); border-radius:14px;">
                                <h5 class="mb-1 text-purple fw-bold">Nilai Kumulatif</h5>
                                <div class="display-4 fw-bold text-purple" id="cumulativeScore" style="font-size: 2.5rem;">0.0</div>
                                <p class="text-muted mb-0 small">(Rata-rata Sikap + Rata-rata Kompetensi) / 2</p>
                            </div>
                        </div>

                        <div class="form-section mt-4 p-0">
                            <h6 class="section-heading mb-4">Informasi Tambahan</h6>
                            <div class="row-info" style="display:flex; flex-direction:column; gap:1rem;">
                                <div class="form-group">
                                    <label class="form-label">Kategori Penilaian</label>
                                    <select name="kategori" class="custom-select" required>
                                        <option value="">Pilih Kategori...</option>
                                        <option value="Evaluasi Bulan 1">Evaluasi Bulan 1</option>
                                        <option value="Evaluasi Bulan 2">Evaluasi Bulan 2</option>
                                        <option value="Evaluasi Bulan 3">Evaluasi Bulan 3</option>
                                        <option value="Penilaian Akhir Magang">Penilaian Akhir Magang</option>
                                    </select>
                                </div>
                                <div class="row-textareas" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                                    <div class="form-group">
                                        <label class="form-label">Komentar / Catatan</label>
                                        <textarea name="komentar" class="custom-textarea" rows="3" placeholder="Sebutkan hal-baik..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Saran Pengembangan</label>
                                        <textarea name="saran" class="custom-textarea" rows="3" placeholder="Sebutkan saran..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class="btn-cancel btn-close-modal" data-modal="evaluationModal">Batal</button>
                            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane me-2"></i>Simpan Penilaian</button>
                        </div>
                    </form>
                </div>

                <!-- Modal Content: Riwayat Evaluasi -->
                <div id="tabHistoryContent" class="tab-content p-4" style="display: none;">
                    <div id="historyListArea" class="history-list">
                        <!-- Populated via Javascript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Main Tab Switching (Pending vs History)
            const tabTriggers = document.querySelectorAll('.btn-tab-trigger');
            const tabPanes = document.querySelectorAll('.tab-pane-content');

            tabTriggers.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    
                    // Update buttons
                    tabTriggers.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Update contents
                    tabPanes.forEach(pane => {
                        pane.style.display = pane.id === targetId ? 'block' : 'none';
                    });
                });
            });

            // Live Search Logic
            const searchInput = document.getElementById('searchInput');
            const pendingRows = document.querySelectorAll('#pending table tbody tr:not(#noResultsPending)');
            const historyRows = document.querySelectorAll('#history table tbody tr:not(#noResultsHistory)');
            const noResultsPending = document.getElementById('noResultsPending');
            const noResultsHistory = document.getElementById('noResultsHistory');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Filter Pending Table
                    let pendingMatchFound = false;
                    pendingRows.forEach(row => {
                        if (row.querySelector('.fw-bold') === null) return; 
                        
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
                        if (row.querySelector('.fw-bold') === null) return;
                        
                        const text = row.innerText.toLowerCase();
                        const isMatch = text.includes(searchTerm);
                        row.style.display = isMatch ? '' : 'none';
                        if (isMatch) historyMatchFound = true;
                    });
                    if (noResultsHistory) {
                        noResultsHistory.style.display = (historyMatchFound || searchTerm === '') ? 'none' : 'table-row';
                    }
                });
            }

            // Modal System Logic
            const openBtns = document.querySelectorAll('.btn-open-modal');
            const closeBtns = document.querySelectorAll('.btn-close-modal');
            const overlays = document.querySelectorAll('.custom-modal-overlay');

            openBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                });
            });

            closeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const modalId = this.getAttribute('data-modal');
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });

            overlays.forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
        });

        function setupEvaluationForm(nisn, name, jurusan, penilaians) {
            // Update headers in modal
            document.getElementById('activeStudentName').textContent = name;
            document.getElementById('activeStudentNisn').textContent = nisn;
            document.getElementById('activeStudentJurusan').textContent = jurusan || '-';

            // Set form data
            document.getElementById('formSiswaNisn').value = nisn;

            // Reset scores
            document.querySelectorAll('.rating-input').forEach(input => input.value = '');
            calculateAverages();

            // Populate History
            populateHistory(penilaians);

            // Reset modal tabs to Form
            switchModalTab('form');
        }

        function switchModalTab(tabName) {
            const btns = document.querySelectorAll('.workspace-tabs .tab-btn');
            const formContent = document.getElementById('tabFormContent');
            const historyContent = document.getElementById('tabHistoryContent');

            btns.forEach(btn => btn.classList.remove('active'));

            if (tabName === 'form') {
                btns[0].classList.add('active');
                formContent.style.display = 'block';
                historyContent.style.display = 'none';
            } else {
                btns[1].classList.add('active');
                formContent.style.display = 'none';
                historyContent.style.display = 'block';
            }
        }

        function calculateAverages() {
            // Sikap Kerja
            const sikapInputs = document.querySelectorAll('.score-sikap');
            let totalSikap = 0;
            let countSikap = 0;
            sikapInputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                totalSikap += val;
                countSikap++;
                document.getElementById('val_score_' + input.dataset.id).textContent = val;
            });
            const avgSikap = countSikap > 0 ? totalSikap / countSikap : 0;
            document.getElementById('avgSikap').textContent = avgSikap.toFixed(1);

            // Kompetensi
            const kompetensiInputs = document.querySelectorAll('.score-kompetensi');
            let totalKompetensi = 0;
            let countKompetensi = 0;
            kompetensiInputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                totalKompetensi += val;
                countKompetensi++;
                document.getElementById('val_score_' + input.dataset.id).textContent = val;
            });
            const avgKompetensi = countKompetensi > 0 ? totalKompetensi / countKompetensi : 0;
            document.getElementById('avgKompetensi').textContent = avgKompetensi.toFixed(1);

            // Cumulative
            const cumulative = (avgSikap + avgKompetensi) / 2;
            document.getElementById('cumulativeScore').textContent = cumulative.toFixed(1);
        }

        function populateHistory(penilaians) {
            const container = document.getElementById('historyListArea');
            container.innerHTML = '';

            if (!penilaians || penilaians.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox empty-icon"></i>
                        <p class="text-muted mt-2">Belum ada riwayat evaluasi untuk siswa ini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            penilaians.forEach(p => {
                const date = new Date(p.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                
                // Note: p.penilaian_details is expected to be loaded via the controller
                let detailsHtml = '';
                if (p.penilaian_details) {
                    let sikapD = p.penilaian_details.filter(d => d.kriteria && d.kriteria.tipe === 'sikap_kerja');
                    let kompD = p.penilaian_details.filter(d => d.kriteria && d.kriteria.tipe === 'kompetensi_keahlian');

                    detailsHtml = `
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px;">
                            <div>
                                <small class="text-purple fw-bold">Sikap</small>
                                ${sikapD.slice(0,3).map(d => `<div style="font-size:0.7rem; display:flex; justify-content:space-between; background:#f8fafc; padding:2px 5px; border-radius:4px; margin-top:2px;">
                                    <span class="text-truncate" style="max-width:100px;">${d.kriteria.nama_kriteria}</span>
                                    <b>${Math.round(d.skor)}</b>
                                </div>`).join('')}
                            </div>
                            <div>
                                <small class="text-primary fw-bold">Kompetensi</small>
                                ${kompD.slice(0,3).map(d => `<div style="font-size:0.7rem; display:flex; justify-content:space-between; background:#f8fafc; padding:2px 5px; border-radius:4px; margin-top:2px;">
                                    <span class="text-truncate" style="max-width:100px;">${d.kriteria.nama_kriteria}</span>
                                    <b>${Math.round(d.skor)}</b>
                                </div>`).join('')}
                            </div>
                        </div>
                    `;
                }

                html += `
                    <div class="history-card" style="background:#fff; border:1px solid var(--border); border-radius:12px; padding:15px; margin-bottom:15px; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <span class="badge-status status-pending" style="background:var(--color-primary-lt); color:var(--color-primary);">${p.kategori}</span>
                                <div class="text-muted small mt-1"><i class="fas fa-calendar-alt"></i> ${date}</div>
                            </div>
                            <div style="background:var(--color-primary); color:#fff; border-radius:8px; padding:5px 10px; text-align:center;">
                                <div style="font-size:1.1rem; font-weight:800;">${parseFloat(p.rata_rata).toFixed(1)}</div>
                                <div style="font-size:0.5rem; text-transform:uppercase;">Skor</div>
                            </div>
                        </div>
                        ${detailsHtml}
                        ${p.komentar ? `
                            <div class="note-bubble mt-2" style="background:var(--color-primary-lt); border-left:3px solid var(--color-primary); padding:8px 12px; font-size:0.75rem; border-radius:6px;">
                                <b>Komentar:</b> ${p.komentar}
                            </div>
                        ` : ''}
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    </script>
@endpush
@endsection
