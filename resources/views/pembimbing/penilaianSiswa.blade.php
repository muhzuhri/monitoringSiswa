@extends('layouts.nav.pembimbing')

@section('title', 'Evaluasi & Penilaian Siswa')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/penilaianSiswa-dosen.css') }}">
@endpush

@section('body')
    <div class="assessment-container">

        <div class="page-header-section">
            <div class="header-title-group">
                <h2 class="main-title"><i class="fas fa-star-half-alt title-icon"></i>Evaluasi & Penilaian Siswa</h2>
                <p class="subtitle-text">Berikan evaluasi berkala dan penilaian akhir magang kepada siswa binaan Anda.</p>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="status-msg msg-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="status-msg msg-error">
                <i class="fas fa-exclamation-triangle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Search Bar --}}
        <div class="search-wrapper">
            <form id="searchForm">
                <div class="search-input-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="search-field" 
                        placeholder="Cari nama siswa, NISN, atau sekolah..." value="{{ $search ?? '' }}" autocomplete="off">
                </div>
            </form>
        </div>

        {{-- Tab Navigasi --}}
        <div class="tab-nav-wrapper">
            <div class="tabs-navigation-panel">
                <button class="tab-trigger-btn active" data-target="pending">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Menunggu Penilaian ({{ $siswasPending->count() }})</span>
                </button>
                <button class="tab-trigger-btn" data-target="history">
                    <i class="fas fa-check-double"></i>
                    <span>Riwayat Penilaian ({{ $siswasDone->count() }})</span>
                </button>
            </div>
        </div>

        <div class="tab-contents">
            {{-- Tab Menunggu Penilaian --}}
            <div class="tab-pane-content" id="pending">
                <div class="main-content-card">
                    <div class="table-scroller">
                        <table class="assessment-table">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Asal Sekolah</th>
                                    <th>Status</th>
                                    <th class="actions-cell">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasPending as $siswa)
                                    <tr>
                                        <td>
                                            <div class="student-data-cell">
                                                <div class="avatar-circle bg-purple-light text-purple">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="student-name-text">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td><span class="nisn-text">{{ $siswa->nisn }}</span></td>
                                        <td><span class="date-text">{{ $siswa->sekolah ?? '-' }}</span></td>
                                        <td><span class="badge-pill pill-pending">Belum Dinilai</span></td>
                                        <td class="actions-cell">
                                            <button class="btn-premium btn-assessment btn-open-modal" 
                                                data-modal="evaluationModal"
                                                data-nisn="{{ $siswa->nisn }}"
                                                data-nama="{{ addslashes($siswa->nama) }}"
                                                data-jurusan="{{ $siswa->jurusan }}"
                                                data-penilaians="{{ json_encode($siswa->penilaians) }}"
                                                onclick="setupEvaluationFormFromEl(this)">
                                                <i class="fas fa-edit"></i> Input Nilai
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-data-container">
                                                <i class="fas fa-clipboard-check empty-data-icon"></i>
                                                <p class="subtitle-text">Tidak ada siswa yang menunggu penilaian.</p>
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
                <div class="main-content-card">
                    <div class="table-scroller">
                        <table class="assessment-table">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Rata-rata</th>
                                    <th>Update Terakhir</th>
                                    <th class="actions-cell">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasDone as $siswa)
                                    @php
                                        $p = $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="student-data-cell">
                                                <div class="avatar-circle bg-success-light text-success">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="student-name-text">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td><span class="nisn-text">{{ $siswa->nisn }}</span></td>
                                        <td>
                                            <span class="avg-score-badge">{{ number_format($p->rata_rata ?? 0, 1) }}</span>
                                        </td>
                                        <td>
                                            <span class="date-text">{{ $p ? \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') : '-' }}</span>
                                        </td>
                                        <td class="actions-cell">
                                            <div class="action-flex-group">
                                                <button class="btn-premium btn-edit-history btn-open-modal" 
                                                    data-modal="evaluationModal"
                                                    data-nisn="{{ $siswa->nisn }}"
                                                    data-nama="{{ addslashes($siswa->nama) }}"
                                                    data-jurusan="{{ $siswa->jurusan }}"
                                                    data-penilaians="{{ json_encode($siswa->penilaians) }}"
                                                    onclick="setupEvaluationFormFromEl(this)">
                                                    <i class="fas fa-history"></i> Edit
                                                </button>
                                                <a href="{{ route('pembimbing.laporan.cetak', $siswa->nisn) }}" class="btn-premium btn-print">
                                                    <i class="fas fa-print"></i> Cetak
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-data-container">
                                                <i class="fas fa-history empty-data-icon"></i>
                                                <p class="subtitle-text">Belum ada riwayat penilaian.</p>
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

    <!-- Modal Penilaian -->
    <div class="custom-modal-overlay" id="evaluationModal">
        <div class="premium-modal-box">
            <div class="modal-header-nav">
                <div class="student-identity-group">
                    <h4 id="activeStudentName">Nama Siswa</h4>
                    <p class="student-meta-subtitle"><span id="activeStudentNisn">NISN</span> | <span id="activeStudentJurusan">Jurusan</span></p>
                </div>
                <button type="button" class="modal-close-trigger btn-close-modal" data-modal="evaluationModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body-scroller">
                <div class="modal-tab-selector">
                    <button class="modal-tab-btn active" onclick="switchModalTab('form')"><i class="fas fa-edit me-2"></i>Form Penilaian</button>
                    <button class="modal-tab-btn" onclick="switchModalTab('history')"><i class="fas fa-history me-2"></i>Riwayat Evaluasi</button>
                </div>

                <!-- Tab: Form Penilaian -->
                <div id="tabFormContent" class="tab-content active">
                    <form action="{{ route('pembimbing.evaluasi.store') }}" method="POST" id="evaluationForm">
                        @csrf
                        <input type="hidden" name="nisn" id="formSiswaNisn">

                        <div class="rating-form-grid">
                            <div class="rating-column column-left">
                                <h6 class="grid-heading heading-sikap"><i class="fas fa-user-check"></i>Sikap Kerja</h6>
                                @foreach($kriteria->where('tipe', 'sikap_kerja') as $item)
                                    <div class="rating-control-group">
                                        <label class="rating-label-flex">
                                            <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                            <span class="current-val-chip" id="val_score_{{ $item->id_kriteria }}">0</span>
                                        </label>
                                        <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                               class="score-num-field score-sikap" 
                                               data-id="{{ $item->id_kriteria }}"
                                               min="0" max="100" placeholder="0 - 100" required
                                               oninput="calculateAverages()">
                                    </div>
                                @endforeach
                                <div class="avg-display-card bg-sikap-soft">
                                    <span class="avg-label">Rata-rata Sikap</span>
                                    <div class="avg-value" id="avgSikap">0.0</div>
                                </div>
                            </div>
                            <div class="rating-column column-right">
                                <h6 class="grid-heading heading-kompetensi"><i class="fas fa-tools"></i>Kompetensi Keahlian</h6>
                                @foreach($kriteria->where('tipe', 'kompetensi_keahlian') as $item)
                                    <div class="rating-control-group">
                                        <label class="rating-label-flex">
                                            <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                            <span class="current-val-chip" id="val_score_{{ $item->id_kriteria }}">0</span>
                                        </label>
                                        <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                               class="score-num-field score-kompetensi" 
                                               data-id="{{ $item->id_kriteria }}"
                                               min="0" max="100" placeholder="0 - 100" required
                                               oninput="calculateAverages()">
                                    </div>
                                @endforeach
                                <div class="avg-display-card bg-kompetensi-soft">
                                    <span class="avg-label">Rata-rata Kompetensi</span>
                                    <div class="avg-value" id="avgKompetensi">0.0</div>
                                </div>
                            </div>
                        </div>

                        <div class="cumulative-summary-panel">
                            <h5 class="subtitle-text">Nilai Kumulatif</h5>
                            <div class="cumulative-score-display" id="cumulativeScore">0.0</div>
                            <p class="date-text">(Rata-rata Sikap + Rata-rata Kompetensi) / 2</p>
                        </div>
                        <div class="additional-info-section">
                            <div class="form-field-group">
                                <label class="field-label">Kategori Penilaian</label>
                                <select name="kategori" id="formKategori" class="premium-select" required>
                                    <option value="">Pilih Kategori...</option>
                                    <option value="Evaluasi Bulan 1">Evaluasi Bulan 1</option>
                                    <option value="Evaluasi Bulan 2">Evaluasi Bulan 2</option>
                                    <option value="Evaluasi Bulan 3">Evaluasi Bulan 3</option>
                                    <option value="Penilaian Akhir Magang">Penilaian Akhir Magang</option>
                                </select>
                            </div>
                            <div class="textarea-layout-row">
                                <div class="form-field-group">
                                    <label class="field-label">Komentar / Catatan</label>
                                    <textarea name="komentar" id="formKomentar" class="premium-textarea" rows="3" placeholder="Sebutkan hal-baik..."></textarea>
                                </div>
                                <div class="form-field-group">
                                    <label class="field-label">Saran Pengembangan</label>
                                    <textarea name="saran" id="formSaran" class="premium-textarea" rows="3" placeholder="Sebutkan saran..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-action-footer">
                            <button type="button" class="btn-premium btn-edit-history btn-close-modal" data-modal="evaluationModal">Batal</button>
                            <button type="submit" class="btn-premium btn-assessment"><i class="fas fa-paper-plane"></i>Simpan Penilaian</button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Riwayat Evaluasi -->
                <div id="tabHistoryContent" class="tab-content" style="display: none;">
                    <div id="historyListArea">
                        <!-- Populated via Javascript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Main Tab Switching
            const tabTriggers = document.querySelectorAll('.tab-trigger-btn');
            const tabPanes = document.querySelectorAll('.tab-pane-content');

            tabTriggers.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    tabTriggers.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    tabPanes.forEach(pane => {
                        pane.style.display = pane.id === targetId ? 'block' : 'none';
                    });
                });
            });

            // Live Search
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    ['pending', 'history'].forEach(tabId => {
                        const tabPane = document.getElementById(tabId);
                        const rows = tabPane.querySelectorAll('.assessment-table tbody tr:not([id^="noResults"])');
                        const noResultsRow = document.getElementById(`noResults${tabId.charAt(0).toUpperCase() + tabId.slice(1)}`);
                        let found = false;

                        rows.forEach(row => {
                            const match = row.innerText.toLowerCase().includes(searchTerm);
                            row.style.display = match ? '' : 'none';
                            if (match) found = true;
                        });

                        if (noResultsRow) {
                            noResultsRow.style.display = (found || searchTerm === '') ? 'none' : 'table-row';
                        }
                    });
                });
            }

            // Modal Logic
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

        function setupEvaluationFormFromEl(el) {
            setupEvaluationForm(
                el.getAttribute('data-nisn'),
                el.getAttribute('data-nama'),
                el.getAttribute('data-jurusan'),
                JSON.parse(el.getAttribute('data-penilaians'))
            );
        }

        function setupEvaluationForm(nisn, name, jurusan, penilaians) {
            document.getElementById('activeStudentName').textContent = name;
            document.getElementById('activeStudentNisn').textContent = nisn;
            document.getElementById('activeStudentJurusan').textContent = jurusan || '-';
            document.getElementById('formSiswaNisn').value = nisn;
            
            // Reset scores
            document.querySelectorAll('.score-num-field').forEach(input => input.value = '');
            
            // Default category and comments
            document.getElementById('formKategori').value = '';
            document.getElementById('formKomentar').value = '';
            document.getElementById('formSaran').value = '';

            // If editing, find the latest assessment by Supervisor
            if (penilaians && penilaians.length > 0) {
                const supervisorAssessments = penilaians.filter(p => p.pemberi_nilai === 'Dosen Pembimbing');
                if (supervisorAssessments.length > 0) {
                    const latest = supervisorAssessments[0]; // Assuming order by created_at desc
                    document.getElementById('formKategori').value = latest.kategori || '';
                    document.getElementById('formKomentar').value = latest.komentar || '';
                    document.getElementById('formSaran').value = latest.saran || '';
                    
                    // Fill scores if details exist
                    if (latest.penilaian_details) {
                        latest.penilaian_details.forEach(detail => {
                            const input = document.querySelector(`.score-num-field[data-id="${detail.id_kriteria}"]`);
                            if (input) input.value = Math.round(detail.skor);
                        });
                    }
                }
            }

            calculateAverages();
            populateHistory(penilaians);
            switchModalTab('form');
        }

        function switchModalTab(tabName) {
            const btns = document.querySelectorAll('.modal-tab-btn');
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
            const types = ['sikap', 'kompetensi'];
            let cumulative = 0;

            types.forEach(type => {
                const inputs = document.querySelectorAll(`.score-${type}`);
                let total = 0, count = 0;
                inputs.forEach(input => {
                    const val = parseFloat(input.value) || 0;
                    total += val;
                    count++;
                    const chip = document.getElementById('val_score_' + input.dataset.id);
                    if (chip) chip.textContent = val;
                });
                const avg = count > 0 ? total / count : 0;
                document.getElementById(`avg${type.charAt(0).toUpperCase() + type.slice(1)}`).textContent = avg.toFixed(1);
                cumulative += avg;
            });

            document.getElementById('cumulativeScore').textContent = (cumulative / 2).toFixed(1);
        }

        function populateHistory(penilaians) {
            const container = document.getElementById('historyListArea');
            container.innerHTML = '';

            if (!penilaians || penilaians.length === 0) {
                container.innerHTML = `
                    <div class="empty-data-container">
                        <i class="fas fa-inbox empty-data-icon"></i>
                        <p class="subtitle-text">Belum ada riwayat evaluasi untuk siswa ini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            penilaians.forEach(p => {
                const date = new Date(p.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                let detailsHtml = '';
                
                if (p.penilaian_details) {
                    const types = [
                        { key: 'sikap_kerja', label: 'Sikap', class: 'heading-sikap' },
                        { key: 'kompetensi_keahlian', label: 'Kompetensi', class: 'heading-kompetensi' }
                    ];
                    
                    detailsHtml = '<div class="mini-score-grid">';
                    types.forEach(t => {
                        const items = p.penilaian_details.filter(d => d.kriteria && d.kriteria.tipe === t.key);
                        detailsHtml += `<div><small class="${t.class} fw-bold">${t.label}</small>`;
                        items.slice(0,3).forEach(d => {
                            detailsHtml += `
                                <div class="mini-score-item">
                                    <span>${d.kriteria.nama_kriteria}</span>
                                    <b>${Math.round(d.skor)}</b>
                                </div>`;
                        });
                        detailsHtml += '</div>';
                    });
                    detailsHtml += '</div>';
                }

                html += `
                    <div class="modal-history-card">
                        <div class="card-top-flex">
                            <div>
                                <span class="history-cat-tag">${p.kategori}</span>
                                <div class="date-text mt-1"><i class="fas fa-calendar-alt"></i> ${date}</div>
                            </div>
                            <div class="score-round-box">
                                <div class="val-large">${parseFloat(p.rata_rata).toFixed(1)}</div>
                                <div class="lbl-tiny">Skor</div>
                            </div>
                        </div>
                        ${detailsHtml}
                        ${p.komentar ? `
                            <div class="comment-bubble">
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
