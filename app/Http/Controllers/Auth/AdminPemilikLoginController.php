<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminPemilikLoginController extends Controller
{
    public function showLoginForm()
    {
        // Redirect jika user sudah login
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'pemilik_kos') {
                return redirect()->route('pemilik.dashboard');
            }
            // Jika role user biasa, redirect ke user dashboard
            return redirect()->route('users.dashboard');
        }

        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginType = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Cari user dengan role admin atau pemilik_kos
        $user = User::where($loginType, $credentials['username'])
            ->whereIn('role', ['admin', 'pemilik_kos'])
            ->first();

        // Cek apakah user ditemukan
        if (!$user) {
            return back()->withErrors([
                'username' => 'Username/Email atau password salah.',
            ])->onlyInput('username');
        }

        // Verifikasi password
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Username/Email atau password salah.',
            ])->onlyInput('username');
        }

        // Cek status akun
        if ($user->status_akun !== 'aktif') {
            return back()->withErrors([
                'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->onlyInput('username');
        }

        // Login berdasarkan role
        if ($user->role === 'admin') {
            Auth::guard('admin')->login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'pemilik_kos') {
            Auth::guard('web')->login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->route('pemilik.dashboard');
        }

        // Jika role tidak sesuai
        return back()->withErrors([
            'username' => 'Akses ditolak. Role tidak valid.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // Logout dari guard yang sedang aktif
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been logged out successfully.');      
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
}
