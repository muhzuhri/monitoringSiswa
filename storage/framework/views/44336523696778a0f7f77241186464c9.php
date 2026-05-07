<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
            <a class="navbar-brand" href="<?php echo e(route('pembimbing.pembimbing')); ?>">
                <img src="<?php echo e(asset('images/unsri-pride.png')); ?>" alt="Logo" width="40" class="me-2">
                <div class="brand-text">
                    <div class="subtitle">FACULTY OF</div>
                    <div class="main-title">COMPUTER SCIENCE</div>
                </div>
            </a>

            <button class="navbar-toggler" type="button" id="navbarToggler">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse" id="navbarNav">
                <ul class="nav-list ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('pembimbing.pembimbing') ? 'active' : ''); ?>"
                            href="<?php echo e(route('pembimbing.pembimbing')); ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('pembimbing.siswa') ? 'active' : ''); ?>"
                            href="<?php echo e(route('pembimbing.siswa')); ?>">Daftar Siswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('pembimbing.pengajuan') ? 'active' : ''); ?>"
                            href="<?php echo e(route('pembimbing.pengajuan')); ?>">Persetujuan</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('pembimbing.evaluasi') ? 'active' : ''); ?>"
                            href="<?php echo e(route('pembimbing.evaluasi')); ?>">Penilaian Siswa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(Route::is('pembimbing.profil') ? 'active' : ''); ?>"
                            href="<?php echo e(route('pembimbing.profil')); ?>">Profil</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.getElementById('navbarToggler');
            const collapse = document.getElementById('navbarNav');

            if (toggler && collapse) {
                toggler.addEventListener('click', function() {
                    collapse.classList.toggle('show');
                });
            }
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html>
<?php /**PATH C:\laragon\www\monitoringSiswa\resources\views/layouts/nav/pembimbing.blade.php ENDPATH**/ ?>