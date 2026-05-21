<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    protected $redirectTo = '/login';

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.user.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ], [
            'token.required' => 'Token reset tidak valid.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput($request->only('email'));
        }

        // Proses reset password. Jika berhasil, update di database.
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // Jika reset sukses, redirect ke login. Jika gagal, kembali ke form.
        return $status === Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $status)
                    : $this->sendResetFailedResponse($request, $status);
    }

    /**
     * Reset the given user's password.
     *
     * @param  User  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $status)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => trans($status),
                'redirect' => $this->redirectTo
            ]);
        }

        return redirect($this->redirectTo)->with('status', trans($status));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $status)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => trans($status),
                'errors' => ['email' => [trans($status)]]
            ], 422);
        }

        return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($status)]);
    }
}
