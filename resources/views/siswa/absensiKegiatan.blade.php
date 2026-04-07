@extends('layouts.nav.siswa')

@section('title', 'Absensi & Kegiatan - SIM Magang')
@section('body-class', 'absensi-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/absensiKegiatan-siswa.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">
        <div class="page-header">
            <div>
                <h2 class="page-title">Absensi & Kegiatan Harian</h2>
                <p class="page-subtitle">
                    Kelola kehadiran dan laporan aktivitas magang kamu di sini.
                </p>
            </div>

            <div class="current-date-badge">
                <i class="fas fa-calendar-alt"></i>
                <span id="currentDateDisplay">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>


        <!-- Custom Tabs -->
        <div class="tabs-wrapper">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="tab-absensi" data-bs-toggle="pill" data-bs-target="#pills-absensi"
                    type="button" role="tab" aria-selected="true">

                    <i class="fas fa-clock"></i>
                    <span>Absensi Harian</span>
                </button>

                <button class="tab-button" id="tab-logbook" data-bs-toggle="pill" data-bs-target="#pills-logbook"
                    type="button" role="tab" aria-selected="false">

                    <i class="fas fa-book-open"></i>
                    <span>Logbook / Jurnal</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">

            <!-- TAB ABSENSI -->
            <div class="tab-pane fade show active" id="pills-absensi" role="tabpanel">
                <div class="content-grid">
                    <!-- Left: Action Card -->
                    <div class="action-section">
                        <div class="ui-card action-card">
                            @if (session('success'))
                                <div class="ui-alert ui-alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ session('success') }}</span>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="ui-alert ui-alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>{{ session('error') }}</span>
                                </div>
                            @endif

                            <div class="clock-section">
                                <h5 class="clock-label">Waktu Saat Ini</h5>
                                <h1 class="real-time-clock" id="realtimeClock">--:--:--</h1>
                                <div class="location-badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Lokasi: {{ $user->perusahaan }}</span>
                                </div>
                            </div>

                            <div class="action-forms">
                                @if (!$absensiHariIni)
                                    <!-- Check In Form -->
                                    <form action="{{ route('siswa.absensi.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="masuk">
                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                <i class="fas fa-info-circle"></i> Status Kehadiran
                                            </label>
                                            <select name="status_pilihan" class="form-control-custom" required
                                                onchange="togglePhotoLabel(this.value)">
                                                <option value="hadir" selected>Hadir (Sesuai Jadwal)</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                <i class="fas fa-camera"></i> <span id="photoLabel">Foto Selfie / Bukti</span>
                                            </label>
                                            
                                                <input type="file" name="foto" class="form-control-custom" required
                                                    accept="image/*" capture="camera">
                                                <div class="form-help-text" id="photoHelp">Ambil foto Anda di lokasi magang atau upload surat keterangan.</div>
                                        </div>

                                        <script>
                                            function togglePhotoLabel(val) {
                                                const label = document.getElementById('photoLabel');
                                                const help = document.getElementById('photoHelp');
                                                if (val === 'hadir') {
                                                    label.innerText = 'Foto Selfie di Lokasi';
                                                    help.innerText = 'Ambil foto Anda di lokasi magang.';
                                                } else {
                                                    label.innerText = 'Foto Surat / Bukti Pendukung';
                                                    help.innerText = 'Unggah foto surat izin atau surat keterangan sakit.';
                                                }
                                            }
                                        </script>

                                        <button class="btn-action-main btn-checkin" type="submit">
                                            <div class="icon-box-white">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </div>
                                            <div class="btn-text-content">
                                                <span class="btn-title">CHECK-IN (MASUK)</span>
                                                <span class="btn-subtitle">Batas: 07:00 - 10:00</span>
                                            </div>
                                        </button>
                                    </form>
                                @elseif(!$absensiHariIni->jam_pulang)
                                    <!-- Check Out Form -->
                                    <form action="{{ route('siswa.absensi.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="pulang">

                                        <div class="form-group">
                                            <label class="form-label-custom">
                                                <i class="fas fa-camera"></i> Foto Bukti Pulang
                                            </label>
                                            <input type="file" name="foto" class="form-control-custom" required
                                                accept="image/*" capture="camera">
                                            <div class="form-help-text">Ambil foto bukti selesai kegiatan.</div>
                                        </div>

                                        <button class="btn-action-main btn-checkout" type="submit">
                                            <div class="icon-box-white">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </div>
                                            <div class="btn-text-content">
                                                <span class="btn-title">CHECK-OUT (PULANG)</span>
                                                <span class="btn-subtitle">Batas: 15:00 - 16:30</span>
                                            </div>
                                        </button>
                                    </form>
                                @else
                                    <div class="ui-alert ui-alert-info">
                                        <div class="icon-box-white" style="background: #0dcaf0; color: white;">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="btn-text-content" style="color: #055160;">
                                            <span class="btn-title">SELESAI</span>
                                            <span class="btn-subtitle">Anda sudah absen masuk & pulang hari ini.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: History & Status -->
                    <div class="history-section">
                        <div class="ui-card history-card">
                            <div class="section-header">
                                <i class="fas fa-history"></i>
                                <h5>Riwayat Absensi Terakhir</h5>
                            </div>
                            <div class="table-container">
                                <table class="ui-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Masuk</th>
                                            <th>Pulang</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($absensis as $absen)
                                            <tr>
                                                <td data-label="Tanggal">
                                                    {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('D, d M Y') }}
                                                </td>
                                                <td data-label="Masuk">
                                                    <div class="time-cell">
                                                        <span
                                                            class="time-val in">{{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '-' }}</span>
                                                        @if ($absen->foto_masuk)
                                                            <a href="{{ asset('storage/' . $absen->foto_masuk) }}"
                                                                target="_blank" class="img-link"
                                                                title="Lihat Foto Masuk">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td data-label="Pulang">
                                                    <div class="time-cell">
                                                        <span
                                                            class="time-val out">{{ $absen->jam_pulang ? \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') : '-' }}</span>
                                                        @if ($absen->foto_pulang)
                                                            <a href="{{ asset('storage/' . $absen->foto_pulang) }}"
                                                                target="_blank" class="img-link"
                                                                title="Lihat Foto Pulang">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    @if ($absen->verifikasi == 'verified')
                                                        <span class="badge-ui badge-success"
                                                            title="Sudah diverifikasi pembimbing">
                                                            <i class="fas fa-check-circle me-1"></i> Disetujui
                                                        </span>
                                                    @elseif($absen->verifikasi == 'rejected')
                                                        <span class="badge-ui badge-danger"
                                                            title="Ditolak: {{ $absen->keterangan }}">
                                                            <i class="fas fa-times-circle me-1"></i> Ditolak
                                                        </span>
                                                    @else
                                                        <span class="badge-ui badge-warning"
                                                            title="Menunggu verifikasi pembimbing">
                                                            <i class="fas fa-clock me-1"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4"
                                                    style="text-align: center; color: #999; padding: 3rem;">Belum ada
                                                    riwayat absensi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="history-footer">
                                <button class="btn-ripple btn-history-more" data-bs-toggle="modal"
                                    data-bs-target="#modalHistoryDetail">
                                    <i class="fas fa-expand-arrows-alt"></i> Lihat Riwayat Lengkap
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB LOGBOOK -->
            <div class="tab-pane fade" id="pills-logbook" role="tabpanel">
                <div class="content-grid logbook-grid">
                    <!-- Form Input Logbook -->
                    <div class="logbook-form-section">
                        <div class="ui-card sticky-card">
                            <div class="section-header">
                                <i class="fas fa-pen-nib"></i>
                                <h5>Isi Logbook Hari Ini</h5>
                            </div>
                            <form action="{{ route('siswa.logbook.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="form-label-custom">Tanggal Kegiatan</label>
                                    <input type="date" name="tanggal" class="form-control-custom"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-custom">Deskripsi Kegiatan</label>
                                    <textarea name="kegiatan" class="form-control-custom" rows="5"
                                        placeholder="Apa yang kamu kerjakan hari ini? (Detailkan tugas, kendala, atau hasil kerja)" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label-custom">Foto Bukti Kegiatan (Opsional)</label>
                                    <input type="file" name="foto" class="form-control-custom" accept="image/*">
                                    <div class="form-help-text">Lampirkan foto saat sedang mengerjakan tugas atau hasil
                                        kerja.</div>
                                </div>
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-save"></i> Simpan Logbook
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- History Logbook List -->
                    <div class="logbook-history-section">
                        <div class="ui-card">
                            <div class="section-header">
                                <h5>Riwayat Logbook (3 Hari Terakhir)</h5>
                            </div>

                            <div class="table-container">
                                <table class="ui-table">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kegiatan / Pekerjaan</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logbooks as $log)
                                            <tr>
                                                <td data-label="Tanggal" style="white-space: nowrap;">
                                                    <div class="fw-bold">
                                                        {{ \Carbon\Carbon::parse($log->tanggal)->translatedFormat('D, d M Y') }}
                                                    </div>
                                                </td>
                                                <td data-label="Kegiatan">
                                                    <div class="log-content-wrapper">
                                                        <div class="fw-bold text-dark">
                                                            {{ Str::limit($log->kegiatan, 100) }}</div>
                                                        @if ($log->catatan_pembimbing)
                                                            <div
                                                                class="small mt-1 p-2 rounded bg-light border-start border-3 border-primary">
                                                                <i class="fas fa-comment-dots text-primary me-1"></i>
                                                                <span class="text-muted">Catatan:</span>
                                                                "{{ $log->catatan_pembimbing }}"
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    @if ($log->status == 'verified')
                                                        <span class="badge-ui badge-success">Disetujui</span>
                                                    @elseif($log->status == 'rejected')
                                                        <span class="badge-ui badge-danger">Ditolak</span>
                                                    @else
                                                        <span class="badge-ui badge-warning">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3"
                                                    style="text-align: center; color: #999; padding: 3rem;">Belum ada
                                                    riwayat logbook.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Detai (Floating Above) -->
    <div class="modal fade" id="modalHistoryDetail" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-content-premium">
                <div class="modal-header-premium">
                    <div class="header-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="header-text">
                        <h5 class="modal-title">Riwayat Lengkap</h5>
                        <p class="modal-subtitle">Gunakan tombol Navigasi untuk melihat data sebelumnya.</p>
                    </div>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <!-- Tab Switcher inside Modal -->
                    <div class="modal-tab-nav">
                        <button class="modal-tab-btn active" onclick="switchModalTab('absensi')">
                            <i class="fas fa-clock"></i> Absensi
                        </button>
                        <button class="modal-tab-btn" onclick="switchModalTab('kegiatan')">
                            <i class="fas fa-book"></i> Kegiatan
                        </button>
                    </div>

                    <!-- Navigation Bar -->
                    <div class="pagination-nav-bar">
                        <button id="btnPrevPage" class="btn-nav-page" onclick="changePage(1)">
                            <i class="fas fa-history"></i> Sebelumnya
                        </button>
                        <div class="page-indicator">
                            Halaman <span id="currentPageNum">1</span>
                        </div>
                        <button id="btnNextPage" class="btn-nav-page" onclick="changePage(-1)">
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Content Area -->
                    <div class="history-table-wrapper">
                        <div id="absensiTableArea">
                            <table class="history-detail-table">
                                <thead>
                                    <tr>
                                        <th>Hari & Tanggal</th>
                                        <th>Kehadiran</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="historyAbsensiBody">
                                    <!-- AJAX Content -->
                                </tbody>
                            </table>
                        </div>

                        <div id="kegiatanTableArea" style="display: none;">
                            <table class="history-detail-table">
                                <thead>
                                    <tr>
                                        <th>Hari & Tanggal</th>
                                        <th style="min-width: 250px;">Detail Kegiatan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="historyKegiatanBody">
                                    <!-- AJAX Content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Check In (Refactored) -->
    <div class="modal fade" id="modalCheckIn" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-custom">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title">Konfirmasi Check-In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-custom">
                    <form>
                        <div class="form-group">
                            <label class="form-label-custom">Kondisi Kesehatan</label>
                            <select class="form-control-custom">
                                <option>Sehat</option>
                                <option>Kurang Fit</option>
                                <option>Sakit (Izin)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label-custom">Bukti Foto (Selfie di Lokasi)</label>
                            <input type="file" class="form-control-custom" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label class="form-label-custom">Catatan Tambahan (Opsional)</label>
                            <textarea class="form-control-custom" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer modal-footer-custom">
                    <button type="button" class="btn-pill btn-pill-secondary" data-bs-dismiss="modal"
                        style="background: #e9ecef; border: none; color: #495057;">Batal</button>
                    <button type="button" class="btn-pill btn-pill-primary"
                        style="background: #198754; border: none; color: #ffffff;">Lakukan Check-In</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-GB', {
                hour12: false
            });
            document.getElementById('realtimeClock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Pagination & Tab State
        let currentModalTab = 'absensi';
        let currentAbsenPage = 1;
        let currentKegiatanPage = 1;
        let hasMoreAbsen = true;
        let hasMoreKegiatan = true;

        function switchModalTab(tab) {
            currentModalTab = tab;
            document.querySelectorAll('.modal-tab-btn').forEach(btn => btn.classList.remove('active'));
            if (tab === 'absensi') {
                document.querySelectorAll('.modal-tab-btn')[0].classList.add('active');
                document.getElementById('absensiTableArea').style.display = 'block';
                document.getElementById('kegiatanTableArea').style.display = 'none';
                updatePaginationUI(currentAbsenPage, hasMoreAbsen);
            } else {
                document.querySelectorAll('.modal-tab-btn')[1].classList.add('active');
                document.getElementById('absensiTableArea').style.display = 'none';
                document.getElementById('kegiatanTableArea').style.display = 'block';
                updatePaginationUI(currentKegiatanPage, hasMoreKegiatan);
            }
            fetchHistoryData();
        }

        async function fetchHistoryData() {
            const page = currentModalTab === 'absensi' ? currentAbsenPage : currentKegiatanPage;
            const route = currentModalTab === 'absensi' ? '{{ route('siswa.absensi.detail') }}' :
                '{{ route('siswa.logbook.detail') }}';
            const tbodyId = currentModalTab === 'absensi' ? 'historyAbsensiBody' : 'historyKegiatanBody';
            const tbody = document.getElementById(tbodyId);
            const cols = currentModalTab === 'absensi' ? 4 : 3;

            try {
                const response = await fetch(`${route}?page=${page}`);
                const result = await response.json();
                const data = result.data;

                if (currentModalTab === 'absensi') {
                    hasMoreAbsen = result.has_more;
                    updatePaginationUI(currentAbsenPage, hasMoreAbsen);
                } else {
                    hasMoreKegiatan = result.has_more;
                    updatePaginationUI(currentKegiatanPage, hasMoreKegiatan);
                }

                if (data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="${cols}" class="text-center p-5 text-muted"><i class="fas fa-folder-open mb-3 d-block" style="font-size: 2rem; opacity: 0.3;"></i> Tidak ada data di halaman ini.</td></tr>`;
                    return;
                }

                if (currentModalTab === 'absensi') {
                    tbody.innerHTML = data.map(item => `
                            <tr>
                                <td data-label="Tanggal"><div class="date-col"><strong>${item.tanggal.split(',')[0]}</strong><span>${item.tanggal.split(',')[1]}</span></div></td>
                                <td data-label="Waktu">
                                    <div class="arrival-time-stack">
                                        <div class="time-in"><i class="fas fa-sign-in-alt"></i> ${item.jam_masuk}</div>
                                        <div class="time-out"><i class="fas fa-sign-out-alt"></i> ${item.jam_pulang}</div>
                                    </div>
                                </td>
                                <td data-label="Status" class="text-center">
                                    ${item.verifikasi === 'verified' 
                                        ? `<span class="badge-ui badge-success"><i class="fas fa-check-circle me-1"></i> Disetujui</span>`
                                        : item.verifikasi === 'rejected'
                                        ? `<span class="badge-ui badge-danger"><i class="fas fa-times-circle me-1"></i> Ditolak</span>`
                                        : `<span class="badge-ui badge-warning"><i class="fas fa-clock me-1"></i> Pending</span>`
                                    }
                                </td>
                                <td data-label="Aksi" class="text-center">
                                    <div class="action-btns-row">
                                        ${item.foto_masuk ? `<a href="${item.foto_masuk}" target="_blank" class="btn-preview-mini m" title="Foto Masuk"><i class="fas fa-camera"></i></a>` : ''}
                                        ${item.foto_pulang ? `<a href="${item.foto_pulang}" target="_blank" class="btn-preview-mini p" title="Foto Pulang"><i class="fas fa-camera"></i></a>` : ''}
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                } else {
                    tbody.innerHTML = data.map(item => `
                            <tr>
                                <td data-label="Tanggal"><div class="date-col"><strong>${item.tanggal.split(',')[0]}</strong><span>${item.tanggal.split(',')[1]}</span></div></td>
                                <td data-label="Kegiatan">
                                    <div class="activity-detail-preview">
                                        <p class="act-text">${item.kegiatan}</p>
                                        ${item.catatan ? `<div class="pembimbing-note"><i class="fas fa-comment-dots"></i> "${item.catatan}"</div>` : ''}
                                    </div>
                                </td>
                                <td data-label="Status"><span class="badge ${getLogStatusClass(item.status)}">${item.status}</span></td>
                            </tr>
                        `).join('');
                }

            } catch (error) {
                console.error('Error fetching data:', error);
                tbody.innerHTML =
                    `<tr><td colspan="${cols}" class="text-center p-5 text-danger">Gagal memuat data.</td></tr>`;
            }
        }

        function changePage(direction) {
            if (currentModalTab === 'absensi') {
                if (direction === 1 && hasMoreAbsen) currentAbsenPage++;
                else if (direction === -1 && currentAbsenPage > 1) currentAbsenPage--;
            } else {
                if (direction === 1 && hasMoreKegiatan) currentKegiatanPage++;
                else if (direction === -1 && currentKegiatanPage > 1) currentKegiatanPage--;
            }
            fetchHistoryData();
        }

        function updatePaginationUI(page, hasMore) {
            document.getElementById('currentPageNum').innerText = page;

            // "Sebelumnya" (Previous) means Older Data (Increment Page)
            document.getElementById('btnPrevPage').disabled = !hasMore;
            // "Selanjutnya" (Next) means Newer Data (Decrement Page)
            document.getElementById('btnNextPage').disabled = (page === 1);

            document.getElementById('btnPrevPage').style.opacity = !hasMore ? '0.5' : '1';
            document.getElementById('btnNextPage').style.opacity = (page === 1) ? '0.5' : '1';
        }

        function getStatusBadgeClass(status) {
            status = status.toLowerCase();
            if (status.includes('hadir')) return 'badge-ui badge-success';
            if (status.includes('izin')) return 'badge-ui badge-info';
            if (status.includes('sakit')) return 'badge-ui badge-warning';
            return 'badge-ui badge-danger';
        }

        function getLogStatusClass(status) {
            if (status === 'verified') return 'badge-ui badge-success';
            if (status === 'rejected') return 'badge-ui badge-danger';
            return 'badge-ui badge-warning';
        }

        // Auto fetch when modal opens
        var historyModal = document.getElementById('modalHistoryDetail');
        historyModal.addEventListener('shown.bs.modal', function() {
            fetchHistoryData();
        });
    </script>
@endsection
