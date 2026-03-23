@extends('layouts.nav.pembimbing')

@section('title', 'Daftar Siswa Bimbingan - SIM Magang')
@section('body-class', 'dashboard-page pembimbing-page')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/guru/daftarSiswa.css') }}">
@endpush

@section('body')
    <div class="page-wrapper">

        {{-- Header Halaman --}}
        <div class="page-header">
            <div class="header-text">
                <h4 class="page-title">Siswa Bimbingan</h4>
                <p class="page-subtitle">Kelola dan pantau seluruh siswa magang di bawah bimbingan Anda.</p>
            </div>
            <div class="search-wrapper">
                <form action="{{ route('pembimbing.siswa') }}" method="GET" class="search-form">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="search-input"
                        placeholder="Cari Nama, NISN, atau Perusahaan...">
                </form>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold text-dark"><i class="fas fa-users me-2 text-primary"></i> Total Siswa Bimbingan: {{ $siswas->count() }}</h5>
        </div>

        <div class="siswa-container">
            <div class="siswa-grid">
                @forelse($siswas as $siswa)
                    <div class="student-card">
                        <div class="student-header">
                            <div class="student-avatar">
                                @if($siswa->foto_profil)
                                    <img src="{{ asset('storage/' . $siswa->foto_profil) }}" alt="{{ $siswa->nama }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                @else
                                    {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                @endif
                            </div>
                            <div class="student-meta">
                                <h6 class="student-name">{{ $siswa->nama }}</h6>
                                <p class="student-nisn">NISN: {{ $siswa->nisn }}</p>
                            </div>
                            <div class="status-wrapper">
                                @if ($siswa->absen_hari_ini)
                                    <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                                @else
                                    <span class="status-badge status-absen"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                @endif
                            </div>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                <span class="info-value">{{ $siswa->sekolah }}</span>
                            </div>
                            <div class="info-item">
                                <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                <span class="info-value">{{ $siswa->perusahaan }}</span>
                            </div>
                        </div>
                        <div class="action-grid">
                            <button class="btn-action btn-detail-group btn-show-profile" 
                                    data-siswa="{{ json_encode($siswa) }}">
                                <i class="fas fa-user-circle"></i> Profil Siswa
                            </button>
                            <div class="action-row">
                                <a href="{{ route('pembimbing.logbook', $siswa->nisn) }}" class="btn-action btn-logbook">
                                    <i class="fas fa-book"></i> Logbook
                                </a>
                                <a href="{{ route('pembimbing.absensi', $siswa->nisn) }}" class="btn-action btn-absensi">
                                    <i class="fas fa-calendar-check"></i> Absensi
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada siswa bimbingan.</div>
                @endforelse
            </div>
        </div>

    </div> {{-- End of page-wrapper --}}

    {{-- Modal Profil Siswa --}}
    <div class="modal fade" id="studentProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="fas fa-user-id-badge me-2"></i> Profil Lengkap Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="text-center py-4 bg-light">
                        <div class="profile-modal-avatar mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center" 
                             style="width: 100px; height: 100px; border-radius: 50%; background: #fff; font-size: 2.5rem; color: #0d6efd; font-weight: 800; border: 4px solid #fff;">
                            <span id="modalInitial"></span>
                            <img id="modalPhoto" src="" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; display: none;">
                        </div>
                        <h5 class="fw-bold mb-1" id="modalName"></h5>
                        <p class="text-muted small mb-0" id="modalNisn"></p>
                    </div>
                    <div class="modal-info-list p-4">
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-envelope"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">EMAIL</label>
                                <span class="fw-semibold" id="modalEmail"></span>
                            </div>
                        </div>
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-phone"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">NO. HANDPHONE</label>
                                <span class="fw-semibold" id="modalPhone"></span>
                            </div>
                        </div>
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-school"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">SEKOLAH / INSTANSI</label>
                                <span class="fw-semibold" id="modalSchool"></span>
                            </div>
                        </div>
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-graduation-cap"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">KELAS / JURUSAN</label>
                                <span class="fw-semibold" id="modalClass"></span>
                            </div>
                        </div>
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-building"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">PERUSAHAAN</label>
                                <span class="fw-semibold" id="modalCompany"></span>
                            </div>
                        </div>
                        <div class="info-row mb-3 d-flex border-bottom pb-2">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-user-tie"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">GURU PEMBIMBING</label>
                                <span class="fw-semibold" id="modalGuru"></span>
                            </div>
                        </div>
                        <div class="info-row d-flex">
                            <div class="info-icon text-primary me-3" style="width: 24px;"><i class="fas fa-calendar-alt"></i></div>
                            <div class="info-content">
                                <label class="text-muted small fw-bold d-block">PERIODE MAGANG</label>
                                <span class="fw-semibold" id="modalPeriod"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fs-4"></i>
                        <div>
                            <div class="fw-bold">Berhasil!</div>
                            <div class="small opacity-75">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="toast-container position-fixed bottom-0 end-0 p-4">
            <div id="liveToast" class="toast show align-items-center text-white bg-warning border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
                <div class="d-flex p-2">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-3 fs-4 text-dark"></i>
                        <div class="text-dark">
                            <div class="fw-bold">Peringatan</div>
                            <div class="small opacity-75">{{ session('warning') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileModalElement = document.getElementById('studentProfileModal');
            const profileModal = new bootstrap.Modal(profileModalElement);
            
            document.querySelectorAll('.btn-show-profile').forEach(button => {
                button.addEventListener('click', function() {
                    const siswa = JSON.parse(this.getAttribute('data-siswa'));
                    
                    document.getElementById('modalName').innerText = siswa.nama;
                    document.getElementById('modalNisn').innerText = 'NISN: ' + siswa.nisn;
                    document.getElementById('modalEmail').innerText = siswa.email || '-';
                    document.getElementById('modalPhone').innerText = siswa.no_hp || '-';
                    document.getElementById('modalSchool').innerText = siswa.sekolah;
                    document.getElementById('modalClass').innerText = (siswa.kelas || '-') + ' / ' + (siswa.jurusan || '-');
                    document.getElementById('modalCompany').innerText = siswa.perusahaan;
                    document.getElementById('modalGuru').innerText = siswa.guru ? siswa.guru.nama : '-';
                    
                    // Format Period
                    if (siswa.tgl_mulai_magang && siswa.tgl_selesai_magang) {
                        const start = new Date(siswa.tgl_mulai_magang);
                        const end = new Date(siswa.tgl_selesai_magang);
                        const options = { day: 'numeric', month: 'short', year: 'numeric' };
                        document.getElementById('modalPeriod').innerText = start.toLocaleDateString('id-ID', options) + ' - ' + end.toLocaleDateString('id-ID', options);
                    } else {
                        document.getElementById('modalPeriod').innerText = '-';
                    }

                    // Handle Photo/Initial
                    const initialEl = document.getElementById('modalInitial');
                    const photoEl = document.getElementById('modalPhoto');
                    
                    if (siswa.foto_profil) {
                        photoEl.src = "{{ asset('storage') }}/" + siswa.foto_profil;
                        photoEl.style.display = 'block';
                        initialEl.style.display = 'none';
                    } else {
                        initialEl.innerText = siswa.nama.charAt(0).toUpperCase();
                        initialEl.style.display = 'block';
                        photoEl.style.display = 'none';
                    }
                    
                    profileModal.show();
                });
            });
        });
    </script>
@endpush
@endsection