<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());

        return view('users.profile.index', [
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

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

        $user = User::findOrFail(Auth::id());

        try {
            // Ensure directory exists
            if (!Storage::disk('public')->exists('profile-photos')) {
                Storage::disk('public')->makeDirectory('profile-photos');
            }

            // Delete old photo if it exists
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $file = $request->file('profile_photo');
            $filename = 'profile-' . $user->user_id . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store the new photo
            $path = $file->storeAs('profile-photos', $filename, 'public');
            
            if (!$path) {
                throw new \Exception('Gagal menyimpan file ke storage. Periksa permission folder storage di server.');
            }

            $user->foto_profil = $path;
            $user->save();

            // Auto link storage if not exists (helpful for server setup)
            if (!file_exists(public_path('storage'))) {
                try {
                    Artisan::call('storage:link');
                } catch (\Exception $e) {
                    // Silently fail if artisan link fails, user might need to do it manually
                }
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
        $user = User::findOrFail(Auth::id());

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
