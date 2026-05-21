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
     * Menyimpan user baru
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
        $user->nama_lengkap = $request->input('nama_lengkap');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = $request->input('role');
        $user->no_telepon = $request->input('no_telepon');
        $user->alamat = $request->input('alamat');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat');
    }

    /**
     * Menampilkan detail user
     */
    public function show(int $id)
    {
        $users = User::query()->where('user_id', $id)->firstOrFail();

        return view('admin.users.show', [
            'users' => $users,
        ]);
    }

    /**
     * Menampilkan form edit user
     */
    public function edit(int $id)
    {
        $users = User::query()->where('user_id', $id)->firstOrFail();

        return view('admin.users.update', [
            'users' => $users,
        ]);
    }

    /**
     * Menyimpan perubahan data user
     */
    public function update(Request $request, int $id)
    {
        // Tampilkan form edit jika request GET
        if ($request->isMethod('get')) {
            return $this->edit($id);
        }

        // Proses update jika request PUT
        $user = User::query()->where('user_id', $id)->firstOrFail();

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
     * Menghapus user
     */
    public function destroy(int $id)
    {
        $user = User::query()->where('user_id', $id)->firstOrFail();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus');
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request, int $id)
    {
        $user = User::query()->where('user_id', $id)->firstOrFail();

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
