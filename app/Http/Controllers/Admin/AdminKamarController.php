<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Kosan;
use App\Models\Fasilitas;
use App\Models\FotoProperti;
use App\Services\KamarAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminKamarController extends Controller
{
    protected $kamarService;

    public function __construct(KamarAdminService $kamarService)
    {
        $this->kamarService = $kamarService;
    }

    /**
     * Menampilkan daftar kamar
     */
    public function index(Request $request)
    {
        $filters = $request->except('_token');
        $kamars = $this->kamarService->getAllKamar($filters);

        // Get list of kosans for filter
        $kosans = Kosan::orderBy('nama_kosan')->get();

        $stats = $this->kamarService->getKamarStatistics();

        return view('admin.manajemen-kamar.index', [
            'kamars' => $kamars,
            'kosans' => $kosans,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan kamar berdasarkan ID kosan
     */
    public function byKosan($id)
    {
        $kosans = Kosan::findOrFail($id);
        $kamars = $this->kamarService->getAllKamar(['id_kosan' => $id]);
        $stats = $this->kamarService->getKamarStatistics($id);

        return view('admin.manajemen-kamar.index', [
            'kamars' => $kamars,
            'kosans' => $kosans,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan form untuk membuat kamar baru
     */
    public function create(Request $request)
    {
        $kosans = Kosan::orderBy('nama_kosan')->get();
        $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();
        $selectedKosan = null;

        // If kosan_id is provided, preselect the kosan
        if ($request->has('kosan_id')) {
            $selectedKosan = Kosan::find($request->kosan_id);
        }

        return view('admin.manajemen-kamar.create', [
            'kosans' => $kosans,
            'fasilitas' => $fasilitas,
            'selectedKosan' => $selectedKosan
        ]);
    }

    /**
     * Menyimpan kamar baru ke database
     */
    public function store(Request $request)
    {
        // Validasi berdasarkan struktur database yang valid
        $request->validate([
            'kosan_id' => 'required|exists:kosan,kosan_id',
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:single,double,shared',
            'harga_per_bulan' => 'required|numeric|min:0',
            'ukuran_kamar' => 'required|string|max:20',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
        ], [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max karakter.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus format: :values.',
            'exists' => ':attribute tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
        ], [
            'kosan_id' => 'Kosan',
            'nomor_kamar' => 'Nomor Kamar',
            'tipe_kamar' => 'Tipe Kamar',
            'harga_per_bulan' => 'Harga Per Bulan',
            'ukuran_kamar' => 'Ukuran Kamar',
            'kapasitas' => 'Kapasitas',
            'status_kamar' => 'Status Kamar',
            'foto_kamar' => 'Foto Kamar',
        ]);

        // Validasi foto tambahan secara terpisah
        if ($request->hasFile('foto_tambahan')) {
            $request->validate([
                'foto_tambahan' => 'array|max:3',
                'foto_tambahan.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'array' => ':attribute harus berupa array.',
                'max' => ':attribute maksimal :max item.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => ':attribute harus format: :values.',
            ], [
                'foto_tambahan' => 'Foto Tambahan',
                'foto_tambahan.*' => 'Foto Tambahan',
            ]);
        }

        DB::beginTransaction();

        try {
            // Check for duplicate room number in the same kosan
            $exists = Kamar::where('kosan_id', $request->kosan_id)
                ->where('nomor_kamar', $request->nomor_kamar)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'nomor_kamar' => 'Nomor kamar sudah ada di kos ini.'
                ]);
            }

            // Create kamar
            $kamar = new Kamar();
            $kamar->kosan_id = $request->kosan_id;
            $kamar->nomor_kamar = $request->nomor_kamar;
            $kamar->tipe_kamar = $request->tipe_kamar;
            $kamar->harga_per_bulan = $request->harga_per_bulan;
            $kamar->ukuran_kamar = $request->ukuran_kamar;
            $kamar->kapasitas = $request->kapasitas;
            $kamar->deskripsi = $request->deskripsi;
            $kamar->status_kamar = $request->status_kamar;

            // Upload foto utama
            if ($request->hasFile('foto_kamar')) {
                $path = $request->file('foto_kamar')->store('kamar', 'public');
                $kamar->foto_kamar = $path;
            }

            $kamar->save();

            // Main photo is handled above, not saved to foto_properti.

            // Upload foto tambahan (2-4)
            if ($request->hasFile('foto_tambahan')) {
                $urutan = 2;
                foreach ($request->file('foto_tambahan') as $foto) {
                    if ($urutan > 4) break;

                    $path = $foto->store('kamar', 'public');
                    FotoProperti::create([
                        'properti_type' => 'kamar',
                        'properti_id' => $kamar->kamar_id,
                        'path_foto' => $path,
                        'urutan' => $urutan,
                        'is_utama' => false,
                        'ukuran_file' => $foto->getSize(),
                    ]);

                    $urutan++;
                }
            }

            // Sync fasilitas kamar
            if ($request->has('fasilitas')) {
                $kamar->fasilitas()->sync($request->fasilitas);
            }

            DB::commit();

            return redirect()->route('admin.manajemen-kamar.index')
                ->with('success', 'Kamar berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail kamar
     */
    public function show($id)
    {
        $kamars = Kamar::with(['kosan', 'fasilitas', 'fotos', 'fotoTambahan'])->findOrFail($id);

        return view('admin.manajemen-kamar.show', [
            'kamars' => $kamars,
        ]);
    }

    /**
     * Menampilkan form edit (GET) atau menyimpan perubahan (PUT)
     */
    public function update(Request $request, $id)
    {
        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $kamars = Kamar::with(['fasilitas', 'fotos'])->findOrFail($id);
            $kosans = Kosan::orderBy('nama_kosan')->get();
            $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();

            return view('admin.manajemen-kamar.update', [
                'kamars' => $kamars,
                'kosans' => $kosans,
                'fasilitas' => $fasilitas,
            ]);
        }

        // Jika request PUT, proses update
        // Validasi berdasarkan struktur database yang valid
        $request->validate([
            'kosan_id' => 'required|exists:kosan,kosan_id',
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:single,double,shared',
            'harga_per_bulan' => 'required|numeric|min:0',
            'ukuran_kamar' => 'required|string|max:20',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
        ], [
            'required' => ':attribute wajib diisi.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max karakter.',
            'image' => ':attribute harus berupa gambar.',
            'mimes' => ':attribute harus format: :values.',
            'exists' => ':attribute tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
        ], [
            'kosan_id' => 'Kosan',
            'nomor_kamar' => 'Nomor Kamar',
            'tipe_kamar' => 'Tipe Kamar',
            'harga_per_bulan' => 'Harga Per Bulan',
            'ukuran_kamar' => 'Ukuran Kamar',
            'kapasitas' => 'Kapasitas',
            'status_kamar' => 'Status Kamar',
            'foto_kamar' => 'Foto Kamar',
        ]);

        // Validasi foto tambahan secara terpisah
        if ($request->hasFile('foto_tambahan')) {
            $request->validate([
                'foto_tambahan' => 'array|max:3',
                'foto_tambahan.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'array' => ':attribute harus berupa array.',
                'max' => ':attribute maksimal :max item.',
                'image' => ':attribute harus berupa gambar.',
                'mimes' => ':attribute harus format: :values.',
            ], [
                'foto_tambahan' => 'Foto Tambahan',
                'foto_tambahan.*' => 'Foto Tambahan',
            ]);
        }

        // Validasi hapus foto secara terpisah
        if ($request->has('hapus_foto')) {
            $request->validate([
                'hapus_foto' => 'array',
                'hapus_foto.*' => 'integer|exists:foto_properti,foto_id',
            ]);
        }

        DB::beginTransaction();

        try {
            $kamar = Kamar::findOrFail($id);

            // Check for duplicate room number in the same kosan (excluding current room)
            $exists = Kamar::where('kosan_id', $request->kosan_id)
                ->where('nomor_kamar', $request->nomor_kamar)
                ->where('kamar_id', '!=', $id)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'nomor_kamar' => 'Nomor kamar sudah ada di kos ini.'
                ]);
            }

            $kamar->kosan_id = $request->kosan_id;
            $kamar->nomor_kamar = $request->nomor_kamar;
            $kamar->tipe_kamar = $request->tipe_kamar;
            $kamar->harga_per_bulan = $request->harga_per_bulan;
            $kamar->ukuran_kamar = $request->ukuran_kamar;
            $kamar->kapasitas = $request->kapasitas;
            $kamar->deskripsi = $request->deskripsi;
            $kamar->status_kamar = $request->status_kamar;

            // Hapus foto yang ditandai
            if ($request->filled('hapus_foto')) {
                foreach ($request->hapus_foto as $fotoId) {
                    $foto = FotoProperti::find($fotoId);
                    if ($foto && $foto->properti_id == $id) {
                        if (Storage::disk('public')->exists($foto->path_foto)) {
                            Storage::disk('public')->delete($foto->path_foto);
                        }
                        $foto->delete();
                    }
                }
            }

            // Upload foto utama baru
            if ($request->hasFile('foto_kamar')) {
                // Hapus file foto lama jika ada
                if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
                    Storage::disk('public')->delete($kamar->foto_kamar);
                }
                
                // Simpan path baru ke kolom foto_kamar
                $path = $request->file('foto_kamar')->store('kamar', 'public');
                $kamar->foto_kamar = $path;
            }

            // Upload foto tambahan baru
            if ($request->hasFile('foto_tambahan')) {
                $existingCount = FotoProperti::where('properti_type', 'kamar')
                    ->where('properti_id', $id)
                    ->count();

                $urutan = max(1, $existingCount + 1);

                foreach ($request->file('foto_tambahan') as $foto) {
                    $totalFoto = FotoProperti::where('properti_type', 'kamar')
                        ->where('properti_id', $id)
                        ->count();

                    if ($totalFoto >= 3) break; // Max 3 "other" photos

                    $path = $foto->store('kamar', 'public');
                    FotoProperti::create([
                        'properti_type' => 'kamar',
                        'properti_id' => $kamar->kamar_id,
                        'path_foto' => $path,
                        'urutan' => $urutan,
                        'is_utama' => false,
                        'ukuran_file' => $foto->getSize(),
                    ]);

                    $urutan++;
                }
            }

            $kamar->save();

            // Sync fasilitas kamar
            if ($request->has('fasilitas')) {
                $kamar->fasilitas()->sync($request->fasilitas);
            } else {
                $kamar->fasilitas()->sync([]);
            }

            DB::commit();

            return redirect()->route('admin.manajemen-kamar.index')
                ->with('success', 'Kamar berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat memperbarui kamar: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus kamar dari database
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $kamar = Kamar::findOrFail($id);
            $idKosan = $kamar->kosan_id;

            // Delete all foto_properti
            $fotos = FotoProperti::where('properti_type', 'kamar')
                ->where('properti_id', $id)
                ->get();

            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto->path_foto)) {
                    Storage::disk('public')->delete($foto->path_foto);
                }
                $foto->delete();
            }

            // Delete kamar photo (backward compatibility)
            if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
                Storage::disk('public')->delete($kamar->foto_kamar);
            }

            $this->kamarService->deleteKamar($id);

            DB::commit();

            // If accessed from kosan page, redirect back to that page
            if (request()->has('from_kosan') && request()->from_kosan) {
                return redirect()->route('admin.manajemen-kamar.by-kosan', $idKosan)
                    ->with('success', 'Kamar berhasil dihapus!');
            }

            return redirect()->route('admin.manajemen-kamar.index')
                ->with('success', 'Kamar berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus kamar: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status kamar
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
        ]);

        try {
            $kamar = $this->kamarService->changeKamarStatus($id, $request->status_kamar);
            return redirect()->back()->with('success', "Status kamar berhasil diubah menjadi {$request->status_kamar}");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah status kamar: ' . $e->getMessage());
        }
    }
}
