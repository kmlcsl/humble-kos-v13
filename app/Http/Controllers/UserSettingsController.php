<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSettingsController extends Controller
{
    public function index()
    {
        return view('users.pengaturan.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}
