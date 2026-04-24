<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\FotoProperti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PemilikKosanController extends Controller
{
    /**
     * Display listing of boarding houses owned by this user
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Kosan::where('owner_id', $user->user_id)
            ->withCount('kamars');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('status_validasi', $request->status);
        }

        if ($request->has('jenis_kos') && $request->jenis_kos != 'all') {
            $query->where('tipe_kosan', $request->jenis_kos);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kosan', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('alamat', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('kota', 'LIKE', '%' . $request->search . '%');
            });
        }

        $kosans = $query->orderBy('created_at', 'desc')->paginate(10);

        $allOwnerKosan = Kosan::where('owner_id', $user->user_id)->get();
        $totalKosan = $allOwnerKosan->count();
        $kosanApproved = $allOwnerKosan->where('status_validasi', 'approved')->count();
        $kosanPending = $allOwnerKosan->where('status_validasi', 'pending')->count();

        $totalKamar = Kamar::whereIn('kosan_id', $allOwnerKosan->pluck('kosan_id'))->count();
        $kamarTersedia = Kamar::whereIn('kosan_id', $allOwnerKosan->pluck('kosan_id'))
            ->where('status_kamar', 'tersedia')
            ->count();

        $stats = [
            'total_kosan' => $totalKosan,
            'kosan_approved' => $kosanApproved,
            'kosan_pending' => $kosanPending,
            'total_kamar' => $totalKamar,
            'kamar_tersedia' => $kamarTersedia,
        ];

        return view('pemilik.kosan.index', [
            'kosans' => $kosans,
            'stats' => $stats,
        ]);
    }

    /**
     * Show form to create new boarding house
     */
    public function create()
    {
        $owners = User::where('role', 'pemilik_kos')->get();
        
        return view('pemilik.kosan.create', [
            'owners' => $owners
        ]);
    }

    /**
     * Store new boarding house
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kosan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'tipe_kosan' => 'required|in:putra,putri,campur',
            'peraturan' => 'nullable|string',
            'foto_kosan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Validasi foto tambahan secara terpisah
        if ($request->hasFile('foto_tambahan')) {
            $request->validate([
                'foto_tambahan' => 'array|max:3',
                'foto_tambahan.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
        }

        DB::beginTransaction();

        try {
            $user = Auth::user();

            if ($request->hasFile('foto_kosan')) {
                $file = $request->file('foto_kosan');
                $filename = time() . '_' . Str::slug($request->nama_kosan) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('kosan', $filename, 'public');
                $validated['foto_kosan'] = $path;
            }

            $validated['owner_id'] = $user->user_id;
            $validated['status_validasi'] = 'pending';
            $validated['rating_rata'] = 0.0;

            $kosan = Kosan::create($validated);

            // Simpan foto utama ke tabel foto_properti - a path is already in validated
            if (isset($validated['foto_kosan'])) {
                 // The main photo path is already saved via mass assignment.
                // No need to create a separate entry in foto_properti for the main photo.
            }

            // Upload foto tambahan (2-4)
            if ($request->hasFile('foto_tambahan')) {
                $urutan = 2;
                foreach ($request->file('foto_tambahan') as $foto) {
                    if ($urutan > 4) break;

                    $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $path = $foto->storeAs('kosan', $filename, 'public');

                    FotoProperti::create([
                        'properti_type' => 'kosan',
                        'properti_id' => $kosan->kosan_id,
                        'path_foto' => $path,
                        'urutan' => $urutan,
                        'is_utama' => false,
                        'ukuran_file' => $foto->getSize(),
                    ]);

                    $urutan++;
                }
            }

            DB::commit();

            return redirect()->route('pemilik.kosan.show', $kosan->kosan_id)
                ->with('success', 'Kos berhasil ditambahkan dan menunggu validasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display single boarding house details
     */
    public function show($id)
    {
        $user = Auth::user();

        $kosans = Kosan::where('kosan_id', $id)
            ->where('owner_id', $user->user_id)
            ->with([
                'kamars' => function ($query) {
                    $query->withCount('bookings');
                }
            ])
            ->firstOrFail();

        $totalKamar = $kosans->kamars->count();
        $kamarTersedia = $kosans->kamars->where('status_kamar', 'tersedia')->count();
        $kamarTerisi = $kosans->kamars->where('status_kamar', 'terisi')->count();

        return view('pemilik.kosan.show', [
            'kosans' => $kosans,
            'totalKamar' => $totalKamar,
            'kamarTersedia' => $kamarTersedia,
            'kamarTerisi' => $kamarTerisi,
        ]);
    }

    /**
     * Show form to edit (GET) or update (PUT) boarding house
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $kosan = Kosan::with(['kamars.fasilitas', 'fotos'])
                ->where('kosan_id', $id)
                ->where('owner_id', $user->user_id)
                ->firstOrFail();

            $owners = User::where('role', 'pemilik_kos')->get();

            return view('pemilik.kosan.update', [
                'kosan' => $kosan,
                'owners' => $owners,
            ]);
        }

        // Jika request PUT, proses update
        $kosan = Kosan::where('kosan_id', $id)
            ->where('owner_id', $user->user_id)
            ->firstOrFail();

        $rules = [
            'nama_kosan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'jenis_kos' => 'required|in:putra,putri,campur', // Corrected from tipe_kosan
            'peraturan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        // Conditionally add rules for file uploads
        if ($request->hasFile('foto_kosan')) {
            $rules['foto_kosan'] = 'image|mimes:jpeg,png,jpg|max:2048';
        }
        if ($request->hasFile('foto_tambahan')) {
            $rules['foto_tambahan'] = 'array|max:3';
            $rules['foto_tambahan.*'] = 'image|mimes:jpeg,png,jpg|max:2048';
        }
        if ($request->has('hapus_foto')) {
            $rules['hapus_foto'] = 'array';
            $rules['hapus_foto.*'] = 'integer|exists:foto_properti,foto_id';
        }
        
        $validated = $request->validate($rules);

        DB::beginTransaction();

        try {
            // Hapus foto yang ditandai untuk dihapus
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

            // Update foto utama
            if ($request->hasFile('foto_kosan')) {
                // Hapus file foto lama jika ada
                if ($kosan->foto_kosan && Storage::disk('public')->exists($kosan->foto_kosan)) {
                    Storage::disk('public')->delete($kosan->foto_kosan);
                }

                $file = $request->file('foto_kosan');
                $filename = time() . '_' . Str::slug($request->nama_kosan) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('kosan', $filename, 'public');
                $kosan->foto_kosan = $path; // Simpan path baru ke kolom foto_kosan
            }

            // Upload foto tambahan baru
            if ($request->hasFile('foto_tambahan')) {
                $existingCount = FotoProperti::where('properti_type', 'kosan')
                    ->where('properti_id', $id)
                    ->count();

                $urutan = max(2, $existingCount + 1);

                foreach ($request->file('foto_tambahan') as $foto) {
                    if (FotoProperti::where('properti_type', 'kosan')->where('properti_id', $id)->count() >= 4) break;

                    $filename = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
                    $path = $foto->storeAs('kosan', $filename, 'public');

                    FotoProperti::create([
                        'properti_type' => 'kosan',
                        'properti_id' => $kosan->kosan_id,
                        'path_foto' => $path,
                        'urutan' => $urutan,
                        'is_utama' => false,
                        'ukuran_file' => $foto->getSize(),
                    ]);
                    $urutan++;
                }
            }

            // Update model fields
            $kosan->nama_kosan = $validated['nama_kosan'];
            $kosan->alamat = $validated['alamat'];
            $kosan->kota = $validated['kota'];
            $kosan->deskripsi = $validated['deskripsi'];
            $kosan->tipe_kosan = $validated['jenis_kos']; // Use correct key
            $kosan->peraturan = $validated['peraturan'];
            $kosan->latitude = $validated['latitude'];
            $kosan->longitude = $validated['longitude'];

            if ($kosan->status_validasi == 'rejected') {
                $kosan->status_validasi = 'pending';
            }

            $kosan->save();

            DB::commit();

            return redirect()->route('pemilik.kosan.show', $kosan->kosan_id)
                ->with('success', 'Data kos berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete boarding house
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();

            $kosan = Kosan::where('kosan_id', $id)
                ->where('owner_id', $user->user_id)
                ->firstOrFail();

            // Check if has active bookings
            $hasActiveBookings = Kamar::where('kosan_id', $kosan->kosan_id)
                ->whereHas('bookings', function ($query) {
                    $query->whereIn('status_booking', ['pending', 'confirmed']);
                })
                ->exists();

            if ($hasActiveBookings) {
                return back()->with('error', 'Tidak dapat menghapus kos karena masih memiliki booking aktif.');
            }

            // Delete photo (backward compatibility)
            if ($kosan->foto_kosan && Storage::disk('public')->exists($kosan->foto_kosan)) {
                Storage::disk('public')->delete($kosan->foto_kosan);
            }

            // Delete all foto_properti
            $fotos = FotoProperti::where('properti_type', 'kosan')
                ->where('properti_id', $id)
                ->get();

            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto->path_foto)) {
                    Storage::disk('public')->delete($foto->path_foto);
                }
                $foto->delete();
            }

            $kosan->delete();

            DB::commit();

            return redirect()->route('pemilik.kosan.index')
                ->with('success', 'Kos berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Upload additional gallery photos
     */
    public function uploadGaleri(Request $request, $id)
    {
        $user = Auth::user();

        $kosan = Kosan::where('kosan_id', $id)
            ->where('owner_id', $user->user_id)
            ->firstOrFail();

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('kosan/galeri', $filename, 'public');

            return back()->with('success', 'Foto galeri berhasil ditambahkan.');
        }

        return back()->with('error', 'Gagal mengunggah foto.');
    }

    /**
     * Delete gallery photo
     */
    public function deleteGaleri($id, $foto)
    {
        $user = Auth::user();

        $kosan = Kosan::where('kosan_id', $id)
            ->where('owner_id', $user->user_id)
            ->firstOrFail();

        return back()->with('success', 'Foto galeri berhasil dihapus.');
    }
}
