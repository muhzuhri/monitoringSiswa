@extends('layouts.nav.siswa')

@section('title', 'Laporan & Penilaian - SIM Magang')
@section('body-class', 'laporan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/laporan-siswa.css') }}?v={{ time() }}">
@endpush

@section('body')
    <div class="page-wrapper">
        <div class="page-header">
            <div>
                <h2 class="page-title">Pusat Laporan & Penilaian</h2>
                <p class="page-subtitle">Pantau hasil penilaian dan kelola laporan akhir magang kamu.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="ui-alert ui-alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="ui-alert ui-alert-info">
                <i class="fas fa-info-circle"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- Custom Tabs Navigation -->
        <div class="tabs-wrapper">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="tab-laporan" data-bs-toggle="pill" data-bs-target="#pills-laporan"
                    type="button" role="tab" aria-selected="true">
                    <i class="fas fa-file-export"></i>
                    <span>Laporan Akhir</span>
                </button>

                <button class="tab-button" id="tab-rekap" data-bs-toggle="pill" data-bs-target="#pills-rekap" type="button"
                    role="tab" aria-selected="false">
                    <i class="fas fa-history"></i>
                    <span>Riwayat & Rekap</span>
                </button>

                <button class="tab-button" id="tab-penilaian" data-bs-toggle="pill" data-bs-target="#pills-penilaian" type="button"
                    role="tab" aria-selected="false">
                    <i class="fas fa-star"></i>
                    <span>Hasil Penilaian</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">


            <!-- TAB LAPORAN AKHIR -->
            <div class="tab-pane fade show active" id="pills-laporan" role="tabpanel">
                <div class="laporan-grid" style="max-width: 800px; margin: 0 auto;">
                    <div class="ui-card">
                        <h5 class="page-title" style="margin-bottom: 1.5rem;">Pengumpulan Laporan Akhir Magang</h5>

                        @if($laporanAkhir)
                            <div class="file-status-bar">
                                <div
                                    class="status-icon {{ $laporanAkhir->status == 'approved' ? 'status-approved' : ($laporanAkhir->status == 'rejected' ? 'status-rejected' : 'status-pending') }}">
                                    <i
                                        class="fas {{ $laporanAkhir->status == 'approved' ? 'fa-check' : ($laporanAkhir->status == 'rejected' ? 'fa-times' : 'fa-clock') }}"></i>
                                </div>
                                <div style="flex-grow: 1;">
                                    <h6 style="margin: 0; font-weight: 700;">File: {{ basename($laporanAkhir->file) }}</h6>
                                    <small class="page-subtitle">Status: <span
                                            style="text-transform: capitalize; font-weight: 700;">{{ $laporanAkhir->status }}</span></small>
                                </div>
                                <a href="javascript:void(0)" data-url="{{ route('siswa.laporan.downloadAkhir') }}" class="tab-button btn-preview-pdf"
                                    style="padding: 8px 16px; font-size: 0.8rem; background: #f1f5f9;">
                                    Lihat File
                                </a>
                            </div>

                            @if($laporanAkhir->catatan)
                                <div class="ui-alert {{ $laporanAkhir->status == 'rejected' ? 'ui-alert-danger' : 'ui-alert-info' }}"
                                    style="padding: 1rem;">
                                    <div>
                                        <h6 style="margin: 0 0 4px 0; font-size: 0.8rem; font-weight: 700;">Catatan Pembimbing:</h6>
                                        <p style="margin: 0; font-size: 0.85rem;">{{ $laporanAkhir->catatan }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($laporanAkhir->status != 'approved')
                                <div style="text-align: center; margin-bottom: 1.5rem;">
                                    <p class="page-subtitle" style="font-size: 0.85rem;">Ingin mengganti file? Silakan upload
                                        kembali di bawah ini.</p>
                                </div>
                            @else
                                <div class="ui-alert ui-alert-success"
                                    style="flex-direction: column; text-align: center; padding: 2rem;">
                                    <i class="fas fa-award fa-3x" style="margin-bottom: 1rem;"></i>
                                    <h5 style="font-weight: 800; margin-bottom: 0.5rem;">Laporan Sudah Disetujui!</h5>
                                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.8;">Selamat! Laporan akhir magang Anda telah
                                        diverifikasi oleh pembimbing.</p>
                                </div>
                            @endif
                        @endif

                        @if(!$laporanAkhir || $laporanAkhir->status != 'approved')
                            <form action="{{ route('siswa.laporan.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h6>Klik untuk pilih file laporan</h6>
                                    <p class="page-subtitle" style="font-size: 0.8rem;">Format: PDF, DOC, DOCX (Maks. 5MB)</p>
                                    <input type="file" name="file_laporan" id="fileInput" class="d-none"
                                        onchange="document.getElementById('fileName').innerHTML = this.files[0].name" required>
                                    <div id="fileName"
                                        style="margin-top: 10px; font-weight: 700; color: #0d6efd; font-size: 0.9rem;"></div>
                                </div>
                                <button type="submit" class="btn-upload-submit" style="width: 100%;">
                                    <i class="fas fa-upload" style="margin-right: 8px;"></i> Upload Laporan Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB REKAP -->
            <div class="tab-pane fade" id="pills-rekap" role="tabpanel">
                <div class="laporan-grid">
                    <!-- Rekap Kehadiran Bulan Ini -->
                    <div class="ui-card">
                        <h5 class="page-title" style="margin-bottom: 1.5rem;">Rekap Absensi (Bulan Ini)</h5>
                        <div class="rekap-grid">
                            <div class="rekap-box rekap-hadir">
                                <span class="rekap-value">{{ $rekapAbsensi['hadir'] }}</span>
                                <span class="rekap-label">Hadir</span>
                            </div>
                            <div class="rekap-box rekap-izin">
                                <span class="rekap-value">{{ $rekapAbsensi['izin'] }}</span>
                                <span class="rekap-label">Izin</span>
                            </div>
                            <div class="rekap-box rekap-sakit">
                                <span class="rekap-value">{{ $rekapAbsensi['sakit'] }}</span>
                                <span class="rekap-label">Sakit</span>
                            </div>
                            <div class="rekap-box rekap-alpa">
                                <span class="rekap-value">{{ $rekapAbsensi['alpa'] }}</span>
                                <span class="rekap-label">Alpa</span>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Options -->
                    <div class="ui-card">
                        <h5 class="page-title" style="margin-bottom: 1.5rem;">Lihat & Cetak Laporan Kegiatan</h5>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.jurnal') }}" class="btn-download-premium btn-preview-pdf" style="margin-bottom: 1rem;">
                                <div class="icon-box-white">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h6>Lihat Jurnal Kegiatan Mingguan</h6>
                                    <small>Lihat/Cetak file jurnal kegiatan dengan kolom Pembimbing Lapangan.</small>
                                </div>
                            </a>

                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.individu') }}" class="btn-download-premium btn-preview-pdf" style="margin-bottom: 1rem;">
                                <div class="icon-box-white">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <h6>Lihat Rekap Absensi (Individu)</h6>
                                    <small>Lihat/Cetak rekap absensi pribadi (Tanggal, Presensi, Status).</small>
                                </div>
                            </a>

                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.kelompok') }}" class="btn-download-premium btn-preview-pdf">
                                <div class="icon-box-white">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6>Lihat Rekap Absensi (Berkelompok)</h6>
                                    <small>Lihat/Cetak rekap absensi bulanan untuk seluruh anggota kelompok.</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB HASIL PENILAIAN -->
            <div class="tab-pane fade" id="pills-penilaian" role="tabpanel">
                <div class="laporan-grid" style="max-width: 900px; margin: 0 auto;">
                    <div class="ui-card">
                        <h5 class="page-title" style="margin-bottom: 1.5rem;">Rekap Penilaian Siswa</h5>
                        
                        @if($user->status == 'selesai')
                            <div class="ui-alert ui-alert-success mb-4" style="padding: 1.5rem; border: 2px solid var(--primary);">
                                <div style="display: flex; align-items: center; gap: 1.5rem;">
                                    <div style="background: var(--primary); color: white; width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div style="flex-grow: 1;">
                                        <h5 style="margin: 0; font-weight: 800;">Selamat, Magang Telah Selesai!</h5>
                                        <p style="margin: 0; opacity: 0.8;">Kamu telah menyelesaikan seluruh rangkaian kegiatan magang. Silakan unduh sertifikat kamu di bawah ini.</p>
                                    </div>
                                    <a href="javascript:void(0)" data-url="{{ route('siswa.sertifikat.cetak') }}" class="btn-unduh-sm btn-preview-pdf" style="background: var(--primary); color: white; padding: 10px 20px;">
                                        <i class="fas fa-award"></i>
                                        <span>Cetak Sertifikat</span>
                                    </a>

                                </div>
                            </div>
                        @endif

                        <p class="page-subtitle mb-4">Berikut adalah daftar penilaian yang telah diberikan oleh Pembimbing Lapangan dan Guru Pembimbing.</p>


                        <div class="assessment-list">
                            @forelse($penilaians as $penilaian)
                                <div class="assessment-item">
                                    <div class="assessment-info">
                                        <div class="assessment-icon">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div class="assessment-details">
                                            <h6>{{ $penilaian->kategori }}</h6>
                                            <p>{{ \Carbon\Carbon::parse($penilaian->created_at)->translatedFormat('d F Y') }}</p>
                                        </div>
                                    </div>

                                    <div class="assessment-meta">
                                        <div class="meta-item">
                                            <span class="meta-label">Pemberi Nilai</span>
                                            <span class="badge {{ $penilaian->pemberi_nilai == 'Dosen Pembimbing' ? 'bg-purple-light text-purple' : 'bg-primary-light text-primary' }}" style="padding: 6px 12px; border-radius: 8px;">
                                                {{ $penilaian->pemberi_nilai }}
                                            </span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Rata-rata</span>
                                            <span class="meta-value h5 mb-0" style="color: var(--primary);">{{ number_format($penilaian->rata_rata, 1) }}</span>
                                        </div>
                                    </div>

                                    <div class="assessment-action">
                                        <a href="javascript:void(0)" data-url="{{ route('siswa.penilaian.cetak', ['id_penilaian' => $penilaian->id_penilaian]) }}" 
                                           class="btn-unduh-sm btn-preview-pdf">
                                            <i class="fas fa-eye"></i>
                                            <span>Lihat PDF</span>
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="opacity-50 mb-3">
                                        <i class="fas fa-inbox fa-3x" style="color: var(--primary);"></i>
                                    </div>
                                    <h6 class="text-muted">Belum ada hasil penilaian yang tersedia.</h6>
                                    <p class="page-subtitle">Penilaian akan muncul di sini setelah divalidasi pembimbing.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Modal Preview PDF -->
    <div class="modal fade preview-pdf-modal" id="previewPdfModal" tabindex="-1" aria-labelledby="previewPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-pdf-viewer">
            <div class="modal-content">
                <div class="modal-header pdf-viewer-header">
                    <div class="pdf-viewer-title">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h6 class="modal-title" id="previewPdfModalLabel">Preview Laporan</h6>
                    </div>
                    
                    <div class="pdf-viewer-actions">
                        <div class="pdf-desktop-actions">
                            <a id="downloadPdfBtn" href="#" class="btn-pdf-action" title="Unduh File">
                                <i class="fas fa-download"></i> <span>Unduh Laporan</span>
                            </a>
                        </div>
                        
                        <div class="pdf-mobile-actions">
                             <a id="downloadPdfBtnMobile" href="#" class="btn-pdf-mobile-icon"><i class="fas fa-download"></i></a>
                        </div>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body pdf-viewer-body">
                    <div id="pdfCanvasContainer">
                        <div id="pdfLoadingIndicator">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p>Memuat PDF...</p>
                        </div>
                        <div id="pdfErrorMsg" style="display:none;">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Gagal memuat file PDF.<br><small>Coba gunakan tombol Unduh.</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Konfigurasi PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let currentPdfUrl = '';

        // Render semua halaman PDF ke dalam canvas menggunakan PDF.js
        // Bekerja di semua browser termasuk mobile Chrome dan iOS Safari
        async function renderPDF(url) {
            const container = document.getElementById('pdfCanvasContainer');
            const loadingEl = document.getElementById('pdfLoadingIndicator');
            const errorEl = document.getElementById('pdfErrorMsg');

            // Bersihkan canvas halaman sebelumnya
            container.querySelectorAll('canvas').forEach(c => c.remove());
            loadingEl.style.display = 'block';
            errorEl.style.display = 'none';
            container.scrollTop = 0;

            try {
                const pdfDoc = await pdfjsLib.getDocument(url).promise;
                loadingEl.style.display = 'none';

                const containerWidth = container.clientWidth - 24;
                const outputScale = window.devicePixelRatio || 1; // Deteksi kepadatan piksel layar (HD/Retina)

                // Render setiap halaman PDF ke canvas
                for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                    const page = await pdfDoc.getPage(pageNum);
                    
                    // Hitung skala agar pas dengan lebar container
                    const unscaledViewport = page.getViewport({ scale: 1 });
                    const baseScale = containerWidth / unscaledViewport.width;
                    
                    // Gunakan skala yang lebih tinggi untuk render (agar tajam), tapi tampilkan sesuai ukuran layout
                    const viewport = page.getViewport({ scale: baseScale * outputScale });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    
                    // Ukuran internal canvas (piksel fisik) - ditingkatkan untuk kualitas HD
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    
                    // Ukuran tampilan CSS (piksel logis) - tetap sesuai layout
                    canvas.style.width = (viewport.width / outputScale) + 'px';
                    canvas.style.height = (viewport.height / outputScale) + 'px';

                    container.appendChild(canvas);

                    // Render dengan kualitas tinggi
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
            const downloadBtnMobile = document.getElementById('downloadPdfBtnMobile');
            const printBtnMobile = document.getElementById('printPdfBtnMobile');

            previewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    if (!url) return;

                    // Set link unduh
                    const downloadUrl = url.includes('?') ? url + '&download=1' : url + '?download=1';
                    if (downloadBtn) downloadBtn.href = downloadUrl;
                    if (downloadBtnMobile) downloadBtnMobile.href = downloadUrl;

                    currentPdfUrl = url;

                    // Tampilkan modal dan render PDF (bekerja di desktop & mobile)
                    pdfModal.show();
                    renderPDF(url);
                });
            });

            // Cetak: buka di tab baru agar printer native device bisa digunakan
            const doPrint = function() {
                if (currentPdfUrl) window.open(currentPdfUrl, '_blank');
            };
            if (printBtnMobile) printBtnMobile.addEventListener('click', doPrint);

            // Bersihkan canvas saat modal ditutup
            pdfModalElement.addEventListener('hidden.bs.modal', function() {
                const container = document.getElementById('pdfCanvasContainer');
                container.querySelectorAll('canvas').forEach(c => c.remove());
                document.getElementById('pdfLoadingIndicator').style.display = 'none';
                document.getElementById('pdfErrorMsg').style.display = 'none';
                currentPdfUrl = '';
            });
        });
    </script>
@endpush
