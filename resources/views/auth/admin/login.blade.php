<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - HumbleKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-login-body">
    <div class="admin-login-container">
        <div class="admin-login-header">
            <h1>HumbleKos <span class="admin-badge">ADMIN</span></h1>
            <p>Masuk ke dashboard admin / pemilik kos</p>
        </div>

        @if ($errors->has('username'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first('username') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label for="username">Username atau Email</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                    name="username" value="{{ old('username') }}" required placeholder="Masukkan username">
                <span class="input-icon"><i class="fas fa-user-shield"></i></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" required placeholder="Masukkan password">
                <span class="input-icon toggle-password" data-target="password"><i class="fas fa-eye"></i></span>
            </div>
            <div class="forgot-password">
                <a href="#">Lupa Password?</a>
            </div>
            <button type="submit" class="btn-admin-login">
                <i class="fas fa-lock me-2"></i> Secure Login
            </button>
        </form>

        <div class="security-notice">
            <i class="fas fa-shield-alt"></i>
            <p>Panel ini untuk administrator dan pemilik kos HumbleKos. Akses tidak sah dapat dikenakan sanksi hukum.
            </p>
        </div>

        <hr>
        
        <!-- Back to Home -->
        <div style="text-align: center;">
            <a href="{{ url('/') }}"
                style="color: #999; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                <i class="fas fa-arrow-left"></i>Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    </script>
</body>

</html>
