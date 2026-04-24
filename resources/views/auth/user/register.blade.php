<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - HumbleKos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/register.css', 'resources/js/app.js'])
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

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

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

                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Daftar Sebagai</label>
                        <div class="input-wrapper">
                            <span class="input-icon"><i class="fas fa-user-tag"></i></span>
                            <select id="role" name="role" required class="form-control">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih tipe akun</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
                                <option value="pemilik_kos" {{ old('role') == 'pemilik_kos' ? 'selected' : '' }}>Pemilik Kos</option>
                            </select>
                            <span class="input-icon" style="right: 10px; left: auto;"><i class="fas fa-chevron-down"></i></span>
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
