<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>

    {{-- CSS utama --}}
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/siswa/home-siswa.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('assets/css/navbar/navbar.css') }}">

    @stack('styles')
</head>

<body class="@yield('body-class', 'bg-light')">

    <div class="top-info-bar d-none d-md-block">
        <div class="container">
            <div><i class="fas fa-clock"></i> 8:00AM - 4:00PM | Senin - Jum'at</div>
            <div><i class="fas fa-envelope"></i> humasss@ilkom.unsri.ac.id</div>
        </div>
    </div>
    <nav class="custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="{{ route('siswa.siswa') }}">
                <img src="{{ asset('images/unsri-pride.png') }}" alt="Logo" width="40" class="me-2">
                <div class="brand-text">
                    <div class="subtitle">FACULTY OF</div>
                    <div class="main-title">COMPUTER SCIENCE</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="nav-list ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.siswa') ? 'active' : '' }}"
                            href="{{ route('siswa.siswa') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.absensi') ? 'active' : '' }}"
                            href="{{ route('siswa.absensi') }}">Absensi & Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.pengajuan') ? 'active' : '' }}"
                            href="{{ route('siswa.pengajuan') }}">Lupa Isi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.laporan') ? 'active' : '' }}"
                            href="{{ route('siswa.laporan') }}">Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('siswa.profil') ? 'active' : '' }}"
                            href="{{ route('siswa.profil') }}">Profil</a>
                    </li>
                </ul>
                <form action="{{ route('logout') }}" method="post" class="ms-2">
                    @csrf
                    <button type="submit" class="btn-logout">LOGOUT</button>
                </form>
            </div>
        </div>
    </nav>
    @yield('body')

    {{-- JS utama --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')




</body>

</html>