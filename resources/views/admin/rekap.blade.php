@extends('layouts.nav.admin')

@section('title', 'Pusat Rekapitulasi Data - Admin')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/rekap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/kelola-modals.css') }}">
@endpush

@section('body')
    <div class="dashboard-container">
        <div class="admin-content-wrapper">
            <div class="rekap-main-card">
                <div class="rekap-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
                    <div class="rekap-info">
                        <h2 class="rekap-title">Pusat Rekapitulasi Data</h2>
                        <p class="rekap-subtitle mb-0">Kelola dan pantau statistik data mahasiswa/siswa serta guru pembimbing secara real-time dengan laporan profesional.</p>
                        <div id="activeFilterBadge" style="display:none; margin-top:12px;">
                            <span class="badge-active-filter">
                                <i class="fas fa-calendar-alt"></i>
                                <span id="activeFilterLabel"></span>
                            </span>
                        </div>
                    </div>
                    <div class="filter-box">
                        <label class="form-label fw-bold small text-muted mb-2 text-uppercase letter-spacing-1">Filter Tahun Ajaran</label>
                        <div class="input-group-custom">
                            <select id="filterPeriode" class="form-select border-0 shadow-sm rounded-pill px-4">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->tahun_ajaran }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Custom Tabs Navigation -->
                <div class="rekap-tabs-wrapper">
                    <div class="rekap-tabs-nav" id="rekapTabs" role="tablist">
                        <button class="rekap-tab-btn active" id="siswa-tab" data-bs-toggle="pill" data-bs-target="#siswa-pane" type="button" role="tab">
                            <i class="fas fa-user-graduate"></i>
                            <span>Rekap Siswa</span>
                        </button>
                        <button class="rekap-tab-btn" id="guru-tab" data-bs-toggle="pill" data-bs-target="#guru-pane" type="button" role="tab">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Rekap Guru</span>
                        </button>
                    </div>
                </div>

                <div class="tab-content mt-2">
                    <!-- Tab Siswa -->
                    <div class="tab-pane fade show active" id="siswa-pane" role="tabpanel">
                        <div class="rekap-grid">
                            <!-- Siswa Aktif -->
                            <div class="rekap-card">
                                <div class="rekap-card-icon icon-primary">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <div class="rekap-card-count" id="statSiswaAktif">{{ $stats['siswa_aktif'] }}</div>
                                <h5 class="rekap-card-title">Mahasiswa/Siswa Aktif</h5>
                                <a href="javascript:void(0)" data-url="{{ route('admin.rekap.siswaAktif') }}" class="rekap-btn-download btn-preview-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Preview Rekap</span>
                                </a>
                            </div>

                            <!-- Siswa Selesai -->
                            <div class="rekap-card">
                                <div class="rekap-card-icon icon-success">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="rekap-card-count" id="statSiswaSelesai">{{ $stats['siswa_selesai'] }}</div>
                                <h5 class="rekap-card-title">Siswa Selesai Magang</h5>
                                <a href="javascript:void(0)" data-url="{{ route('admin.rekap.siswaSelesai') }}" class="rekap-btn-download btn-preview-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Preview Rekap</span>
                                </a>
                            </div>

                            <!-- Total Siswa -->
                            <div class="rekap-card">
                                <div class="rekap-card-icon icon-info">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="rekap-card-count" id="statTotalSiswa">{{ $stats['total_siswa'] }}</div>
                                <h5 class="rekap-card-title">Total Keseluruhan Siswa</h5>
                                <a href="javascript:void(0)" data-url="{{ route('admin.rekap.siswaTotal') }}" class="rekap-btn-download btn-preview-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Preview Rekap</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Guru -->
                    <div class="tab-pane fade" id="guru-pane" role="tabpanel">
                        <div class="rekap-grid">
                            <div class="rekap-card">
                                <div class="rekap-card-icon icon-warning">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="rekap-card-count" id="statTotalGuru">{{ $stats['total_guru'] }}</div>
                                <h5 class="rekap-card-title">Total Guru Pembimbing</h5>
                                <a href="javascript:void(0)" data-url="{{ route('admin.rekap.guru') }}" class="rekap-btn-download btn-preview-pdf">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Preview Daftar Guru</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview PDF -->
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content">
                <div class="pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h5 class="modal-title mb-0">Preview Laporan</h5>
                    </div>
                    
                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File" target="_blank">
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body pdf-viewer-body">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                            <p class="mt-3">Sedang memuat dokumen...</p>
                        </div>
                        <div id="pdfErrorMsg" style="display:none; color: #fff; text-align: center;">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                            <h5>Gagal memuat file PDF</h5>
                            <p>Maaf, terjadi kesalahan saat memuat dokumen. Anda tetap bisa langsung mengunduh file menggunakan tombol di atas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        async function renderPDF(url) {
            const container = document.getElementById('pdfCanvasContainer');
            const loadingEl = document.getElementById('pdfLoadingIndicator');
            const errorEl = document.getElementById('pdfErrorMsg');

            container.querySelectorAll('canvas').forEach(c => c.remove());
            loadingEl.style.display = 'flex';
            errorEl.style.display = 'none';
            container.scrollTop = 0;

            try {
                const pdfDoc = await pdfjsLib.getDocument(url).promise;
                loadingEl.style.display = 'none';

                const containerWidth = container.clientWidth - 40;
                const outputScale = window.devicePixelRatio || 1;

                for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                    const page = await pdfDoc.getPage(pageNum);
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const baseScale = containerWidth / unscaledViewport.width;
                    const viewport = page.getViewport({ scale: baseScale * outputScale });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    canvas.style.width = (viewport.width / outputScale) + 'px';
                    canvas.style.height = (viewport.height / outputScale) + 'px';

                    container.appendChild(canvas);

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    await page.render(renderContext).promise;
                }
            } catch (err) {
                loadingEl.style.display = 'none';
                errorEl.style.display = 'block';
                console.error('PDF.js error:', err);
            }
        }

        // ── Animasi counter angka ──────────────────────────────────────────────
        function animateCounter(el, targetVal) {
            const start   = parseInt(el.textContent) || 0;
            const end     = parseInt(targetVal)      || 0;
            const dur     = 500;
            const step    = 16;
            const steps   = Math.ceil(dur / step);
            const inc     = (end - start) / steps;
            let   current = start;
            let   count   = 0;

            const timer = setInterval(function() {
                count++;
                current += inc;
                el.textContent = Math.round(count >= steps ? end : current);
                if (count >= steps) clearInterval(timer);
            }, step);
        }

        // ── Fetch stats berdasarkan filter tahun ajaran ───────────────────────
        function loadStats(periodeId) {
            const statsUrl = '{{ route("admin.rekap.stats") }}';
            const url      = periodeId ? `${statsUrl}?periode=${periodeId}` : statsUrl;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    animateCounter(document.getElementById('statSiswaAktif'),   data.siswa_aktif);
                    animateCounter(document.getElementById('statSiswaSelesai'), data.siswa_selesai);
                    animateCounter(document.getElementById('statTotalSiswa'),   data.total_siswa);
                    animateCounter(document.getElementById('statTotalGuru'),    data.total_guru);
                })
                .catch(err => console.error('Gagal memuat statistik:', err));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterEl         = document.getElementById('filterPeriode');
            const badgeEl          = document.getElementById('activeFilterBadge');
            const badgeLabelEl     = document.getElementById('activeFilterLabel');
            const previewButtons   = document.querySelectorAll('.btn-preview-pdf');
            const pdfModalElement  = document.getElementById('previewPdfModal');
            const pdfModal         = new bootstrap.Modal(pdfModalElement);
            const downloadBtn      = document.getElementById('downloadPdfBtn');

            // ── Saat filter berubah ─────────────────────────────────────────
            filterEl.addEventListener('change', function() {
                const periodeId    = this.value;
                const periodeLabel = this.options[this.selectedIndex].text;

                // Update badge label
                if (periodeId) {
                    badgeLabelEl.textContent = periodeLabel;
                    badgeEl.style.display    = 'block';
                } else {
                    badgeEl.style.display    = 'none';
                }

                loadStats(periodeId);
            });

            // ── Tombol preview PDF (sertakan filter) ───────────────────────
            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url    = this.getAttribute('data-url');
                    const periode = filterEl.value;
                    if (!url) return;

                    let finalUrl = url;
                    if (periode) {
                        finalUrl += (finalUrl.includes('?') ? '&' : '?') + 'periode=' + periode;
                    }

                    downloadBtn.href = finalUrl + (finalUrl.includes('?') ? '&' : '?') + 'download=1';
                    pdfModal.show();
                    renderPDF(finalUrl);
                });
            });

            pdfModalElement.addEventListener('hidden.bs.modal', function() {
                const container = document.getElementById('pdfCanvasContainer');
                container.querySelectorAll('canvas').forEach(c => c.remove());
            });
        });
    </script>
@endpush
