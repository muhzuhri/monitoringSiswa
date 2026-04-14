<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Siswa Magang</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/auth/login.css') }}">
</head>

<body>

<div class="login-container">
    <div class="card login-card">
        <div class="card-header text-center login-header">
            <h4 class="login-title">LOGIN</h4>
            <p class="login-subtitle">Monitoring Siswa Magang</p>
            <span class="login-accent"></span>
        </div>        
        

        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="form-control"
                           required
                           value="{{ old('email') }}">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control"
                               required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" id="btnTogglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('register') }}" class="register-link">
                        Belum punya akun? Daftar
                    </a>
                </div>

                <button type="submit" class="btn btn-primary mt-3 w-100">
                    Login
                </button>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('btnTogglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>

</body>
</html>
