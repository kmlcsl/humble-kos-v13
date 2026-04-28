<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class AdminPengaturanController extends Controller
{
    /**
     * Display general settings.
     */
    public function general()
    {
        return view('admin.pengaturan.general')->with('info', 'Fitur ini sedang dalam pengembangan.');
    }

    /**
     * Display appearance settings.
     */
    public function appearance()
    {
        return view('admin.pengaturan.appearance')->with('info', 'Fitur ini sedang dalam pengembangan.');
    }

    /**
     * Display email settings.
     */
    public function email()
    {
        return view('admin.pengaturan.email')->with('info', 'Fitur ini sedang dalam pengembangan.');
    }

    /**
     * Display admin users management.
     */
    public function admins()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pengaturan.admins', [
            'admins' => $admins,
        ]);
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        return view('admin.pengaturan.admins_create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->route('admin.pengaturan.admins')->with('success', 'Admin baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing (GET) or update (PUT) the specified admin.
     */
    public function update(Request $request, User $admin)
    {
        if ($admin->role !== 'admin') {
            return redirect()->route('admin.pengaturan.admins')->with('error', 'Pengguna ini bukan admin.');
        }

        // If request GET, show the form
        if ($request->isMethod('get')) {
            return view('admin.pengaturan.admins_update', [
                'admin' => $admin,
            ]);
        }

        // If request PUT, process update
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$admin->user_id.',user_id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return redirect()->route('admin.pengaturan.admins')->with('success', 'Data admin berhasil diperbarui.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin)
    {
        if ($admin->user_id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        if ($admin->role !== 'admin') {
            return back()->with('error', 'Pengguna ini bukan admin.');
        }

        $admin->delete();

        return redirect()->route('admin.pengaturan.admins')->with('success', 'Admin berhasil dihapus.');
    }
}
