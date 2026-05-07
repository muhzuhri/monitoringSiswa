<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/navbar/navbar.css')); ?>">


    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="<?php echo $__env->yieldContent('body-class', 'bg-light'); ?>">
    <div class="top-info-bar d-none d-md-block">
        <div class="container">
            <div><i class="fas fa-clock"></i> 8:00AM - 4:00PM | Senin - Jum'at</div>
            <div><i class="fas fa-envelope"></i> humas@ilkom.unsri.ac.id</div>
        </div>
    </div>
    <nav class="custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('admin.admin')); ?>">
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
                        <a class="nav-link <?php echo e(Route::is('admin.admin') ? 'active' : ''); ?>" href="<?php echo e(route('admin.admin')); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('admin.kelolaSiswa') || Route::is('admin.kelolaGuru') || Route::is('admin.kelolaPembimbing') ? 'active' : ''); ?>" href="<?php echo e(route('admin.kelolaSiswa')); ?>">Kelola Akun</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('admin.rekap') ? 'active' : ''); ?>" href="<?php echo e(route('admin.rekap')); ?>">Rekap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('admin.masterData') ? 'active' : ''); ?>" href="<?php echo e(route('admin.masterData')); ?>">Data Master</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('admin.profil') ? 'active' : ''); ?>" href="<?php echo e(route('admin.profil')); ?>">Profil</a>
                    </li>
                </ul>
                <form action="<?php echo e(route('logout')); ?>" method="post" class="btn-logout-pimpinan">
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

</html><?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/layouts/nav/admin.blade.php ENDPATH**/ ?>