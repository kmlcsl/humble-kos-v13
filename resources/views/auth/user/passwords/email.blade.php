<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password - HumbleKos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background-color: #eef5e4; min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <div style="width: 100%; max-width: 420px; margin: 20px; background: white; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); overflow: hidden;">
        <div style="display: flex; flex-direction: column;">

            <!-- Hero Section -->
            <div style="background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); padding: 30px 25px; color: white; text-align: center;">
                <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px 0;">Reset Password</h2>
                <p style="margin: 0; opacity: 0.9; font-size: 13px;">Kami akan mengirimkan link untuk meriset password anda</p>
            </div>

            <!-- Form Section -->
            <div style="padding: 35px 35px;">
                <div style="text-align: center; margin-bottom: 25px;">
                    <p style="color: #666; margin: 0; font-size: 14px;">Masukkan alamat email yang terdaftar pada akun anda</p>
                </div>

                @if (session('status'))
                    <div style="background: #dcfce7; border-left: 4px solid #22c55e; color: #166534; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; display: flex; align-items: center; font-size: 13px;">
                        <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->has('email'))
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; display: flex; align-items: center; font-size: 13px;">
                        <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Field -->
                    <div style="margin-bottom: 25px;">
                        <label for="email" style="display: block; font-weight: 500; color: #333; margin-bottom: 6px; font-size: 13px;">Alamat Email</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px;">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="nama@email.com"
                                style="width: 100%; padding: 11px 11px 11px 36px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: all 0.3s;"
                                onfocus="this.style.borderColor='#4f6f52'; this.style.boxShadow='0 0 0 3px rgba(79,111,82,0.1)';"
                                onblur="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none';"
                            >
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        style="width: 100%; background: linear-gradient(135deg, #4f6f52 0%, #739072 100%); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 12px rgba(79,111,82,0.25);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(79,111,82,0.35)';"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(79,111,82,0.25)';"
                    >
                        <i class="fas fa-paper-plane" style="margin-right: 6px;"></i>Kirim Link Reset
                    </button>

                    <!-- Back to Login -->
                    <div style="text-align: center; margin-top: 25px;">
                        <a href="{{ route('login') }}" style="color: #4f6f52; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fas fa-arrow-left"></i>Kembali ke Login
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
</body>
</html>
