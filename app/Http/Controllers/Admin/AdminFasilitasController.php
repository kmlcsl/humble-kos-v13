<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fasilitas = Fasilitas::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.fasilitas.index', [
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.fasilitas.create');
    }

    /**
     * Menyimpan fasilitas baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'icon_fasilitas' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        $fasilitas = new Fasilitas();
        $fasilitas->nama_fasilitas = $request->input('nama_fasilitas');
        $fasilitas->deskripsi = $request->input('deskripsi');

        // Upload icon jika ada
        if ($request->hasFile('icon_fasilitas')) {
            $path = $request->file('icon_fasilitas')->store('fasilitas/icons', 'public');
            $fasilitas->icon_fasilitas = $path;
        }

        $fasilitas->save();

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail fasilitas
     */
    public function show(string $id)
    {
        $fasilitas = Fasilitas::query()->findOrFail($id);

        return view('admin.fasilitas.show', [
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Menampilkan form edit fasilitas
     */
    public function edit(string $id)
    {
        $fasilitas = Fasilitas::query()->findOrFail($id);

        return view('admin.fasilitas.update', [
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Menyimpan perubahan data fasilitas
     */
    public function update(Request $request, string $id)
    {
        // Tampilkan form edit jika request GET
        if ($request->isMethod('get')) {
            return $this->edit($id);
        }

        // Validasi input
        $request->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'icon_fasilitas' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        $fasilitas = Fasilitas::query()->findOrFail($id);
        $fasilitas->nama_fasilitas = $request->input('nama_fasilitas');
        $fasilitas->deskripsi = $request->input('deskripsi');

        // Update icon jika ada
        if ($request->hasFile('icon_fasilitas')) {
            if ($fasilitas->icon_fasilitas && Storage::disk('public')->exists($fasilitas->icon_fasilitas)) {
                Storage::disk('public')->delete($fasilitas->icon_fasilitas);
            }

            $path = $request->file('icon_fasilitas')->store('fasilitas/icons', 'public');
            $fasilitas->icon_fasilitas = $path;
        }

        $fasilitas->save();

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $fasilitas = Fasilitas::findOrFail($id);

            // Delete icon if exists
            if ($fasilitas->icon_fasilitas && Storage::disk('public')->exists($fasilitas->icon_fasilitas)) {
                Storage::disk('public')->delete($fasilitas->icon_fasilitas);
            }

            $fasilitas->delete();

            return redirect()->route('admin.fasilitas.index')
                ->with('success', 'Fasilitas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus fasilitas: ' . $e->getMessage());
        }
    }
}
