@extends('layouts.nav.siswa')

@section('title', 'Pengajuan Lupa Absensi / Kegiatan - SIM Magang')
@section('body-class', 'pengajuan-page siswa-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/siswa/pengajuan.css') }}">
@endpush

@section('body')
<div class="page-body">
    <div class="main-container">
        
        <div class="page-header animate-fade-in">
            <div class="header-content">
                <h3 class="header-title">Pengajuan Lupa Isi<span class="dot-primary">.</span></h3>
                <p class="header-subtitle">Laporkan absensi atau kegiatan yang terlewat untuk diverifikasi pembimbing.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="status-alert alert-success animate-fade-in" role="alert">
                <i class="fas fa-check-circle alert-icon"></i>
                <span class="alert-message">{{ session('success') }}</span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="status-alert alert-danger animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <span class="alert-message">{{ session('error') }}</span>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        @if($errors->any())
            <div class="status-alert alert-danger animate-fade-in" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <div class="alert-message">
                    <div class="error-title">Terdapat Kesalahan!</div>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="content-layout">
            <!-- Form Pengajuan -->
            <div class="form-column">
                <div class="premium-card animate-fade-in">
                    <div class="card-header-premium">
                        <div class="header-title-wrapper">
                            <div class="icon-circle-premium">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h5 class="card-title-premium">Form Pengajuan</h5>
                        </div>
                    </div>
                    <div class="card-body-premium">
                        <form action="{{ route('siswa.pengajuan.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="form-field">
                                <label class="field-label">Jenis Lupa <span class="required-mark">*</span></label>
                                <select class="custom-select" name="jenis" id="jenis_pengajuan" required>
                                    <option value="" disabled {{ old('jenis') == '' ? 'selected' : '' }}>-- Pilih Jenis --</option>
                                    <option value="absensi" {{ old('jenis') == 'absensi' ? 'selected' : '' }}>Lupa Absensi</option>
                                    <option value="kegiatan" {{ old('jenis') == 'kegiatan' ? 'selected' : '' }}>Lupa Logbook / Kegiatan</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Tanggal Lupa <span class="required-mark">*</span></label>
                                <input type="date" class="custom-input" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Fields for Absensi -->
                            <div id="fields_absensi" class="nested-fields" @if(old('jenis') != 'absensi') style="display: none;" @endif>
                                <div class="field-row">
                                    <div class="field-half">
                                        <label class="field-label">Jam Masuk</label>
                                        <input type="time" class="custom-input" name="jam_masuk" value="{{ old('jam_masuk') }}">
                                        <small class="field-hint">Kosongkan jika hanya lupa pulang</small>
                                    </div>
                                    <div class="field-half">
                                        <label class="field-label">Jam Pulang</label>
                                        <input type="time" class="custom-input" name="jam_pulang" value="{{ old('jam_pulang') }}">
                                        <small class="field-hint">Kosongkan jika hanya lupa masuk</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Fields for Kegiatan -->
                            <div id="fields_kegiatan" class="nested-fields-kegiatan" @if(old('jenis') != 'kegiatan') style="display: none;" @endif>
                                <div class="form-field">
                                    <label class="field-label">Deskripsi Kegiatan <span class="required-mark">*</span></label>
                                    <textarea class="custom-textarea" name="deskripsi" rows="3" placeholder="Jelaskan secara singkat kegiatan yang Anda lakukan hari itu...">{{ old('deskripsi') }}</textarea>
                                </div>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Alasan Keterlambatan Pengisian <span class="required-mark">*</span></label>
                                <textarea class="custom-textarea" name="alasan_terlambat" rows="2" required placeholder="Mengapa Anda baru mengisi sekarang?">{{ old('alasan_terlambat') }}</textarea>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Foto / Bukti Lampiran <span class="hint-mark">(Opsional)</span></label>
                                <div class="custom-upload-area">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <p class="upload-text">Klik atau Drag file kesini</p>
                                    <p class="upload-detail">Maksimal 2MB (JPG, PNG, PDF)</p>
                                    <input type="file" class="hidden-file-input" name="bukti" accept=".jpg,.jpeg,.png,.pdf" onchange="updateFileName(this)">
                                </div>
                                <div id="file-name-display" class="file-chosen-info" style="display:none;"></div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="premium-submit-btn">
                                    <i class="fas fa-paper-plane"></i>Kirim Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pengajuan -->
            <div class="history-column">
                <div class="premium-card">
                    <div class="card-header-premium">
                        <div class="header-title-wrapper-spread">
                            <div class="title-with-icon">
                                <div class="icon-circle-secondary">
                                    <i class="fas fa-history"></i>
                                </div>
                                <h5 class="card-title-premium">Riwayat Pengajuan</h5>
                            </div>
                            <span class="count-badge">{{ $pengajuans->count() }} Pengajuan</span>
                        </div>
                    </div>
                    <div class="card-body-history">
                        @if($pengajuans->isEmpty())
                            <div class="empty-state">
                                <div class="empty-icon-wrapper">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h6 class="empty-title">Belum ada riwayat</h6>
                                <p class="empty-subtitle">Anda belum pernah melakukan pengajuan lupa absensi atau kegiatan.</p>
                            </div>
                        @else
                            <div class="scrollable-table">
                                <table class="premium-table">
                                    <thead>
                                        <tr>
                                            <th class="col-info">Informasi Lupa</th>
                                            <th class="col-detail">Detail Isi</th>
                                            <th class="col-status">Status Approval</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pengajuans as $p)
                                            <tr class="table-row">
                                                <td class="cell-info">
                                                    <div class="info-content">
                                                        <div class="type-icon-wrapper type-{{ $p->jenis }}">
                                                            <i class="fas fa-{{ $p->jenis == 'absensi' ? 'clock' : 'clipboard-list' }}"></i>
                                                        </div>
                                                        <div class="date-wrapper">
                                                            <div class="main-date">{{ \Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y') }}</div>
                                                            <span class="type-tag tag-{{ $p->jenis }}">
                                                                {{ ucfirst($p->jenis) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="cell-detail">
                                                    <div class="diajukan-time">
                                                        <i class="far fa-calendar-alt"></i>
                                                        Diajukan: {{ $p->created_at->format('d/m/Y H:i') }}
                                                    </div>
                                                    @if($p->jenis == 'absensi')
                                                        <div class="time-block-wrapper">
                                                            <div class="time-item border-right">
                                                                <div class="time-label">MASUK</div>
                                                                <div class="time-value">{{ $p->jam_masuk ? substr($p->jam_masuk, 0, 5) : '--:--' }}</div>
                                                            </div>
                                                            <div class="time-item">
                                                                <div class="time-label">PULANG</div>
                                                                <div class="time-value">{{ $p->jam_pulang ? substr($p->jam_pulang, 0, 5) : '--:--' }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-description" title="{{ $p->deskripsi }}">
                                                            "{{ $p->deskripsi }}"
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="cell-status">
                                                    @if($p->status == 'pending')
                                                        <div class="status-pill state-pending">
                                                            <i class="fas fa-hourglass-half"></i>Pending
                                                        </div>
                                                    @elseif($p->status == 'valid')
                                                        <div class="status-pill state-valid">
                                                            <i class="fas fa-check-circle"></i>Valid
                                                        </div>
                                                    @else
                                                        <div class="status-pill state-rejected" title="{{ $p->catatan_pembimbing }}">
                                                            <i class="fas fa-times-circle"></i>Ditolak
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.style.display = 'block';
            display.innerHTML = '<i class="fas fa-file-alt me-1"></i> Terpilih: ' + input.files[0].name;
            input.parentElement.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            input.parentElement.classList.remove('bg-light', 'border-light');
            
            // Ubah icon
            const icon = input.parentElement.querySelector('i');
            icon.classList.remove('fa-cloud-upload-alt', 'text-muted');
            icon.classList.add('fa-check-circle', 'text-primary');
        } else {
            display.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis_pengajuan');
        const fieldsAbsensi = document.getElementById('fields_absensi');
        const fieldsKegiatan = document.getElementById('fields_kegiatan');
        
        // Remove 'required' from non-visible fields initially based on select value
        function toggleRequired() {
            if (jenisSelect.value === 'kegiatan') {
                document.querySelector('textarea[name="deskripsi"]').setAttribute('required', 'required');
            } else {
                const desc = document.querySelector('textarea[name="deskripsi"]');
                if(desc) desc.removeAttribute('required');
            }
        }
        
        toggleRequired();

        jenisSelect.addEventListener('change', function() {
            if (this.value === 'absensi') {
                // Show/hide with tiny animation via css
                fieldsAbsensi.style.opacity = '0';
                fieldsAbsensi.style.display = 'block';
                setTimeout(() => fieldsAbsensi.style.opacity = '1', 10);
                
                fieldsKegiatan.style.display = 'none';
                
            } else if (this.value === 'kegiatan') {
                fieldsKegiatan.style.opacity = '0';
                fieldsKegiatan.style.display = 'block';
                setTimeout(() => fieldsKegiatan.style.opacity = '1', 10);
                
                fieldsAbsensi.style.display = 'none';
            } else {
                fieldsAbsensi.style.display = 'none';
                fieldsKegiatan.style.display = 'none';
            }
            toggleRequired();
        });
    });
</script>
@endpush
@endsection