<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PemilikKosMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::guard('web')->user();
        if (!$user || $user->role !== 'pemilik_kos') {
            return redirect()->route('users.dashboard')->withErrors([
                'message' => 'Akses ditolak. Anda harus memiliki akun Pemilik Kos untuk mengakses halaman ini.'
            ]);
        }

        if ($user->status_akun !== 'aktif') {
            Auth::guard('web')->logout();
            return redirect()->route('login')->withErrors([
                'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
            ]);
        }

        return $next($request);
    }
}
