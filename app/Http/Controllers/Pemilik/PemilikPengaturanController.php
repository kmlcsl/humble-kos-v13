<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PemilikPengaturanController extends Controller
{
    /**
     * Display profile settings page
     */
    public function profil()
    {
        $user = Auth::user();

        return view('pemilik.pengaturan.profil', [
            'user' => $user
        ]);
    }

    /**
     * Update profile information
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Display security settings page
     */
    public function keamanan()
    {
        $user = Auth::user();

        return view('pemilik.pengaturan.keamanan', [
            'user' => $user
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'password_lama' => 'required',
            'password_baru' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'password_lama.required' => 'Password lama harus diisi.',
            'password_baru.required' => 'Password baru harus diisi.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_baru.min' => 'Password minimal 8 karakter.',
        ]);

        // Verify current password
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password_baru'])
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update profile photo
     */
    /**
     * Update profile photo
     */
    public function updateFotoProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        try {
            // Delete old photo if it exists
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $file = $request->file('foto_profil');
            $filename = 'profile-' . $user->user_id . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Store the new photo
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $user->foto_profil = $path;
            $user->save();

            return back()->with('success', 'Foto profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunggah foto: ' . $e->getMessage());
        }   
    }

    /**
     * Remove profile photo
     */
    public function removeFotoProfil()
    {
        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $user->update(['foto_profil' => null]);

        return back()->with('success', 'Foto profil berhasil dihapus.');
    }
}
