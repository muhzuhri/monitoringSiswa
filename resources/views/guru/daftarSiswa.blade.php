@extends('layouts.nav.guru')

@section('title', 'Daftar Siswa Bimbingan - SIM Magang')
@section('body-class', 'dashboard-page guru-page')

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
                <form action="{{ route('guru.siswa') }}" method="GET" class="search-form">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" value="{{ $search }}" class="search-input"
                        placeholder="Cari Nama, NISN, atau Perusahaan...">
                </form>
            </div>
        </div>

        {{-- Tab Navigasi --}}
        <div class="tabs-wrapper mb-4">
            <div class="tabs-nav" role="tablist">
                <button class="tab-button active" id="bimbingan-tab" data-bs-toggle="pill" data-bs-target="#bimbingan"
                    type="button" role="tab">
                    <i class="fas fa-users"></i>
                    <span>Siswa Bimbingan ({{ $siswas->count() }})</span>
                </button>
                <button class="tab-button" id="search-tab" data-bs-toggle="pill" data-bs-target="#search-students"
                    type="button" role="tab">
                    <i class="fas fa-search-plus"></i>
                    <span>Cari Siswa ({{ $availableSiswas->count() }})</span>
                </button>
            </div>
        </div>

        <div class="tab-content" id="siswaTabContent">
            {{-- Tab Siswa Bimbingan --}}
            <div class="tab-pane fade show active" id="bimbingan" role="tabpanel">
                <div class="siswa-grid">
                    @forelse($groupedSiswas as $groupKey => $g)
                        <div class="student-card {{ $g['is_group'] ? 'group-card' : '' }}">
                            <div class="student-header">
                                <div class="student-avatar">
                                    @if($g['is_group'])
                                        <i class="fas fa-layer-group"></i>
                                    @else
                                        {{ strtoupper(substr($g['leader']->nama, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="student-meta">
                                    <h6 class="student-name">
                                        {{ $g['leader']->nama }}
                                        @if($g['is_group'])
                                            <span class="badge bg-info-light text-info-dark ms-1" style="font-size: 0.65rem; border-radius: 50px;">{{ $g['members']->count() }} Anggota</span>
                                        @endif
                                    </h6>
                                    <p class="student-nisn">NISN: {{ $g['leader']->nisn }}</p>
                                </div>
                                <div class="status-wrapper">
                                    @php
                                        $hadirAll = $g['members']->every(fn($m) => $m->absen_hari_ini);
                                        $hadirCount = $g['members']->filter(fn($m) => $m->absen_hari_ini)->count();
                                    @endphp
                                    @if ($hadirAll)
                                        <span class="status-badge status-hadir"><i class="fas fa-check-circle"></i> Hadir</span>
                                    @elseif($hadirCount > 0)
                                        <span class="status-badge status-warning"><i class="fas fa-user-clock"></i> {{ $hadirCount }}/{{ $g['members']->count() }}</span>
                                    @else
                                        <span class="status-badge status-absen"><i class="fas fa-times-circle"></i> Belum Absen</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <label class="info-label"><i class="fas fa-school me-1"></i> SEKOLAH</label>
                                    <span class="info-value">{{ $g['leader']->sekolah }}</span>
                                </div>
                                <div class="info-item">
                                    <label class="info-label"><i class="fas fa-building me-1"></i> PERUSAHAAN</label>
                                    <span class="info-value">{{ $g['leader']->perusahaan }}</span>
                                </div>
                            </div>
                            <div class="action-grid">
                                @if($g['is_group'])
                                    <button class="btn-action btn-detail-group btn-show-members" 
                                            data-name="{{ $g['leader']->nama }}"
                                            data-members="{{ $g['members']->toJson() }}">
                                        <i class="fas fa-search"></i> Pantau Kelompok
                                    </button>
                                @endif
                                <div class="action-row">
                                    <a href="{{ route('guru.logbook', $g['leader']->nisn) }}" class="btn-action btn-logbook">
                                        <i class="fas fa-book"></i> Logbook
                                    </a>
                                    <a href="{{ route('guru.absensi', $g['leader']->nisn) }}" class="btn-action btn-absensi">
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

            {{-- Tab Cari Siswa --}}
            <div class="tab-pane fade" id="search-students" role="tabpanel">
                <div class="filter-section shadow-sm">
                    <form action="{{ route('guru.siswa') }}" method="GET" class="row align-items-end g-3">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <div class="col-md-7">
                            <label class="filter-label"><i class="fas fa-filter me-2 text-primary"></i> Filter Berdasarkan NPSN Sekolah</label>
                            <div class="d-flex">
                                <div class="input-group-modern flex-grow-1">
                                    <span class="input-group-text-modern"><i class="fas fa-school"></i></span>
                                    <input type="text" name="npsn" value="{{ $npsn }}" class="form-control form-control-modern"
                                        placeholder="Masukkan NPSN Sekolah (Contoh: 10203040)...">
                                    <button class="btn btn-filter px-4" type="submit">Cari Siswa</button>
                                </div>
                                @if($npsn)
                                    <a href="{{ route('guru.siswa', ['search' => $search]) }}"
                                        class="btn btn-reset-modern">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="ui-card">
                    <div class="table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="ps-4">Identitas Siswa / Ketua</th>
                                    <th>Asal Sekolah</th>
                                    <th>Tipe Magang</th>
                                    <th>Kapasitas</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupedAvailable as $asKey => $ga)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="td-siswa-name">{{ $ga['leader']->nama }}</div>
                                            <div class="td-siswa-nisn"><i class="fas fa-id-card-alt me-1 opacity-50"></i> {{ $ga['leader']->nisn }}</div>
                                        </td>
                                        <td><span class="badge-school"><i class="fas fa-university me-1"></i> {{ $ga['leader']->sekolah }}</span></td>
                                        <td>
                                            @if($ga['is_group'])
                                                <span class="badge bg-info-light text-info-dark px-3 py-2" style="border-radius: 50px;">
                                                    <i class="fas fa-layer-group me-1"></i> Kelompok
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-light text-muted px-3 py-2" style="border-radius: 50px; background: #f1f5f9;">
                                                    <i class="fas fa-user me-1"></i> Individu
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ga['is_group'])
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-stack me-2">
                                                        @foreach($ga['members']->take(3) as $member)
                                                            <div class="avatar-mini" title="{{ $member->nama }}">{{ strtoupper(substr($member->nama,0,1)) }}</div>
                                                        @endforeach
                                                    </div>
                                                    <small class="fw-bold text-primary">{{ $ga['members']->count() }} Orang</small>
                                                </div>
                                            @else
                                                <small class="text-muted">1 Orang</small>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('guru.siswa.claim', $ga['leader']->nisn) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-small btn-accept">
                                                    <i class="fas fa-plus-circle"></i> Pilih Jadi Bimbingan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="empty-row text-center p-5">
                                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                                            <p>Tidak ada siswa tersedia untuk kriteria ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- End of page-wrapper --}}

    {{-- Modal Anggota Kelompok --}}
    <div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="fas fa-users-viewfinder me-3 text-primary"></i> <span id="modalGroupName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-4 py-3">
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Klik Logbook atau Absensi untuk melihat detail masing-masing siswa.</p>
                    </div>
                    <div class="modal-member-grid" id="modalGroupBody">
                        {{-- Cards via JS --}}
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pe-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
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
            const modalElement = document.getElementById('groupMembersModal');
            const modal = new bootstrap.Modal(modalElement);
            const modalName = document.getElementById('modalGroupName');
            const modalBody = document.getElementById('modalGroupBody');

            document.querySelectorAll('.btn-show-members').forEach(button => {
                button.addEventListener('click', function() {
                    const name = this.getAttribute('data-name');
                    const members = JSON.parse(this.getAttribute('data-members'));
                    
                    modalName.innerText = name;
                    modalBody.innerHTML = '';
                    
                    // Route patterns
                    const logbookRouteBase = "{{ route('guru.logbook', ['nisn' => ':nisn']) }}";
                    const absensiRouteBase = "{{ route('guru.absensi', ['nisn' => ':nisn']) }}";

                members.forEach((member, index) => {
                    const statusClass = member.absen_hari_ini ? 'status-hadir' : 'status-absen';
                    const statusText = member.absen_hari_ini 
                        ? '<i class="fas fa-check-circle"></i> Hadir' 
                        : '<i class="fas fa-times-circle"></i> Belum Absen';
                    
                    const logbookUrl = logbookRouteBase.replace(':nisn', member.nisn);
                    const absensiUrl = absensiRouteBase.replace(':nisn', member.nisn);
                    const initial = member.nama.charAt(0).toUpperCase();

                    const card = `
                        <div class="member-mini-card" style="animation: fadeInUp 0.4s ease forwards; animation-delay: ${index * 0.1}s; opacity: 0;">
                            <div class="member-card-header">
                                <div class="member-main-info">
                                    <div class="member-mini-avatar">${initial}</div>
                                    <div class="member-info">
                                        <div class="member-name">${member.nama}</div>
                                        <div class="member-nisn">NISN: ${member.nisn}</div>
                                    </div>
                                </div>
                                <div class="status-badge ${statusClass}">${statusText}</div>
                            </div>
                            <div class="member-card-actions">
                                <a href="${logbookUrl}" class="btn-action btn-logbook">
                                    <i class="fas fa-book"></i> Logbook
                                </a>
                                <a href="${absensiUrl}" class="btn-action btn-absensi">
                                    <i class="fas fa-calendar-check"></i> Absensi
                                </a>
                            </div>
                        </div>
                    `;
                    modalBody.innerHTML += card;
                });
                    
                    modal.show();
                });
            });
        });
    </script>
@endpush
@endsection