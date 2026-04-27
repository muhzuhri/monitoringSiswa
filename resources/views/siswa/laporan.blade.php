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
                <div class="laporan-grid max-w-800 mx-auto">
                    <div class="ui-card">
                        <h5 class="page-title mb-1-5">Pengumpulan Laporan Akhir Magang</h5>

                        @if($laporanAkhir)
                            <div class="file-status-bar">
                                <div
                                    class="status-icon {{ $laporanAkhir->status == 'approved' ? 'status-approved' : ($laporanAkhir->status == 'rejected' ? 'status-rejected' : 'status-pending') }}">
                                    <i
                                        class="fas {{ $laporanAkhir->status == 'approved' ? 'fa-check' : ($laporanAkhir->status == 'rejected' ? 'fa-times' : 'fa-clock') }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="m-0 fw-700">File: {{ basename($laporanAkhir->file) }}</h6>
                                    <small class="page-subtitle">Status: <span
                                            class="text-capitalize fw-700">{{ $laporanAkhir->status }}</span></small>
                                </div>
                                <a href="javascript:void(0)" data-url="{{ route('siswa.laporan.downloadAkhir') }}" class="tab-button btn-preview-pdf btn-preview-inline">
                                    Lihat File
                                </a>
                            </div>

                            @if($laporanAkhir->catatan)
                                <div class="ui-alert {{ $laporanAkhir->status == 'rejected' ? 'ui-alert-danger' : 'ui-alert-info' }} alert-compact">
                                    <div>
                                        <h6 class="m-0 mb-4 fs-0-8 fw-700">Catatan Pembimbing:</h6>
                                        <p class="m-0 fs-0-85">{{ $laporanAkhir->catatan }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($laporanAkhir->status != 'approved')
                                <div class="text-center mb-1-5">
                                    <p class="page-subtitle fs-0-85">Ingin mengganti file? Silakan upload
                                        kembali di bawah ini.</p>
                                </div>
                            @else
                                <div class="ui-alert ui-alert-success flex-col text-center p-5">
                                    <i class="fas fa-award fa-3x mb-1"></i>
                                    <h5 class="fw-800 mb-1">Laporan Sudah Disetujui!</h5>
                                    <p class="m-0 fs-0-9 opacity-75">Selamat! Laporan akhir magang Anda telah
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
                                    <p class="page-subtitle fs-0-8">Format: PDF, DOC, DOCX (Maks. 5MB)</p>
                                    <input type="file" name="file_laporan" id="fileInput" class="d-none" required>
                                    <div id="fileName" class="mt-10 fw-700 text-primary fs-0-9"></div>
                                </div>
                                <button type="submit" class="btn-upload-submit w-100">
                                    <i class="fas fa-upload mr-2"></i> Upload Laporan Sekarang
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
                        <h5 class="page-title mb-1-5">Rekap Absensi (Bulan Ini)</h5>
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
                        <h5 class="page-title mb-1-5">Lihat & Cetak Laporan Kegiatan</h5>
                        <div class="d-flex flex-col gap-1-5">
                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.jurnal') }}" class="btn-download-premium btn-preview-pdf mb-1">
                                <div class="icon-box-white">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6>Lihat Jurnal Kegiatan Mingguan</h6>
                                    <small>Lihat/Cetak file jurnal kegiatan dengan kolom Pembimbing Lapangan.</small>
                                </div>
                            </a>

                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.individu') }}" class="btn-download-premium btn-preview-pdf mb-1">
                                <div class="icon-box-white">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6>Lihat Rekap Absensi (Individu)</h6>
                                    <small>Lihat/Cetak rekap absensi pribadi (Tanggal, Presensi, Status).</small>
                                </div>
                            </a>

                            <a href="javascript:void(0)" data-url="{{ route('siswa.rekap.kelompok') }}" class="btn-download-premium btn-preview-pdf">
                                <div class="icon-box-white">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="flex-grow-1">
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
                <div class="laporan-grid max-w-900 mx-auto">
                    <div class="ui-card">
                        <h5 class="page-title mb-1-5">Rekap Penilaian Siswa</h5>
                        
                        @if($user->status == 'selesai')
                            <div class="ui-alert ui-alert-success mb-4 alert-premium">
                                <div class="d-flex align-center gap-1-5">
                                    <div class="icon-box-primary">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="m-0 fw-800">Selamat, Magang Telah Selesai!</h5>
                                        <p class="m-0 opacity-75">Kamu telah menyelesaikan seluruh rangkaian kegiatan magang. Silakan unduh sertifikat kamu di bawah ini.</p>
                                    </div>
                                    <a href="javascript:void(0)" data-url="{{ route('siswa.sertifikat.cetak') }}" class="btn-unduh-sm btn-preview-pdf text-white bg-primary p-3">
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
                                            <span class="badge {{ $penilaian->pemberi_nilai == 'Dosen Pembimbing' ? 'bg-purple-light text-purple' : 'bg-primary-light text-primary' }} badge-meta">
                                                {{ $penilaian->pemberi_nilai }}
                                            </span>
                                        </div>
                                        <div class="meta-item">
                                            <span class="meta-label">Rata-rata</span>
                                            <span class="meta-value h5 mb-0 text-primary">{{ number_format($penilaian->rata_rata, 1) }}</span>
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
                                        <i class="fas fa-inbox fa-3x text-primary"></i>
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
                            <div class="loader-logo-container">
                                <img src="{{ asset('images/unsri-pride.png') }}" alt="UNSRI">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="{{ asset('assets/js/siswa/laporan-siswa.js') }}?v={{ time() }}"></script>
@endpush
