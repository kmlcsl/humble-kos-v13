<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.user.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Mengirim tautan reset password
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $status)
                    : $this->sendResetLinkFailedResponse($request, $status);
    }

    /**
     * Respon setelah berhasil mengirim link reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $status)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => trans($status)
            ]);
        }

        return back()->with('status', trans($status));
    }

    /**
     * Respon setelah gagal mengirim link reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $status)
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
