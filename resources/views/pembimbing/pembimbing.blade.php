@extends('layouts.nav.pembimbing')

@section('title', 'Dashboard Dosen - Monitoring Siswa')
@section('body-class', 'dosen-dashboard-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/style-dosen.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/pembimbing/home-pembimbing.js') }}"></script>
@endpush

@section('body')

    <div class="body-style">

        <div class="container">
            <!-- Hero Greeting -->
            <div class="welcome-banner">
                <div class="welcome-text d-flex align-items-center gap-4">
                    <div class="welcome-avatar">
                        <img src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=f6c23e&color=fff' }}" alt="Profile" class="rounded-circle" width="80" height="80" style="object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    </div>
                    <div>
                        <h1 class="welcome-title">Halo, {{ $user->nama }}! 👋</h1>
                        <p class="welcome-subtitle">Selamat datang kembali di Website Magang Fasilkom Unsri.</p>
                    </div>
                </div>
                <div class="welcome-status">
                    <span class="status-indicator-light">
                        <i class="fas fa-circle"></i> Sesi Aktif
                    </span>
                </div>
            </div>
            <div class="home-premium-section">
                
                <!-- Fasilkom Banner -->
                <div class="fasilkom-banner cursor-pointer" onclick="openSejarah()">
                    <div class="banner-icon-wrapper">
                        <img src="{{ asset('images/unsri-pride.png') }}" alt="">
                    </div>
                    <div class="banner-content">
                        <h2 class="banner-title">Fakultas Ilmu Komputer</h2>
                        <p class="banner-description">
                            Universitas yang berkomitmen mencetak lulusan kompeten di bidang teknologi informasi dan ilmu komputer. 
                            Sistem ini membantu monitoring kegiatan magang mahasiswa secara terpadu.
                        </p>
                        <div class="banner-badge">
                            <i class="fas fa-check-circle"></i>
                            Sistem Monitoring Magang Aktif
                        </div>
                    </div>
                    <div class="banner-more"><i class="fas fa-ellipsis-h"></i></div>
                </div>

                <!-- Tentang Fasilkom -->
                <span class="section-title-sm">Tentang Fasilkom</span>
                <div class="info-grid">
                    <div class="info-card" onclick="openVisiMisi()">
                        <div class="info-icon-box"><i class="fas fa-layer-group"></i></div>
                        <h3 class="info-card-title">Visi & Misi</h3>
                        <p class="info-card-text">
                            {{ Str::limit($informasi->visi, 100) }}
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-icon-box"><i class="far fa-clock"></i></div>
                        <h3 class="info-card-title">Jam Operasional</h3>
                        <p class="info-card-text">
                            {!! nl2br(e($informasi->jam_operasional)) !!}<br>
                            {{ $informasi->deskripsi_jam_operasional }}
                        </p>
                    </div>

                    <a href="{{ $informasi->link_maps }}" target="_blank" class="info-card-link">
                        <div class="info-card">
                            <div class="info-icon-box"><i class="fas fa-map-marker-alt"></i></div>
                            <h3 class="info-card-title">Lokasi</h3>
                            <p class="info-card-text">
                                {{ $informasi->alamat_lokasi }}
                            </p>
                        </div>
                    </a>

                    <div class="info-card">
                        <div class="info-icon-box"><i class="fas fa-phone-alt"></i></div>
                        <h3 class="info-card-title">Kontak</h3>
                        <p class="info-card-text">
                            Email: {{ $informasi->email_kontak }}<br>
                            Telp: {{ $informasi->telp_kontak }}<br>
                            Website: {{ $informasi->website_kontak }}
                        </p>
                    </div>
                </div>

                <span class="section-title-sm">Program Studi</span>
                <div class="prodi-list-container">
                    @forelse($programStudis as $prodi)
                        <div class="prodi-item-simple">
                            <div class="prodi-dot" style="background-color: {{ $prodi->warna_dot }}"></div>
                            <span class="prodi-name">{{ $prodi->nama }}</span>
                            <span class="prodi-badge" style="color: {{ $prodi->warna_dot }}; background-color: {{ $prodi->warna_dot }}15; border-color: {{ $prodi->warna_dot }}30">
                                {{ $prodi->jenjang }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small">Belum ada data program studi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Visi Misi Modal -->
    <div id="visiMisiModal" class="modal-overlay" onclick="closeModal('visiMisiModal')">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-close" onclick="closeModal('visiMisiModal')">&times;</div>
            <h2 class="modal-title">Visi & Misi {{ $informasi->nama_fakultas }}</h2>
            <div class="modal-body">
                <h4>Visi</h4>
                <p>{{ $informasi->visi }}</p>

                <h4>Misi</h4>
                <ul>
                    @foreach($informasi->misi_array as $misi)
                        <li>{{ $misi }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Sejarah Modal -->
    <div id="sejarahModal" class="modal-overlay" onclick="closeModal('sejarahModal')">
        <div class="modal-container" onclick="event.stopPropagation()">
            <div class="modal-close" onclick="closeModal('sejarahModal')">&times;</div>
            <h2 class="modal-title">Sejarah {{ $informasi->nama_fakultas }}</h2>
            <div class="modal-body">
                <p>
                    {!! nl2br(e($informasi->sejarah)) !!}
                </p>
            </div>
        </div>
    </div>
@endsection