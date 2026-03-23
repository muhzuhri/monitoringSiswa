@extends('layouts.nav.pembimbing')

@section('title', 'Evaluasi & Penilaian Siswa')
@section('body-class', 'dosen-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/penilaianSiswa-dosen.css') }}">
@endpush

@section('body')
    <div class="dashboard-container mt-4 mb-5">

        <div class="page-header">
            <div class="header-text">
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
            <form action="{{ route('pembimbing.evaluasi') }}" method="GET" class="search-form">
                <div class="input-group search-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" 
                        placeholder="Cari nama siswa, NISN, atau sekolah..." value="{{ $search ?? '' }}">
                    <button type="submit" class="btn btn-purple px-4">Cari</button>
                    @if($search)
                        <a href="{{ route('pembimbing.evaluasi') }}" class="btn btn-outline-secondary d-flex align-items-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

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
                    <span>Riwayat Penilaian ({{ $siswasDone->count() }})</span>
                </button>
            </div>
        </div>

        <div class="tab-content">
            {{-- Tab Menunggu Penilaian --}}
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="ui-card p-0 overflow-hidden shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Asal Sekolah</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasPending as $siswa)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm-placeholder bg-purple-light text-purple me-2 rounded-circle text-center" style="width: 32px; height: 32px; line-height: 32px; font-size: 0.8rem; font-weight: bold;">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="fw-bold">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $siswa->nisn }}</td>
                                        <td><span class="text-muted">{{ $siswa->sekolah ?? '-' }}</span></td>
                                        <td><span class="badge-status status-pending">Belum Dinilai</span></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-purple rounded-pill px-3" 
                                                onclick="openEvaluationForm('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->jurusan }}', {{ json_encode($siswa->penilaians) }})">
                                                <i class="fas fa-edit me-1"></i> Input Nilai
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="opacity-30 mb-2">
                                                <i class="fas fa-clipboard-check fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted mb-0">Tidak ada siswa yang menunggu penilaian.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tab Riwayat Penilaian --}}
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="ui-card p-0 overflow-hidden shadow-sm border-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Nama Siswa</th>
                                    <th>NISN</th>
                                    <th>Rata-rata</th>
                                    <th>Update Terakhir</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswasDone as $siswa)
                                    @php
                                        $p = $siswa->penilaians->where('pemberi_nilai', 'Dosen Pembimbing')->first();
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm-placeholder bg-success-light text-success me-2 rounded-circle text-center" style="width: 32px; height: 32px; line-height: 32px; font-weight: bold;">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="fw-bold">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $siswa->nisn }}</td>
                                        <td>
                                            <span class="badge bg-purple px-2 py-1">{{ number_format($p->rata_rata ?? 0, 1) }}</span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $p ? \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-outline-purple rounded-pill px-3" 
                                                    onclick="openEvaluationForm('{{ $siswa->nisn }}', '{{ addslashes($siswa->nama) }}', '{{ $siswa->jurusan }}', {{ json_encode($siswa->penilaians) }})">
                                                    <i class="fas fa-history me-1"></i> Edit
                                                </button>
                                                <a href="{{ route('pembimbing.laporan.cetak', $siswa->nisn) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                                    <i class="fas fa-print me-1"></i> Cetak
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="opacity-30 mb-2">
                                                <i class="fas fa-history fa-3x text-muted"></i>
                                            </div>
                                            <p class="text-muted mb-0">Belum ada riwayat penilaian.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penilaian (Pop-up) -->
    <div class="modal fade" id="evaluationModal" tabindex="-1" aria-labelledby="evaluationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-purple text-white p-4">
                    <div class="modal-title-wrapper">
                        <h4 class="modal-title fw-800 mb-1" id="activeStudentName">Nama Siswa</h4>
                        <p class="text-white-50 small mb-0"><span id="activeStudentNisn">NISN</span> | <span id="activeStudentJurusan">Jurusan</span></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="workspace-tabs d-flex border-bottom bg-light">
                        <button class="tab-btn w-50 active py-3 fw-bold border-0" onclick="switchTab('form')"><i class="fas fa-edit me-2"></i>Form Penilaian</button>
                        <button class="tab-btn w-50 py-3 fw-bold border-0" onclick="switchTab('history')"><i class="fas fa-history me-2"></i>Riwayat Evaluasi</button>
                    </div>

                    <!-- Tab Content: Form Penilaian -->
                    <div id="tabForm" class="tab-content active p-4">
                        <form action="{{ route('pembimbing.evaluasi.store') }}" method="POST" class="evaluation-form">
                            @csrf
                            <input type="hidden" name="nisn" id="formSiswaNisn">

                            <div class="form-section p-0">
                                <h6 class="section-heading mb-4">Input Skor Kompetensi & Sikap (Skala 0 - 100)</h6>
                                
                                <div class="row g-4">
                                    <div class="col-md-6 border-end">
                                        <h6 class="text-purple fw-bold mb-3"><i class="fas fa-user-check me-2"></i>Sikap Kerja</h6>
                                        <div id="sikapKerjaList">
                                            @foreach($kriteria->where('tipe', 'sikap_kerja') as $item)
                                                <div class="rating-group mb-3">
                                                    <label class="form-label d-flex justify-content-between">
                                                        <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                                        <span class="text-muted small" id="val_score_{{ $item->id_kriteria }}">0</span>
                                                    </label>
                                                    <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                                           class="rating-input score-sikap w-100" 
                                                           data-id="{{ $item->id_kriteria }}"
                                                           min="0" max="100" placeholder="0 - 100" required
                                                           oninput="calculateAverages()">
                                                </div>
                                            @endforeach
                                            <div class="avg-display bg-light-purple p-3 rounded-4 mb-3 text-center">
                                                <span class="d-block small text-purple fw-bold mb-1">Rata-rata Sikap</span>
                                                <div class="h3 mb-0 fw-800 text-purple" id="avgSikap">0.0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-tools me-2"></i>Kompetensi Keahlian</h6>
                                        <div id="kompetensiList">
                                            @foreach($kriteria->where('tipe', 'kompetensi_keahlian') as $item)
                                                <div class="rating-group mb-3">
                                                    <label class="form-label d-flex justify-content-between">
                                                        <span>{{ $loop->iteration }}. {{ $item->nama_kriteria }}</span>
                                                        <span class="text-muted small" id="val_score_{{ $item->id_kriteria }}">0</span>
                                                    </label>
                                                    <input type="number" name="scores[{{ $item->id_kriteria }}]" 
                                                           class="rating-input score-kompetensi w-100" 
                                                           data-id="{{ $item->id_kriteria }}"
                                                           min="0" max="100" placeholder="0 - 100" required
                                                           oninput="calculateAverages()">
                                                </div>
                                            @endforeach
                                            <div class="avg-display bg-primary-light p-3 rounded-4 mb-3 text-center">
                                                <span class="d-block small text-primary fw-bold mb-1">Rata-rata Kompetensi</span>
                                                <div class="h3 mb-0 fw-800 text-primary" id="avgKompetensi">0.0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="cumulative-score-box mt-4 p-4 border-0 bg-purple-light rounded-4 text-center">
                                    <h5 class="mb-1 text-purple fw-bold">Nilai Kumulatif</h5>
                                    <div class="display-4 fw-800 text-purple" id="cumulativeScore">0.0</div>
                                    <p class="text-muted mb-0 small">(Rata-rata Sikap + Rata-rata Kompetensi) / 2</p>
                                </div>
                            </div>

                            <div class="form-section mt-4 p-0">
                                <h6 class="section-heading mb-4">Informasi Tambahan</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Kategori Penilaian</label>
                                            <select name="kategori" class="custom-select w-100 p-2 border rounded" required>
                                                <option value="">Pilih Kategori...</option>
                                                <option value="Evaluasi Bulan 1">Evaluasi Bulan 1</option>
                                                <option value="Evaluasi Bulan 2">Evaluasi Bulan 2</option>
                                                <option value="Evaluasi Bulan 3">Evaluasi Bulan 3</option>
                                                <option value="Penilaian Akhir Magang">Penilaian Akhir Magang</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Komentar / Catatan</label>
                                            <textarea name="komentar" class="custom-textarea w-100 p-2 border rounded" rows="3" placeholder="Sebutkan hal-baik..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Saran Pengembangan</label>
                                            <textarea name="saran" class="custom-textarea w-100 p-2 border rounded" rows="3" placeholder="Sebutkan saran..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-0 p-4">
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-purple px-5 fw-bold"><i class="fas fa-paper-plane me-2"></i>Simpan Penilaian</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Content: Riwayat Evaluasi -->
                    <div id="tabHistory" class="tab-content p-4">
                        <div id="historyListArea" class="history-list">
                            <!-- Populated via Javascript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openEvaluationForm(nisn, name, jurusan, penilaians) {
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

            // Reset tabs to Form
            switchTab('form');

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('evaluationModal'));
            modal.show();
        }

        function switchTab(tabName) {
            // Toggle Buttons
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(btn => btn.classList.remove('active'));

            // Toggle Contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            if (tabName === 'form') {
                btns[0].classList.add('active');
                document.getElementById('tabForm').classList.add('active');
            } else {
                btns[1].classList.add('active');
                document.getElementById('tabHistory').classList.add('active');
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
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-inbox empty-icon"></i>
                        <p class="text-muted mt-2">Belum ada riwayat evaluasi untuk siswa ini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            penilaians.forEach(p => {
                const date = new Date(p.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                
                // Group details by type
                let sikapDetails = p.penilaian_details.filter(d => d.kriteria.tipe === 'sikap_kerja');
                let kompetensiDetails = p.penilaian_details.filter(d => d.kriteria.tipe === 'kompetensi_keahlian');

                html += `
                    <div class="history-card mb-4 p-3 border rounded shadow-sm bg-white">
                        <div class="history-header d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-purple-light text-purple fw-bold p-2">${p.kategori}</span>
                                <span class="text-muted small ms-2"><i class="fas fa-calendar-alt me-1"></i> ${date}</span>
                            </div>
                            <div class="score-badge text-center bg-purple text-white p-2 rounded">
                                <div class="h4 mb-0 fw-bold">${parseFloat(p.rata_rata).toFixed(1)}</div>
                                <div class="small" style="font-size: 0.6rem;">KUMULATIF</div>
                            </div>
                        </div>
                        <div class="history-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="small fw-bold text-purple mb-1 border-bottom">Sikap Kerja</div>
                                    <div class="row g-1">
                                        ${sikapDetails.slice(0, 4).map(d => `
                                            <div class="col-6" title="${d.kriteria.nama_kriteria}">
                                                <div class="d-flex justify-content-between bg-light p-1 rounded" style="font-size: 0.7rem;">
                                                    <span class="text-truncate" style="max-width: 60px;">${d.kriteria.nama_kriteria}</span>
                                                    <span class="fw-bold">${Math.round(d.skor)}</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="small fw-bold text-primary mb-1 border-bottom">Kompetensi</div>
                                    <div class="row g-1">
                                        ${kompetensiDetails.slice(0, 4).map(d => `
                                            <div class="col-6" title="${d.kriteria.nama_kriteria}">
                                                <div class="d-flex justify-content-between bg-light p-1 rounded" style="font-size: 0.7rem;">
                                                    <span class="text-truncate" style="max-width: 60px;">${d.kriteria.nama_kriteria}</span>
                                                    <span class="fw-bold">${Math.round(d.skor)}</span>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                            ${p.komentar ? `
                                <div class="feedback-box bg-light-purple p-2 rounded mb-2" style="font-size: 0.8rem;">
                                    <strong>Komentar:</strong> ${p.komentar}
                                </div>
                            ` : ''}
                            <div class="text-end">
                                <a href="/pembimbing/laporan/${p.nisn}/cetak" class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="fas fa-print me-1"></i> Cetak/Detail
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }
    </script>
@endpush