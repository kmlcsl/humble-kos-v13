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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'icon_fasilitas' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        $fasilitas = new Fasilitas();
        $fasilitas->nama_fasilitas = $request->nama_fasilitas;
        $fasilitas->deskripsi = $request->deskripsi;

        // Upload icon if provided
        if ($request->hasFile('icon_fasilitas')) {
            $path = $request->file('icon_fasilitas')->store('fasilitas/icons', 'public');
            $fasilitas->icon_fasilitas = $path;
        }

        $fasilitas->save();

        return redirect()->route('admin.fasilitas.index')
            ->with('success', 'Fasilitas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        return view('admin.fasilitas.show', [
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Show the form for editing the specified resource (GET) or update it (PUT).
     */
    public function update(Request $request, string $id)
    {
        // If request GET, show the form
        if ($request->isMethod('get')) {
            $fasilitas = Fasilitas::findOrFail($id);

            return view('admin.fasilitas.update', [
                'fasilitas' => $fasilitas,
            ]);
        }

        // If request PUT, process update
        $request->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'icon_fasilitas' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:1024',
        ]);

        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->nama_fasilitas = $request->nama_fasilitas;
        $fasilitas->deskripsi = $request->deskripsi;

        // Upload new icon if provided
        if ($request->hasFile('icon_fasilitas')) {
            // Delete old icon if exists
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
