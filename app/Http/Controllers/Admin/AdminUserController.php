<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,pemilik_kos',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user = new User();
        $user->nama_lengkap = $request->nama_lengkap;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->no_telepon = $request->no_telepon;
        $user->alamat = $request->alamat;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $users = User::where('user_id', $id)->firstOrFail();

        return view('admin.users.show', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for editing (GET) or update (PUT) the specified user.
     */
    public function update(Request $request, $id)
    {
        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $users = User::where('user_id', $id)->firstOrFail();

            return view('admin.users.update', [
                'users' => $users,
            ]);
        }

        // Jika request PUT, proses update
        $user = User::where('user_id', $id)->firstOrFail();

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'email' => 'nullable|email|max:100|unique:users,email,' . $id . ',user_id',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $user->update($request->only(['nama_lengkap', 'username', 'email', 'no_telepon', 'alamat']));

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::where('user_id', $id)->firstOrFail();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();

        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return redirect()->back()->with('success', 'Password pengguna "' . $user->nama_lengkap . '" berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }
}
