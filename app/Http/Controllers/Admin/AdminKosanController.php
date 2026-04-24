<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\User;
use App\Models\FotoProperti;
use App\Services\KosanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminKosanController extends Controller
{
    protected $kosanService;

    public function __construct(KosanService $kosanService)
    {
        // $this->middleware('auth:admin');
        $this->kosanService = $kosanService;
    }

    /**
     * Menampilkan daftar semua kos
     */
    public function index(Request $request)
    {
        $query = Kosan::query();

        // Filter berdasarkan nama (schema: nama_kosan)
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_kosan', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status validasi (schema: status_validasi)
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status_validasi', $request->status);
        }

        // Filter berdasarkan tipe kosan (schema: tipe_kosan)
        if ($request->has('jenis_kos') && $request->jenis_kos !== 'all') {
            $query->where('tipe_kosan', $request->jenis_kos);
        }

        // Filter berdasarkan kota
        if ($request->has('kota') && !empty($request->kota)) {
            $query->where('kota', $request->kota);
        }

        // Urutkan
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Eager load relationships
        $query->with(['pemilik']);

        // Get data with pagination
        $kosans = $query->paginate(10);

        // Get list of cities for filter
        $cities = Kosan::select('kota')->distinct()->orderBy('kota')->pluck('kota');

        // Calculate stats per schema
        $approvedCount = Kosan::where('status_validasi', 'approved')->count();
        $stats = [
            'total_kosan' => Kosan::count(),
            'kosan_approved' => $approvedCount,
            'kosan_aktif' => $approvedCount,
            'total_kamar' => Kamar::count(),
            'kamar_tersedia' => Kamar::where('status_kamar', 'tersedia')->count(),
            'kosan_unggulan' => 0,
        ];

        return view('admin.manajemen-kosan.index', [
            'kosans' => $kosans,
            'cities' => $cities,
            'stats' => $stats,
        ]);
    }

    /**
     * Menampilkan form untuk membuat kos baru
     */
    public function create()
    {
        $owners = User::where('role', 'pemilik_kos')->get();

        return view('admin.manajemen-kosan.create', [
            'owners' => $owners,
        ]);
    }

    /**
     * Menyimpan kos baru ke database
     */
    public function store(Request $request)
    {
        // Validasi berdasarkan struktur database yang valid
        $validatedData = $request->validate([
            'nama_kosan' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'jenis_kos' => 'required|in:putra,putri,campur',
            'peraturan' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'id_pemilik' => 'nullable|exists:users,user_id',
            'status_validasi' => 'nullable|in:pending,approved,rejected',
            'foto_kosan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga_tahunan' => 'nullable|numeric|min:0',
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
            // Prepare data for mass assignment
            $dataToCreate = [
                'nama_kosan' => $validatedData['nama_kosan'],
                'deskripsi' => $validatedData['deskripsi'],
                'alamat' => $validatedData['alamat'],
                'kota' => $validatedData['kota'],
                'tipe_kosan' => $validatedData['jenis_kos'],
                'peraturan' => $validatedData['peraturan'] ?? '',
                'latitude' => $validatedData['latitude'] ?? null,
                'longitude' => $validatedData['longitude'] ?? null,
                'owner_id' => $validatedData['id_pemilik'] ?? null,
                'status_validasi' => $validatedData['status_validasi'] ?? 'pending',
                'rating_rata' => 0.0, // Use float in mass assignment context
            ];

            // Create new kosan using mass assignment
            $kosan = Kosan::create($dataToCreate);

            // Upload and save kosan main photo (backward compatibility)
            if ($request->hasFile('foto_kosan')) {
                $files = $request->file('foto_kosan');
                $mainPhoto = is_array($files) ? $files[0] : $files;
                $path = $mainPhoto->store('kosan', 'public');
                $kosan->foto_kosan = $path;
                $kosan->save();

                // The main photo path is already saved via mass assignment.
                // No need to create a separate entry in foto_properti for the main photo.
            }

            // Upload foto tambahan (2-4)
            if ($request->hasFile('foto_tambahan')) {
                $fotoTambahan = $request->file('foto_tambahan');
                $urutan = 2; // Mulai dari urutan 2 (karena 1 adalah foto utama)

                foreach ($fotoTambahan as $foto) {
                    if ($urutan > 4) break; // Max 4 foto total

                    $path = $foto->store('kosan', 'public');
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

            return redirect()->route('admin.manajemen-kosan.index')
                ->with('success', 'Kosan berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menambahkan kosan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menampilkan detail kos
     */
    public function show($kosan_id)
    {
        $kosan = Kosan::with(['pemilik', 'kamars'])
            ->findOrFail($kosan_id);

        // Get booking statistics
        $bookingStats = [
            'total_bookings' => $kosan->bookings()->count(),
            'pending' => $kosan->bookings()->where('status_booking', 'pending')->count(),
            'confirmed' => $kosan->bookings()->where('status_booking', 'confirmed')->count(),
            'completed' => $kosan->bookings()->where('status_booking', 'completed')->count(),
            'recent_bookings' => $kosan->bookings()
                ->with('user')
                ->latest()
                ->limit(5)
                ->get()
        ];

        // Get review statistics
        $reviews = $kosan->ulasanReview;
        $totalReviews = $reviews->count();

        $reviewStats = [
            'total_reviews' => $totalReviews,
            'avg_rating' => $totalReviews > 0 ? $reviews->avg('rating') : 0,
            'rating_distribution' => [
                '5_star' => $reviews->where('rating', 5)->count(),
                '4_star' => $reviews->where('rating', 4)->count(),
                '3_star' => $reviews->where('rating', 3)->count(),
                '2_star' => $reviews->where('rating', 2)->count(),
                '1_star' => $reviews->where('rating', 1)->count(),
            ],
            'recent_reviews' => $reviews->sortByDesc('created_at')->take(5)
        ];

        return view('admin.manajemen-kosan.show', [
            'kosan' => $kosan,
            'bookingStats' => $bookingStats,
            'reviewStats' => $reviewStats,
        ]);
    }

    /**
     * Menampilkan form edit (GET) atau menyimpan perubahan (PUT)
     */
    public function update(Request $request, $kosan_id)
    {
        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $kosan = Kosan::with(['kamars.fasilitas', 'fotos'])->findOrFail($kosan_id);
            $owners = User::where('role', 'pemilik_kos')->get();

            return view('admin.manajemen-kosan.update', [
                'kosan' => $kosan,
                'owners' => $owners,
            ]);
        }

        // Jika request PUT, proses update
        $rules = [
            'nama_kosan' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'jenis_kos' => 'required|in:putra,putri,campur',
            'peraturan' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'id_pemilik' => 'nullable|exists:users,user_id',
            'status_validasi' => 'nullable|in:pending,approved,rejected',
            'harga_tahunan' => 'nullable|numeric|min:0',
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

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $kosan = Kosan::findOrFail($kosan_id);
            $kosan->nama_kosan = $request->nama_kosan;
            $kosan->deskripsi = $request->deskripsi;
            $kosan->alamat = $request->alamat;
            $kosan->kota = $request->kota;
            $kosan->tipe_kosan = $request->jenis_kos;
            $kosan->peraturan = $request->peraturan ?? $kosan->peraturan;
            $kosan->latitude = $request->latitude;
            $kosan->longitude = $request->longitude;
            $kosan->owner_id = $request->id_pemilik;
            if ($request->has('status_validasi')) {
                $kosan->status_validasi = $request->status_validasi;
            }
            if ($request->filled('harga_tahunan')) {
                $kosan->harga_tahunan = $request->harga_tahunan;
            }

            // Hapus foto yang ditandai untuk dihapus
            if ($request->filled('hapus_foto')) {
                foreach ($request->hapus_foto as $fotoId) {
                    $foto = FotoProperti::find($fotoId);
if ($foto && $foto->properti_id == $kosan_id) {
                        // Hapus file fisik
                        if (Storage::disk('public')->exists($foto->path_foto)) {
                            Storage::disk('public')->delete($foto->path_foto);
                        }
                        // Hapus record
                        $foto->delete();
                    }
                }
            }

            // Update kosan main photo if uploaded
            if ($request->hasFile('foto_kosan')) {
                // Delete old photo file if it exists
                if ($kosan->foto_kosan && Storage::disk('public')->exists($kosan->foto_kosan)) {
                    Storage::disk('public')->delete($kosan->foto_kosan);
                }
                // Upload new photo and update the kosan table
                $mainPhoto = $request->file('foto_kosan');
                $path = $mainPhoto->store('kosan', 'public');
                $kosan->foto_kosan = $path; // This is the only place it should be saved
            }

            // Upload foto tambahan baru
            if ($request->hasFile('foto_tambahan')) {
                // Hitung urutan berikutnya
                $existingCount = FotoProperti::where('properti_type', 'kosan')
                    ->where('properti_id', $kosan_id)
                    ->count();

                $urutan = max(2, $existingCount + 1); // Minimal urutan 2

                foreach ($request->file('foto_tambahan') as $foto) {
                    // Max 4 foto total
                    if (FotoProperti::where('properti_type', 'kosan')->where('properti_id', $kosan_id)->count() >= 4) break;

                    $path = $foto->store('kosan', 'public');
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

            $kosan->save();
            DB::commit();

            return redirect()->route('admin.manajemen-kosan.index')
                ->with('success', 'Kosan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui kosan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus kos dari database
     */
    public function destroy($kosan_id)
    {
        DB::beginTransaction();

        try {
            $kosan = Kosan::findOrFail($kosan_id);

            // Delete kosan photo if exists (backward compatibility)
            if ($kosan->foto_kosan && Storage::disk('public')->exists($kosan->foto_kosan)) {
                Storage::disk('public')->delete($kosan->foto_kosan);
            }

            // Delete all foto_properti
            $fotos = FotoProperti::where('properti_type', 'kosan')
                ->where('properti_id', $kosan_id)
                ->get();

            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto->path_foto)) {
                    Storage::disk('public')->delete($foto->path_foto);
                }
                $foto->delete();
            }

            // Delete the kosan (cascade will delete kamars and related data)
            $kosan->delete();

            DB::commit();

            return redirect()->route('admin.manajemen-kosan.index')
                ->with('success', 'Kosan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus kosan: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status kosan (approved/rejected)
     */
    public function toggleStatus(Request $request, $kosan_id)
    {
        try {
            $kosan = Kosan::findOrFail($kosan_id);
            $currentStatus = $kosan->status_validasi;
            
            // The logic here is to approve if pending, otherwise toggle between approved and rejected.
            if ($currentStatus == 'pending') {
                $kosan->status_validasi = 'approved';
            } else {
                $kosan->status_validasi = ($currentStatus === 'approved') ? 'rejected' : 'approved';
            }
            
            $kosan->save();

            $newStatus = $kosan->status_validasi;
            $message = "Status kosan berhasil diubah menjadi " . ucfirst($newStatus) . "!";

            return response()->json(['success' => true, 'message' => $message, 'new_status' => $newStatus]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle featured kosan (featured/not featured)
     * DISABLED: Field kos_unggulan tidak ada di struktur database yang valid
     */
    public function toggleFeatured($kosan_id)
    {
        return redirect()->back()
            ->with('error', 'Fitur kos unggulan tidak tersedia karena field tidak ada di database');
    }
}
