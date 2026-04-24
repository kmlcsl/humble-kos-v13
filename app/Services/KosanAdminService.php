<?php

namespace App\Services;

use App\Models\Kosan;
use App\Models\Fasilitas;
use App\Models\UlasanKosan;
use App\Models\BookingKosan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KosanAdminService
{
    /**
     * Mendapatkan daftar kosan dengan filter untuk dashboard admin
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getKosanListWithFilter(Request $request)
    {
        $query = Kosan::query();

        // Filter berdasarkan nama
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nama_kosan', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status validasi
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status_validasi', $request->status);
        }

        // Filter berdasarkan tipe kosan
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

        // Return with pagination
        return $query->paginate($request->per_page ?? 10);
    }

    /**
     * Mendapatkan data dashboard untuk kosan
     *
     * @return array
     */
    public function getKosanDashboardData()
    {
        $data = [
            'total_kosan' => Kosan::count(),
            'kosan_approved' => Kosan::where('status_validasi', 'approved')->count(),
            'kosan_pending' => Kosan::where('status_validasi', 'pending')->count(),
            'kosan_rejected' => Kosan::where('status_validasi', 'rejected')->count(),
            'total_kamar' => \App\Models\Kamar::count(),
            'kamar_tersedia' => \App\Models\Kamar::where('status_kamar', 'tersedia')->count(),
            'kamar_terpakai' => \App\Models\Kamar::where('status_kamar', 'terisi')->count(),
            'tipe_kosan' => [
                'putra' => Kosan::where('tipe_kosan', 'putra')->count(),
                'putri' => Kosan::where('tipe_kosan', 'putri')->count(),
                'campur' => Kosan::where('tipe_kosan', 'campur')->count()
            ],
            'kota_terbanyak' => Kosan::select('kota', DB::raw('count(*) as total'))
                ->groupBy('kota')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get(),
            'kos_terbaru' => Kosan::orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];

        return $data;
    }

    /**
     * Menyimpan kosan baru
     *
     * @param array $data
     * @param array $photos
     * @param int $mainPhotoIndex
     * @return Kosan
     */
    public function createKosan(array $data, array $photos = [], $mainPhotoIndex = 0)
    {
        DB::beginTransaction();

        try {
            // Prepare data for mass assignment
            $dataToCreate = [
                'nama_kosan' => $data['nama_kosan'] ?? null,
                'deskripsi' => $data['deskripsi'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'kota' => $data['kota'] ?? null,
                'tipe_kosan' => $data['jenis_kos'] ?? $data['tipe_kosan'] ?? 'campur',
                'peraturan' => $data['peraturan'] ?? '',
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'owner_id' => $data['id_pemilik'] ?? $data['owner_id'] ?? null,
                'status_validasi' => $data['status_validasi'] ?? 'approved',
                'rating_rata' => 0.0,
            ];

            // Create kosan using mass assignment
            $kosan = Kosan::create($dataToCreate);

            // Upload main photo
            if (!empty($photos)) {
                $mainPhoto = $photos[$mainPhotoIndex] ?? $photos[0];
                $path = $mainPhoto->store('kosan', 'public');
                $kosan->foto_kosan = $path;
                $kosan->save();
            }

            DB::commit();
            return $kosan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Memperbarui data kosan
     *
     * @param int $id
     * @param array $data
     * @return Kosan
     */
    public function updateKosan($id, array $data)
    {
        DB::beginTransaction();

        try {
            $kosan = Kosan::findOrFail($id);
            $kosan->nama_kosan = $data['nama_kosan'] ?? $data['nama_kosan'];
            $kosan->deskripsi = $data['deskripsi'];
            $kosan->alamat = $data['alamat'];
            $kosan->kota = $data['kota'];
            $kosan->tipe_kosan = $data['jenis_kos'] ?? $data['tipe_kosan'];
            $kosan->peraturan = $data['peraturan'] ?? $kosan->peraturan;
            $kosan->latitude = $data['latitude'] ?? null;
            $kosan->longitude = $data['longitude'] ?? null;
            $kosan->owner_id = $data['id_pemilik'] ?? $data['owner_id'] ?? null;
            if (isset($data['status_validasi'])) {
                $kosan->status_validasi = $data['status_validasi'];
            }
            $kosan->save();

            // DISABLED: Struktur fasilitas berbeda
            // Fasilitas adalah tabel master, bukan field boolean per kosan

            DB::commit();
            return $kosan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * DISABLED: Method ini tidak tersedia karena FotoKosan tidak ada di struktur database
     * Foto kosan disimpan sebagai varchar(255) di field foto_kosan, bukan tabel terpisah
     */
    public function addKosanPhotos($kosanId, array $photos)
    {
        throw new \Exception('Method tidak tersedia. Gunakan upload foto langsung ke field foto_kosan.');
    }

    /**
     * DISABLED: Method ini tidak tersedia karena FotoKosan tidak ada di struktur database
     */
    public function deleteKosanPhoto($photoId)
    {
        throw new \Exception('Method tidak tersedia. Gunakan delete foto langsung dari field foto_kosan.');
    }

    /**
     * DISABLED: Method ini tidak tersedia karena FotoKosan tidak ada di struktur database
     */
    public function setMainPhoto($kosanId, $photoId)
    {
        throw new \Exception('Method tidak tersedia. Hanya satu foto utama yang disimpan di field foto_kosan.');
    }

    /**
     * Mendapatkan statistik booking kosan
     *
     * @param int $kosanId
     * @return array
     */
    public function getKosanBookingStats($kosanId)
    {
        $stats = [
            'total_bookings' => BookingKosan::where('kosan_id', $kosanId)->count(),
            'pending' => BookingKosan::where('kosan_id', $kosanId)->where('status_booking', 'pending')->count(),
            'confirmed' => BookingKosan::where('kosan_id', $kosanId)->where('status_booking', 'confirmed')->count(),
            'cancelled' => BookingKosan::where('kosan_id', $kosanId)->where('status_booking', 'cancelled')->count(),
            'completed' => 0,
            'recent_bookings' => BookingKosan::where('kosan_id', $kosanId)
                ->with('pengguna')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'booking_by_month' => BookingKosan::where('kosan_id', $kosanId)
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as total'))
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get()
        ];

        return $stats;
    }

    /**
     * Mendapatkan statistik ulasan kosan
     *
     * @param int $kosanId
     * @return array
     */
    public function getKosanReviewStats($kosanId)
    {
        $stats = [
            'total_reviews' => UlasanKosan::where('id_kosan', $kosanId)->count(),
            'avg_rating' => UlasanKosan::where('id_kosan', $kosanId)->avg('rating') ?: 0,
            'rating_distribution' => [
                '5_star' => UlasanKosan::where('id_kosan', $kosanId)->where('rating', 5)->count(),
                '4_star' => UlasanKosan::where('id_kosan', $kosanId)->where('rating', 4)->count(),
                '3_star' => UlasanKosan::where('id_kosan', $kosanId)->where('rating', 3)->count(),
                '2_star' => UlasanKosan::where('id_kosan', $kosanId)->where('rating', 2)->count(),
                '1_star' => UlasanKosan::where('id_kosan', $kosanId)->where('rating', 1)->count(),
            ],
            'recent_reviews' => UlasanKosan::where('id_kosan', $kosanId)
                ->with('pengguna')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
        ];

        return $stats;
    }

    /**
     * Menghapus kosan dan semua data terkait
     *
     * @param int $kosanId
     * @return bool
     */
    public function deleteKosan($kosanId)
    {
        DB::beginTransaction();

        try {
            $kosan = Kosan::findOrFail($kosanId);

            // Delete kosan photo if exists
            if ($kosan->foto_kosan && Storage::disk('public')->exists($kosan->foto_kosan)) {
                Storage::disk('public')->delete($kosan->foto_kosan);
            }

            // DISABLED: Struktur fasilitas berbeda
            // Fasilitas adalah tabel master, tidak perlu dihapus per kosan

            // Delete the kosan (related records will be cascade deleted due to foreign keys)
            $kosan->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle status validasi kosan
     *
     * @param int $kosanId
     * @return Kosan
     */
    public function toggleKosanStatus($kosanId)
    {
        $kosan = Kosan::findOrFail($kosanId);
        $kosan->status_validasi = ($kosan->status_validasi === 'approved') ? 'rejected' : 'approved';
        $kosan->save();

        return $kosan;
    }

    /**
     * Toggle status unggulan kosan
     * DISABLED: Field kos_unggulan tidak ada di struktur database yang valid
     *
     * @param int $kosanId
     * @return Kosan
     */
    public function toggleKosanFeatured($kosanId)
    {
        // Field kos_unggulan tidak ada di database
        throw new \Exception('Fitur kos unggulan tidak tersedia karena field tidak ada di database');
    }

    /**
     * Mendapatkan daftar kota untuk filter
     *
     * @return array
     */
    public function getKosanCities()
    {
        return Kosan::select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota')
            ->toArray();
    }

    /**
     * Export data kosan ke format CSV
     *
     * @param array $filters
     * @return string
     */
    public function exportKosanToCsv($filters = [])
    {
        $query = Kosan::with('fasilitas');

        // Apply filters if any
        if (!empty($filters)) {
            if (isset($filters['search'])) {
                $query->where('nama_kosan', 'like', '%' . $filters['search'] . '%');
            }

            if (isset($filters['status']) && $filters['status'] !== 'all') {
                $query->where('status_validasi', $filters['status']);
            }

            if (isset($filters['jenis_kos']) && $filters['jenis_kos'] !== 'all') {
                $query->where('tipe_kosan', $filters['jenis_kos']);
            }

            if (isset($filters['kota']) && !empty($filters['kota'])) {
                $query->where('kota', $filters['kota']);
            }
        }

        $kosans = $query->get();

        $headers = [
            'ID',
            'Nama Kosan',
            'Tipe Kosan',
            'Alamat',
            'Kota',
            'Deskripsi',
            'Peraturan',
            'Status Validasi',
            'Rating Rata-rata',
            'Owner ID',
            'Tanggal Dibuat'
        ];

        $csv = implode(',', $headers) . "\n";

        foreach ($kosans as $kosan) {
            $row = [
                $kosan->kosan_id ?? $kosan->kosan_id ?? '',
                '"' . str_replace('"', '""', $kosan->nama_kosan ?? '') . '"',
                $kosan->tipe_kosan ?? '',
                '"' . str_replace('"', '""', $kosan->alamat ?? '') . '"',
                $kosan->kota ?? '',
                '"' . str_replace('"', '""', substr($kosan->deskripsi ?? '', 0, 100)) . '"',
                '"' . str_replace('"', '""', substr($kosan->peraturan ?? '', 0, 100)) . '"',
                ucfirst($kosan->status_validasi ?? 'pending'),
                $kosan->rating_rata ?? 0,
                $kosan->owner_id ?? '',
                $kosan->created_at ? $kosan->created_at->format('Y-m-d H:i:s') : ''
            ];

            $csv .= implode(',', $row) . "\n";
        }

        return $csv;
    }
}
