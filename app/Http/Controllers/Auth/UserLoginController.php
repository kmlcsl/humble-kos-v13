<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.user.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput($request->only('login'));
        }

        try {
            $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

            $user = \App\Models\User::where($loginType, $request->login)
                ->whereIn('role', ['user', 'pemilik_kos'])
                ->first();

            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Username/Email atau Password salah.',
                        'errors' => ['login' => ['Akun tidak ditemukan atau tidak memiliki akses di sini.']]
                    ], 401);
                }
                return back()->withErrors([
                    'login' => 'Akun tidak ditemukan atau tidak memiliki akses di sini.',
                ])->withInput($request->only('login'));
            }

            // Hash comparison
            if (Hash::check($request->password, $user->password)) {
                Auth::guard('web')->login($user, $request->filled('remember'));
                $request->session()->regenerate();

                $redirectUrl = '';
                if ($user->role === 'pemilik_kos') {
                    $redirectUrl = route('pemilik.dashboard');
                } else {
                    $redirectUrl = route('users.dashboard');
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil!',
                        'redirect' => $redirectUrl,
                    ]);
                }
                return redirect()->intended($redirectUrl);
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Username/Email atau Password salah.',
                        'errors' => ['password' => ['Password yang Anda masukkan salah']]
                    ], 401);
                }
                return back()->withErrors([
                    'password' => 'Password yang Anda masukkan salah',
                ])->withInput($request->only('login'));
            }
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan server saat login. Silakan coba lagi.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors([
                'error' => 'Terjadi kesalahan server saat login. Silakan coba lagi.'
            ])->withInput($request->only('login'));
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function username()
    {
        return 'username';
    }
}
