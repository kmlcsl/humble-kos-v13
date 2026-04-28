<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - HumbleKos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Embedded CSS from register.css */
        body.register-page {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eef5e4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 0;
        }

        #notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            width: 300px;
        }

        .register-container {
            width: 100%;
            max-width: 900px;
            margin: 15px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .register-hero {
            background: linear-gradient(135deg, #4f6f52 0%, #739072 100%);
            padding: 25px;
            color: white;
            text-align: center;
        }

        .register-hero h2 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .register-hero p {
            margin: 0;
            opacity: 0.9;
            font-size: 13px;
        }

        .register-form-container {
            padding: 30px 30px 25px 30px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin: 0 0 6px 0;
        }

        .form-header p {
            color: #666;
            margin: 0;
            font-size: 13px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            padding: 9px 10px 9px 34px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4f6f52;
            box-shadow: 0 0 0 3px rgba(79,111,82,0.1);
            outline: none;
        }

        .terms-group {
            margin-bottom: 18px;
            display: flex;
            align-items: start;
        }

        .terms-group input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin-right: 7px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        .terms-group label {
            font-size: 12px;
            color: #666;
            cursor: pointer;
            margin: 0;
            line-height: 1.4;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #4f6f52 0%, #739072 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(79,111,82,0.25);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79,111,82,0.35);
        }

        .divider {
            position: relative;
            margin: 18px 0;
            text-align: center;
        }

        .divider-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }

        .divider-text {
            position: relative;
            background: white;
            padding: 0 12px;
            color: #999;
            font-size: 13px;
        }

        .login-link, .home-link {
            text-align: center;
        }

        .login-link p, .home-link a {
            margin: 0;
            color: #666;
            font-size: 14px;
            text-decoration: none;
        }

        .login-link a {
            color: #4f6f52;
            font-weight: 600;
        }

        .home-link {
            margin-top: 14px;
        }

        .home-link a {
            font-size: 13px;
            color: #999;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Responsive Styles */
        @media (min-width: 768px) {
            .register-container {
                flex-direction: row;
            }
            .register-hero {
                display: flex;
                flex-direction: column;
                justify-content: center;
                width: 40%;
                background-image: url('{{ asset('images/hk-3.png') }}');
                background-size: cover;
                background-position: center;
                position: relative;
                padding: 40px;
                text-align: left;
            }
            .register-hero::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(79, 111, 82, 0.92) 0%, rgba(115, 144, 114, 0.92) 100%);
            }
            .register-hero > * {
                position: relative;
                z-index: 1;
            }
            .register-hero h2 {
                font-size: 26px;
                text-align: left;
            }
            .register-hero p {
                font-size: 14px;
                text-align: left;
            }
            .register-form-container {
                width: 60%;
            }
            .form-row-split {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .register-form-container {
                padding: 20px;
            }
            .form-header h1 {
                font-size: 20px;
            }
            .form-header p {
                font-size: 12px;
            }
            .form-control, .submit-btn {
                font-size: 14px;
            }
        }
        
        .alert {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body class="register-page">

    <div id="notification-container"></div>

    <div class="register-container">
        <div class="register-hero">
            <h2>Bergabunglah dengan HumbleKos</h2>
            <p>Daftar sekarang dan mulai cari kos impian Anda</p>
        </div>

        <div class="register-form-container">
            <div class="form-header">
                <h1>Daftar Akun Baru</h1>
                <p>Buat akun untuk akses fitur lengkap</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" id="registerForm">
                @csrf
                
                {{-- Secara otomatis mendaftar sebagai user --}}
                <input type="hidden" name="role" value="user">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-user"></i></span>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Masukkan nama lengkap" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Masukkan alamat email" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-row form-row-split">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-at"></i></span>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Username Anda" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="no_telepon">No. HP <span style="color: #999; font-size: 11px;">(opsional)</span></label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-phone"></i></span>
                            <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="08xxxxxxxxxx" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-row form-row-split">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" required placeholder="Password" class="form-control">
                            <button type="button" class="toggle-password" data-target="password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; padding: 4px; font-size: 13px;"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Ulangi Password</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password" class="form-control">
                            <button type="button" class="toggle-password" data-target="password_confirmation" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; padding: 4px; font-size: 13px;"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>

                <div class="terms-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Saya menyetujui syarat dan ketentuan</label>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus" style="margin-right: 6px;"></i>Daftar
                </button>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">atau</span>
                </div>

                <div class="login-link">
                    <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk Sekarang</a></p>
                </div>

                <div class="home-link">
                    <a href="{{ url('/') }}"><i class="fas fa-arrow-left"></i>Kembali ke Beranda</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const togglePasswords = document.querySelectorAll('.toggle-password');

        togglePasswords.forEach(toggle => {
            toggle.addEventListener('click', () => {
                const targetId = toggle.getAttribute('data-target');
                const input = document.getElementById(targetId);

                if (input.type === 'password') {
                    input.type = 'text';
                    toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    toggle.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
    </script>
</body>
</html>
