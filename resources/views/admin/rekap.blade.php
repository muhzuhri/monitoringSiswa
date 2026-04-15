@extends('layouts.nav.admin')

@section('title', 'Pusat Rekapitulasi Data - Admin')
@section('body-class', 'dashboard-page admin-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/rekap.css') }}">
@endpush

@section('body')
    <div class="dashboard-container">
        <div class="admin-content-wrapper">
            <div class="rekap-header d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                <div>
                    <h2 class="rekap-title">Pusat Rekapitulasi Data</h2>
                    <p class="rekap-subtitle mb-0">Kelola dan unduh laporan data mahasiswa/siswa serta guru pembimbing dalam format PDF.</p>
                </div>
                <div class="filter-box" style="min-width: 250px;">
                    <label class="form-label fw-bold small text-muted mb-1 text-uppercase">Filter Tahun Ajaran</label>
                    <select id="filterPeriode" class="form-select border-0 shadow-sm rounded-pill px-3">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id_tahun_ajaran }}">{{ $ta->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Custom Tabs Navigation -->
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

            <div class="tab-content">
                <!-- Tab Siswa -->
                <div class="tab-pane fade show active" id="siswa-pane" role="tabpanel">
                    <div class="rekap-grid">
                        <!-- Siswa Aktif -->
                        <div class="rekap-card">
                            <div class="rekap-card-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="rekap-card-count">{{ $stats['siswa_aktif'] }}</div>
                            <h5 class="rekap-card-title">Mahasiswa/Siswa Aktif</h5>
                            <a href="javascript:void(0)" data-url="{{ route('admin.rekap.siswaAktif') }}" class="rekap-btn-download btn-preview-pdf">
                                <i class="fas fa-file-pdf"></i>
                                <span>Preview Rekap</span>
                            </a>
                        </div>

                        <!-- Siswa Selesai -->
                        <div class="rekap-card">
                            <div class="rekap-card-icon" style="background: #ecfdf5; color: #10b981;">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="rekap-card-count">{{ $stats['siswa_selesai'] }}</div>
                            <h5 class="rekap-card-title">Siswa Selesai Magang</h5>
                            <a href="javascript:void(0)" data-url="{{ route('admin.rekap.siswaSelesai') }}" class="rekap-btn-download btn-preview-pdf">
                                <i class="fas fa-file-pdf"></i>
                                <span>Preview Rekap</span>
                            </a>
                        </div>

                        <!-- Total Siswa -->
                        <div class="rekap-card">
                            <div class="rekap-card-icon" style="background: #f1f5f9; color: #475569;">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="rekap-card-count">{{ $stats['total_siswa'] }}</div>
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
                            <div class="rekap-card-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="rekap-card-count">{{ $stats['total_guru'] }}</div>
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

    <!-- Modal Preview PDF -->
    <div class="modal fade" id="previewPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title mb-0">Pratinjau Laporan Rekapitulasi</h6>
                    </div>
                    
                    <div class="pdf-viewer-actions">
                        <a id="downloadPdfBtn" href="#" class="btn-pdf-action">
                            <i class="fas fa-download"></i> Unduh PDF
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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

        document.addEventListener('DOMContentLoaded', function() {
            const previewButtons = document.querySelectorAll('.btn-preview-pdf');
            const pdfModalElement = document.getElementById('previewPdfModal');
            const pdfModal = new bootstrap.Modal(pdfModalElement);
            const downloadBtn = document.getElementById('downloadPdfBtn');

            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    const periode = document.getElementById('filterPeriode').value;
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
