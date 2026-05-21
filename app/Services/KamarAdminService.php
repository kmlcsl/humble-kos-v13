<?php

namespace App\Services;

use App\Models\Kamar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KamarAdminService
{
    /**
     * Mendapatkan semua kamar dengan filter
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllKamar(array $filters = [])
    {
        $query = Kamar::query();

        // Filter berdasarkan id kosan
        if (isset($filters['id_kosan']) && !empty($filters['id_kosan'])) {
            $query->where('kosan_id', $filters['id_kosan']);
        }

        // Filter berdasarkan nomor kamar
        if (isset($filters['nomor_kamar']) && !empty($filters['nomor_kamar'])) {
            $query->where('nomor_kamar', 'like', '%' . $filters['nomor_kamar'] . '%');
        }

        // Filter berdasarkan status
        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status_kamar', $filters['status']);
        }

        // Filter berdasarkan range harga
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('harga_per_bulan', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('harga_per_bulan', '<=', $filters['max_price']);
        }

        // Filter fasilitas via pivot dapat ditambahkan jika diperlukan

        // Urutkan hasil
        $sortField = $filters['sort'] ?? 'nomor_kamar';
        // Map legacy field names to current schema
        if ($sortField === 'harga_bulanan') {
            $sortField = 'harga_per_bulan';
        }
        $sortDirection = $filters['direction'] ?? 'asc';
        $query->orderBy($sortField, $sortDirection);

        // Eager load relationships
        $query->with(['kosan']);

        // Paginate results
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

    /**
     * Mendapatkan detail kamar berdasarkan ID
     *
     * @param int $id
     * @return Kamar
     */
    public function getKamarById(int $id): Kamar
    {
        return Kamar::with(['kosan'])->findOrFail($id);
    }

    /**
     * Membuat kamar baru
     *
     * @param array $data
     * @param array $photos
     * @return Kamar
     */
    public function createKamar(array $data, mixed $photos = null): Kamar
    {
        DB::beginTransaction();

        try {
            // Create kamar
            $kamar = Kamar::create($data);

            // Handle single photo if provided
            if ($photos) {
                $file = is_array($photos) ? $photos[0] : $photos;
                if ($file) {
                    $path = $file->store('kamar', 'public');
                    $kamar->foto_kamar = $path;
                    $kamar->save();
                }
            }

            DB::commit();
            return $kamar;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengupdate kamar
     *
     * @param int $id
     * @param array $data
     * @param array $photos
     * @return Kamar
     */
    public function updateKamar(int $id, array $data, mixed $photos = null): Kamar
    {
        DB::beginTransaction();

        try {
            $kamar = Kamar::findOrFail($id);
            $kamar->update($data);

            // Handle single photo update if provided
            if ($photos) {
                $file = is_array($photos) ? $photos[0] : $photos;
                if ($file) {
                    if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
                        Storage::disk('public')->delete($kamar->foto_kamar);
                    }
                    $path = $file->store('kamar', 'public');
                    $kamar->foto_kamar = $path;
                    $kamar->save();
                }
            }

            // Optional: delete current photo if requested
            if (!empty($data['hapus_foto']) && $kamar->foto_kamar) {
                if (Storage::disk('public')->exists($kamar->foto_kamar)) {
                    Storage::disk('public')->delete($kamar->foto_kamar);
                }
                $kamar->foto_kamar = null;
                $kamar->save();
            }

            DB::commit();
            return $kamar;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Menghapus kamar
     *
     * @param int $id
     * @return bool
     */
    public function deleteKamar(int $id): bool
    {
        DB::beginTransaction();

        try {
            $kamar = Kamar::findOrFail($id);

            // Delete single photo file if exists
            if ($kamar->foto_kamar && Storage::disk('public')->exists($kamar->foto_kamar)) {
                Storage::disk('public')->delete($kamar->foto_kamar);
            }

            // Delete the kamar
            $kamar->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mengubah status kamar
     *
     * @param int $id
     * @param string $status
     * @return Kamar
     */
    public function changeKamarStatus(int $id, string $status): Kamar
    {
        $kamar = Kamar::findOrFail($id);
        $kamar->status_kamar = $status;
        $kamar->save();

        return $kamar;
    }

    /**
     * Menyimpan foto-foto kamar
     *
     * @param Kamar $kamar
     * @param array $photos
     * @param int $primaryPhotoIndex
     * @return void
     */
    // Legacy helper removed: skema sekarang memakai satu field foto_kamar

    /**
     * Mendapatkan statistik kamar
     *
     * @param int|null $kosanId
     * @return array
     */
    public function getKamarStatistics($kosanId = null)
    {
        $query = Kamar::query();

        if ($kosanId) {
            $query->where('kosan_id', $kosanId);
        }

        $stats = [
            'total_kamar' => $query->count(),
            'kamar_tersedia' => (clone $query)->where('status_kamar', 'tersedia')->count(),
            'kamar_terisi' => (clone $query)->where('status_kamar', 'terisi')->count(),
            'kamar_pemeliharaan' => (clone $query)->where('status_kamar', 'maintenance')->count(),
        ];

        return $stats;
    }
}
