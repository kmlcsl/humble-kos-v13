<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur Ulang Password - HumbleKos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background-color: #eef5e4; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div style="width: 100%; max-width: 420px; margin: 20px; background: white; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); overflow: hidden;">
        <div style="display: flex; flex-direction: column;">

            <!-- Hero Section -->
            <div style="background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); padding: 30px 25px; color: white; text-align: center;">
                <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px 0;">Password Baru</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Silakan masukkan password baru anda</p>
            </div>

            <!-- Form Section -->
            <div style="padding: 35px 35px;">
                @if ($errors->any())
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; font-size: 13px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Field (ReadOnly/Hidden if possible, but Laravel needs it) -->
                    <div style="margin-bottom: 18px;">
                        <label for="email" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Alamat Email</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ $email ?? old('email') }}"
                                required
                                readonly
                                style="width: 100%; padding: 11px 11px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; background-color: #f8f9fa; color: #6c757d;"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div style="margin-bottom: 18px;">
                        <label for="password" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Password Baru</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Minimal 8 karakter"
                                style="width: 100%; padding: 11px 40px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;"
                                onfocus="this.style.borderColor='#4f6f52'; this.style.boxShadow='0 0 0 3px rgba(79,111,82,0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            >
                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password"
                                style="position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; padding: 4px; font-size: 14px;"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div style="margin-bottom: 25px;">
                        <label for="password-confirm" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Konfirmasi Password Baru</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <input
                                type="password"
                                id="password-confirm"
                                name="password_confirmation"
                                required
                                placeholder="Ulangi password baru"
                                style="width: 100%; padding: 11px 40px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;"
                                onfocus="this.style.borderColor='#4f6f52'; this.style.boxShadow='0 0 0 3px rgba(79,111,82,0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            >
                            <button
                                type="button"
                                class="toggle-password"
                                data-target="password-confirm"
                                style="position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; padding: 4px; font-size: 14px;"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        style="width: 100%; background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(79,111,82,0.25);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(79,111,82,0.35)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(79,111,82,0.25)';"
                    >
                        <i class="fas fa-key" style="margin-right: 6px;"></i>Simpan Password
                    </button>
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
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
