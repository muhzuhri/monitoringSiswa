<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/navbar/navbar.css') }}">

    @stack('styles')
</head>

<body class="@yield('body-class', 'bg-light')">
    <div class="top-info-bar d-none d-md-block">
        <div class="container">
            <div><i class="fas fa-clock"></i> 8:00AM - 4:00PM | Senin - Jum'at</div>
            <div><i class="fas fa-envelope"></i> humas@ilkom.unsri.ac.id</div>
        </div>
    </div>
    <nav class="custom-navbar">
        
        <div class="container">
            <a class="navbar-brand" href="{{ route('pimpinan.home') }}">
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
                        <a class="nav-link {{ Route::is('pimpinan.home') ? 'active' : '' }}" href="{{ route('pimpinan.home') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pimpinan.siswa') || Route::is('pimpinan.guru') || Route::is('pimpinan.pembimbing') || Route::is('pimpinan.admin') ? 'active' : '' }}" href="{{ route('pimpinan.admin') }}">Kelola Akun</a>
                    </li>                    
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pimpinan.rekap') ? 'active' : '' }}" href="{{ route('pimpinan.rekap') }}">Rekap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pimpinan.profil') ? 'active' : '' }}" href="{{ route('pimpinan.profil') }}">Profil</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <div class="user-profile-nav d-none d-lg-flex">
                        <div class="user-info text-end">
                            <div class="user-name text-white">{{ $user->nama }}</div>
                            <div class="user-role text-white-50 small">{{ $user->jabatan ?? 'Pimpinan' }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn-logout">LOGOUT</button>
                    </form>
                </div>

            </div>
        </div>
    </nav>

    @yield('body')

    {{-- JS utama --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
