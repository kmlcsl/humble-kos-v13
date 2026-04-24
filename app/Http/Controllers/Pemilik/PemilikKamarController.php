<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\FotoProperti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PemilikKamarController extends Controller
{
    /**
     * Display listing of all rooms from owner's boarding houses
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $kosanIds = Kosan::where('owner_id', $user->user_id)->pluck('kosan_id');

        $query = Kamar::with('kosan')
            ->whereIn('kosan_id', $kosanIds);

        if ($request->has('id_kosan') && $request->id_kosan != 'all') {
            $query->where('kosan_id', $request->id_kosan);
        }

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_kamar', $request->status);
        }

        if ($request->has('nomor_kamar') && $request->nomor_kamar != '') {
            $query->where('nomor_kamar', 'LIKE', '%' . $request->nomor_kamar . '%');
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $kamars = $query->orderBy($sortBy, $sortDirection)->paginate(10);

        $kosans = Kosan::where('owner_id', $user->user_id)->get();

        $allOwnerKamar = Kamar::whereIn('kosan_id', $kosanIds)->get();
        $totalKamar = $allOwnerKamar->count();
        $kamarTersedia = $allOwnerKamar->where('status_kamar', 'tersedia')->count();
        $kamarTerisi = $allOwnerKamar->where('status_kamar', 'terisi')->count();
        $kamarPemeliharaan = $allOwnerKamar->where('status_kamar', 'maintenance')->count();

        $stats = [
            'total_kamar' => $totalKamar,
            'kamar_tersedia' => $kamarTersedia,
            'kamar_terisi' => $kamarTerisi,
            'kamar_pemeliharaan' => $kamarPemeliharaan,
        ];

        return view('pemilik.kamar.index', [
            'kamars' => $kamars,
            'kosans' => $kosans,
            'stats'  => $stats,
        ]);
    }

    /**
     * Display rooms by specific boarding house
     */
    public function byKosan($kosanId)
    {
        $user = Auth::user();

        $kosans = Kosan::where('kosan_id', $kosanId)
            ->where('owner_id', $user->user_id)
            ->firstOrFail();

        $kamarList = Kamar::where('kosan_id', $kosanId)
            ->with('fasilitas')
            ->orderBy('nomor_kamar', 'asc')
            ->paginate(10);

        return view('pemilik.kamar.by-kosan', [
            'kamarList' => $kamarList,
            'kosans'    => $kosans,
        ]);
    }

    /**
     * Show form to create new room
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $kosans = Kosan::where('owner_id', $user->user_id)
            ->where('status_validasi', 'approved')
            ->get();

        $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();
        $selectedKosan = $request->get('kosan_id');

        return view('pemilik.kamar.create', [
            'kosans'        => $kosans,
            'fasilitas'     => $fasilitas,
            'selectedKosan' => $selectedKosan,
        ]);
    }

    /**
     * Store new room
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kosan_id' => 'required|exists:kosan,kosan_id',
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:single,double,shared',
            'harga_per_bulan' => 'required|numeric|min:0',
            'ukuran_kamar' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
            'fasilitas' => 'nullable|array',
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
            $kosan = Kosan::where('kosan_id', $validated['kosan_id'])
                ->where('owner_id', $user->user_id)
                ->firstOrFail();

            $exists = Kamar::where('kosan_id', $validated['kosan_id'])
                ->where('nomor_kamar', $validated['nomor_kamar'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'nomor_kamar' => 'Nomor kamar sudah ada di kos ini.'
                ]);
            }

            if ($request->hasFile('foto_kamar')) {
                $file = $request->file('foto_kamar');
                $filename = time() . '_' . Str::slug($request->nomor_kamar) . '.' . $file->getClientOriginalExtension();
                $validated['foto_kamar'] = $file->storeAs('kamar', $filename, 'public');
            }

            $kamar = Kamar::create($validated);

            // Upload foto tambahan (2-4)
            if ($request->hasFile('foto_tambahan')) {
                $urutan = 2;
                foreach ($request->file('foto_tambahan') as $foto) {
                    if ($urutan > 4) break;

                    $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $path = $foto->storeAs('kamar', $filename, 'public');

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

            if ($request->has('fasilitas')) {
                $kamar->fasilitas()->attach($request->fasilitas);
            }

            DB::commit();

            return redirect()
                ->route('pemilik.kamar.show', $kamar->kamar_id)
                ->with('success', 'Kamar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display single room details
     */
    public function show($id)
    {
        $user = Auth::user();

        $kamars = Kamar::with([
            'kosan',
            'fasilitas',
            'fotos',
            'fotoTambahan',
            'bookings' => function ($query) {
                $query->whereIn('status_booking', ['pending', 'confirmed'])
                    ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('kosan', function ($query) use ($user) {
            $query->where('owner_id', $user->user_id);
        })
        ->findOrFail($id);

        return view('pemilik.kamar.show', [
            'kamars' => $kamars,
        ]);
    }

    /**
     * Show form to edit (GET) or update (PUT) room
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $kamars = Kamar::with(['kosan', 'fasilitas', 'fotos'])
                ->whereHas('kosan', function ($query) use ($user) {
                    $query->where('owner_id', $user->user_id);
                })
                ->findOrFail($id);

            $fasilitas = Fasilitas::orderBy('nama_fasilitas')->get();
            $kosans = Kosan::where('owner_id', $user->user_id)->get();

            return view('pemilik.kamar.update', [
                'kamars'     => $kamars,
                'fasilitas' => $fasilitas,
                'kosans'    => $kosans,
            ]);
        }

        // Jika request PUT, proses update
        $kamar = Kamar::whereHas('kosan', function ($query) use ($user) {
            $query->where('owner_id', $user->user_id);
        })->findOrFail($id);

        $validated = $request->validate([
            'nomor_kamar' => 'required|string|max:50',
            'tipe_kamar' => 'required|in:single,double,shared',
            'harga_per_bulan' => 'required|numeric|min:0',
            'ukuran_kamar' => 'required|string|max:50',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'foto_kamar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
            'fasilitas' => 'nullable|array',
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
            'nomor_kamar' => 'Nomor Kamar',
            'tipe_kamar' => 'Tipe Kamar',
            'harga_per_bulan' => 'Harga Per Bulan',
            'ukuran_kamar' => 'Ukuran Kamar',
            'kapasitas' => 'Kapasitas',
            'status_kamar' => 'Status Kamar',
            'foto_kamar' => 'Foto Kamar',
        ]);
        
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
        if ($request->has('hapus_foto')) {
            $request->validate([
                'hapus_foto' => 'array',
                'hapus_foto.*' => 'integer|exists:foto_properti,foto_id',
            ]);
        }

        DB::beginTransaction();

        try {
            $exists = Kamar::where('kosan_id', $kamar->kosan_id)
                ->where('nomor_kamar', $validated['nomor_kamar'])
                ->where('kamar_id', '!=', $id)
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'nomor_kamar' => 'Nomor kamar sudah ada di kos ini.'
                ]);
            }

            // Hapus foto yang ditandai
            if ($request->filled('hapus_foto')) {
                foreach ($request->hapus_foto as $fotoId) {
                    $foto = FotoProperti::find($fotoId);
                    // Relaxed the check here
                    if ($foto && $foto->properti_id == $id) {
                        if (Storage::disk('public')->exists($foto->path_foto)) {
                            Storage::disk('public')->delete($foto->path_foto);
                        }
                        $foto->delete();
                    }
                }
            }

            // Update main photo
            if ($request->hasFile('foto_kamar')) {
                if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
                    Storage::disk('public')->delete($kamar->foto_kamar);
                }
                $file = $request->file('foto_kamar');
                $filename = time() . '_' . Str::slug($request->nomor_kamar) . '.' . $file->getClientOriginalExtension();
                $kamar->foto_kamar = $file->storeAs('kamar', $filename, 'public');
            }

            // Upload additional photos
            if ($request->hasFile('foto_tambahan')) {
                $existingCount = FotoProperti::where('properti_type', 'kamar')
                    ->where('properti_id', $id)
                    ->count();
                $urutan = max(1, $existingCount + 1);

                foreach ($request->file('foto_tambahan') as $foto) {
                    if (FotoProperti::where('properti_type', 'kamar')->where('properti_id', $id)->count() >= 3) break;

                    $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $path = $foto->storeAs('kamar', $filename, 'public');

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

            $kamar->nomor_kamar = $validated['nomor_kamar'];
            $kamar->tipe_kamar = $validated['tipe_kamar'];
            $kamar->harga_per_bulan = $validated['harga_per_bulan'];
            $kamar->ukuran_kamar = $validated['ukuran_kamar'];
            $kamar->kapasitas = $validated['kapasitas'];
            $kamar->deskripsi = $validated['deskripsi'];
            $kamar->status_kamar = $validated['status_kamar'];
            $kamar->save();

            if ($request->has('fasilitas')) {
                $kamar->fasilitas()->sync($request->fasilitas);
            } else {
                $kamar->fasilitas()->detach();
            }

            DB::commit();

            return redirect()
                ->route('pemilik.kamar.show', $kamar->kamar_id)
                ->with('success', 'Data kamar berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete room
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $kamar = Kamar::whereHas('kosan', function ($query) use ($user) {
                $query->where('owner_id', $user->user_id);
            })->findOrFail($id);

            if ($kamar->bookings()
                ->whereIn('status_booking', ['pending', 'confirmed'])
                ->exists()) {
                return back()->with('error', 'Tidak dapat menghapus kamar karena masih memiliki booking aktif.');
            }

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

            $kamar->fasilitas()->detach();
            $kamar->delete();

            DB::commit();

            return redirect()
                ->route('pemilik.kamar.index')
                ->with('success', 'Kamar berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Change room status
     */
    public function changeStatus(Request $request, $id)
    {
        $user = Auth::user();

        $kamar = Kamar::whereHas('kosan', function ($query) use ($user) {
            $query->where('owner_id', $user->user_id);
        })->findOrFail($id);

        $validated = $request->validate([
            'status_kamar' => 'required|in:tersedia,terisi,maintenance',
        ]);

        $kamar->update($validated);

        return back()->with('success', 'Status kamar berhasil diubah.');
    }

    /**
     * Update room facilities
     */
    public function updateFasilitas(Request $request, $id)
    {
        $user = Auth::user();

        $kamar = Kamar::whereHas('kosan', function ($query) use ($user) {
            $query->where('owner_id', $user->user_id);
        })->findOrFail($id);

        $validated = $request->validate([
            'fasilitas' => 'nullable|array',
            'fasilitas.*' => 'exists:fasilitas,fasilitas_id',
        ]);

        if ($request->has('fasilitas')) {
            $kamar->fasilitas()->sync($request->fasilitas);
        } else {
            $kamar->fasilitas()->detach();
        }

        return back()->with('success', 'Fasilitas kamar berhasil diperbarui.');
    }
}
