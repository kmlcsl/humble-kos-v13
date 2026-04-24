<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('users.profile.index', [
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:1000',
        ];

        if (!$user->email && $request->filled('email')) {
            $rules['email'] = 'required|string|email|max:255|unique:users,email';
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;

        if (!$user->email && $request->filled('email')) {
            $user->email = $request->email;
        }
        
        $user->save();

        return redirect()->route('users.profile.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        try {
            // Delete old photo if it exists
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $file = $request->file('profile_photo');
            $filename = 'profile-' . $user->user_id . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store the new photo
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $user->foto_profil = $path;
            $user->save();

            if (!file_exists(public_path('storage'))) {
                \Artisan::call('storage:link');
            }

            return redirect()->route('users.profile.index')
                ->with('success', 'Foto profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('users.profile.index')
                ->with('error', 'Gagal mengunggah foto: ' . $e->getMessage());
        }
    }

    public function removeProfilePhoto()
    {
        $user = Auth::user();

        try {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = null;
            $user->save();

            return redirect()->route('users.profile.index')
                ->with('success', 'Foto profil berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('users.profile.index')
                ->with('error', 'Gagal menghapus foto: ' . $e->getMessage());
        }
    }
}
