@extends('layouts.nav.siswa')

@section('title', 'Laporan & Penilaian - SIM Magang')
@section('body-class', 'laporan-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/laporan-siswa.css') }}">
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
                                <a href="{{ asset('storage/' . $laporanAkhir->file) }}" target="_blank" class="tab-button"
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

                    <!-- Download Options -->
                    <div class="ui-card">
                        <h5 class="page-title" style="margin-bottom: 1.5rem;">Unduh Laporan Kegiatan</h5>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <a href="{{ route('siswa.rekap.jurnal') }}" target="_blank" class="btn-download-premium" style="margin-bottom: 1rem;">
                                <div class="icon-box-white">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h6>Unduh Jurnal Kegiatan Mingguan</h6>
                                    <small>Hasilkan file jurnal kegiatan dengan kolom Pembimbing Lapangan.</small>
                                </div>
                            </a>

                            <a href="{{ route('siswa.rekap.individu') }}" target="_blank" class="btn-download-premium" style="margin-bottom: 1rem;">
                                <div class="icon-box-white">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <h6>Unduh Rekap Absensi (Individu)</h6>
                                    <small>Hasilkan rekap absensi pribadi (Tanggal, Presensi, Status).</small>
                                </div>
                            </a>

                            <a href="{{ route('siswa.rekap.kelompok') }}" target="_blank" class="btn-download-premium">
                                <div class="icon-box-white">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h6>Unduh Rekap Absensi (Berkelompok)</h6>
                                    <small>Hasilkan rekap absensi bulanan untuk seluruh anggota kelompok.</small>
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
                                        <a href="{{ route('siswa.penilaian.cetak', ['id_penilaian' => $penilaian->id_penilaian]) }}" 
                                           class="btn-unduh-sm">
                                            <i class="fas fa-download"></i>
                                            <span>Unduh PDF</span>
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