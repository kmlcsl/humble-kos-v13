<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.user.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'no_telepon' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'role' => 'required|in:user,pemilik_kos',
            'terms' => 'required',
        ], [
            'nama_lengkap.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'no_telepon.regex' => 'Nomor HP hanya boleh berisi angka.',
            'role.required' => 'Pilih tipe akun Anda.',
            'role.in' => 'Tipe akun yang dipilih tidak valid.',
            'terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'role' => $request->role,
                'status_akun' => 'aktif',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akun Anda sudah terdaftar, login segera.',
                    'redirect' => route('login'),
                ]);
            }
            return redirect()->route('login')
                ->with('success', 'Akun Anda sudah terdaftar, login segera.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')
                ->withInput();
        }
    }
}
