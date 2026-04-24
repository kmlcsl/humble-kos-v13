<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Pengguna - HumbleKos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background-color: #eef5e4; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 1050; width: 300px;"></div>

    <div style="width: 100%; max-width: 420px; margin: 20px; background: white; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); overflow: hidden;">
        <div style="display: flex; flex-direction: column;">

            <!-- Hero Section -->
            <div style="background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); padding: 30px 25px; color: white; text-align: center;">
                <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px 0;">Selamat Datang di HumbleKos</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Temukan kenyamanan tempat tinggal dengan harga terjangkau</p>
            </div>

            <!-- Login Form -->
            <div style="padding: 35px 35px;">
                <div style="text-align: center; margin-bottom: 25px;">
                    <h1 style="font-size: 24px; font-weight: 700; color: #333; margin: 0 0 8px 0;">Login Pengguna</h1>
                    <p style="color: #666; margin: 0; font-size: 14px;">Masuk ke akun untuk akses fitur lengkap</p>
                </div>

                @if ($errors->has('login'))
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; display: flex; align-items: center; font-size: 13px;">
                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                        <span>{{ $errors->first('login') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Login Field -->
                    <div style="margin-bottom: 18px;">
                        <label for="login" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Username atau Email</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-user"></i>
                            </span>
                            <input
                                type="text"
                                id="login"
                                name="login"
                                value="{{ old('login') }}"
                                required
                                placeholder="Masukkan username atau email anda"
                                style="width: 100%; padding: 11px 11px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;"
                                onfocus="this.style.borderColor='#4f6f52'; this.style.boxShadow='0 0 0 3px rgba(79,111,82,0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div style="margin-bottom: 18px;">
                        <label for="password" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Password</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Masukkan password anda"
                                style="width: 100%; padding: 11px 40px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;"
                                onfocus="this.style.borderColor='#4f6f52'; this.style.boxShadow='0 0 0 3px rgba(79,111,82,0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            >
                            <button
                                type="button"
                                class="toggle-password"
                                style="position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; padding: 4px; font-size: 14px;"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center;">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                style="width: 16px; height: 16px; cursor: pointer; margin-right: 6px;"
                            >
                            <label for="remember" style="font-size: 13px; color: #666; cursor: pointer; margin: 0;">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" style="font-size: 13px; color: #4f6f52; text-decoration: none;">Lupa Password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        style="width: 100%; background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(79,111,82,0.25);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(79,111,82,0.35)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(79,111,82,0.25)';"
                    >
                        <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i>Masuk
                    </button>

                    <!-- Divider -->
                    <div style="position: relative; margin: 20px 0; text-align: center;">
                        <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #e0e0e0;"></div>
                        <span style="position: relative; background: white; padding: 0 12px; color: #999; font-size: 13px;">atau</span>
                    </div>

                    <!-- Register Link -->
                    <div style="text-align: center; margin-bottom: 16px;">
                        <p style="margin: 0; color: #666; font-size: 14px;">
                            Belum punya akun?
                            <a href="{{ route('register') }}" style="color: #4f6f52; font-weight: 600; text-decoration: none;">Daftar Sekarang</a>
                        </p>
                    </div>

                    <!-- Back to Home -->
                    <div style="text-align: center;">
                        <a href="{{ url('/') }}" style="color: #999; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-left"></i>Kembali ke Beranda
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <style>
        input:focus {
            outline: none;
        }
    </style>

    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        }
    </script>
</body>
</html>
