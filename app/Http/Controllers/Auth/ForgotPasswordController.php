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

        // We will send the password reset link to this user. Once it has been sent
        // we will examine the response then see the message we need to show to the user.
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $status)
                    : $this->sendResetLinkFailedResponse($request, $status);
    }

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
