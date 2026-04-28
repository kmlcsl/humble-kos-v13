<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - HumbleKos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --admin-primary: #1e3a8a;
            --admin-secondary: #3b82f6;
            --admin-text: #0f172a;
        }

        body.admin-login-body {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            font-family: "Montserrat", sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            margin: 0;
            box-sizing: border-box;
        }

        body.admin-login-body::before, body.admin-login-body::after {
            content: "";
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        body.admin-login-body::before { top: -100px; left: -100px; width: 300px; height: 300px; }
        body.admin-login-body::after { bottom: -150px; right: -150px; width: 500px; height: 500px; }

        .admin-login-container {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 50px 40px;
            position: relative;
            z-index: 10;
        }

        .admin-login-header { text-align: center; margin-bottom: 40px; }
        .admin-login-header h1 { font-size: 26px; font-weight: 700; color: var(--admin-text); margin-bottom: 10px; }
        .admin-login-header p { font-size: 15px; color: #64748b; }

        .admin-badge {
            display: inline-block;
            background: var(--admin-primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 5px;
        }

        .admin-login-container .form-group { margin-bottom: 25px; position: relative; }
        .admin-login-container .form-group label { display: block; font-size: 14px; font-weight: 600; color: var(--admin-text); margin-bottom: 8px; }
        .admin-login-container .form-control {
            width: 100%; padding: 14px 45px 14px 15px; border: 2px solid #e2e8f0; border-radius: 8px; background-color: #f8fafc; transition: all 0.3s ease;
        }
        .admin-login-container .form-control:focus { border-color: var(--admin-secondary); background-color: white; outline: none; }
        .admin-login-container .input-icon { position: absolute; top: 43px; right: 15px; color: #94a3b8; }

        .btn-admin-login {
            width: 100%; padding: 14px; background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3); margin-top: 15px;
        }

        .btn-admin-login:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4); }
        .security-notice { text-align: center; margin-top: 30px; color: #64748b; font-size: 13px; }
    </style>
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
