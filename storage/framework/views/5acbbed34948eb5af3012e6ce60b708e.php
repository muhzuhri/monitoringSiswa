<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/navbar/navbar.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="<?php echo $__env->yieldContent('body-class', 'bg-light'); ?>">

    <div class="top-info-bar d-none d-md-block">
        <div class="container">
            <div><i class="fas fa-clock"></i> 8:00AM - 4:00PM | Senin - Jum'at</div>
            <div><i class="fas fa-envelope"></i> humasss@ilkom.unsri.ac.id</div>
        </div>
    </div>
    <nav class="custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('siswa.siswa')); ?>">
                <img src="<?php echo e(asset('images/unsri-pride.png')); ?>" alt="Logo" width="40" class="me-2">
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
                        <a class="nav-link <?php echo e(request()->routeIs('siswa.siswa') ? 'active' : ''); ?>"
                            href="<?php echo e(route('siswa.siswa')); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('siswa.absensi') ? 'active' : ''); ?>"
                            href="<?php echo e(route('siswa.absensi')); ?>">Absensi & Kegiatan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('siswa.pengajuan') ? 'active' : ''); ?>"
                            href="<?php echo e(route('siswa.pengajuan')); ?>">Lupa Isi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('siswa.laporan') ? 'active' : ''); ?>"
                            href="<?php echo e(route('siswa.laporan')); ?>">Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('siswa.profil') ? 'active' : ''); ?>"
                            href="<?php echo e(route('siswa.profil')); ?>">Profil</a>
                    </li>
                </ul>
                <form action="<?php echo e(route('logout')); ?>" method="post" class="ms-2">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-logout">LOGOUT</button>
                </form>
            </div>
        </div>
    </nav>
    <?php echo $__env->yieldContent('body'); ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>




</body>

</html><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/layouts/nav/siswa.blade.php ENDPATH**/ ?>