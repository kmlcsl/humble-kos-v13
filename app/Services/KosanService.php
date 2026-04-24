<?php

namespace App\Services;

use App\Models\Kosan;
use App\Models\UlasanKosan;
use App\Models\Kamar;
use App\Models\BookingKosan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KosanService
{
    public function getAllKosan(Request $request)
    {
        $query = Kosan::query();

        // Filter hanya kosan yang sudah approved
        $query->where('status_validasi', 'approved');

        // Filter berdasarkan kota
        if ($request->has('kota') && !empty($request->kota)) {
            $query->where('kota', $request->kota);
        }

        // Filter berdasarkan tipe kosan
        if ($request->has('tipe_kosan') && !empty($request->tipe_kosan)) {
            $query->where('tipe_kosan', $request->tipe_kosan);
        }

        // Filter harga berdasarkan kamar terkait
        if ($request->has('harga_min') && !empty($request->harga_min)) {
            $query->whereHas('kamars', function ($q) use ($request) {
                $q->where('harga_per_bulan', '>=', $request->harga_min);
            });
        }
        if ($request->has('harga_max') && !empty($request->harga_max)) {
            $query->whereHas('kamars', function ($q) use ($request) {
                $q->where('harga_per_bulan', '<=', $request->harga_max);
            });
        }

        // Filter berdasarkan fasilitas kamar melalui pivot (nama fasilitas)
        if ($request->has('fasilitas') && is_array($request->fasilitas)) {
            $requested = $request->fasilitas;
            $query->whereHas('kamars.fasilitas', function ($q) use ($requested) {
                $q->whereIn('nama_fasilitas', $requested);
            });
        }

        // Pencarian berdasarkan nama atau alamat
        if ($request->has('keyword') && !empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kosan', 'like', '%' . $request->keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $request->keyword . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->keyword . '%');
            });
        }

        // Urutkan hasil
        if ($request->has('sort') && !empty($request->sort)) {
            switch ($request->sort) {
                case 'rating_tertinggi':
                    $query->orderBy('rating_rata', 'desc');
                    break;
                case 'terbaru':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Eager load relasi yang diperlukan
        $query->with(['pemilik']);

        return $query->paginate(12);
    }

    public function getKosanById($id)
    {
        return Kosan::with(['pemilik', 'kamars.fasilitas', 'ulasanReview'])
            ->where('status_validasi', 'approved')
            ->where('kosan_id', $id)
            ->firstOrFail();
    }

    public function getKosanSerupa(Kosan $kosan)
    {
        return Kosan::where('kosan_id', '!=', $kosan->kosan_id)
            ->where('status_validasi', 'approved')
            ->where(function ($query) use ($kosan) {
                $query->where('kota', $kosan->kota)
                    ->orWhere('tipe_kosan', $kosan->tipe_kosan);
            })
            ->with(['pemilik', 'kamars.fasilitas', 'ulasanReview'])
            ->take(4)
            ->get();
    }

    public function toggleFavorite($kosanId, $userId)
    {
        return [
            'status' => 'unsupported',
            'message' => 'Fitur favorit tidak tersedia pada struktur baru'
        ];
    }

    public function getFavoritesByUser($userId)
    {
        if (!$userId) {
            return collect()->paginate(12);
        }
        return Kosan::where('status_validasi', 'approved')
            ->whereJsonContains('favorit', $userId)
            ->paginate(12);
    }

    public function createBooking($kosanId, $userId, array $data)
    {
        $kosan = Kosan::findOrFail($kosanId);

        // Cast nilai_durasi ke integer untuk memastikan tipe data yang benar
        $nilaiDurasi = (int) $data['nilai_durasi'];
        $jenisDurasi = $data['jenis_durasi'] ?? 'bulanan';
        $jumlahKamar = isset($data['jumlah_kamar']) ? (int) $data['jumlah_kamar'] : 1;

        // Hitung tanggal selesai berdasarkan jenis durasi
        $tanggalMulai = Carbon::parse($data['tanggal_mulai']);
        $bulanDurasi = 0;
        $tanggalSelesai = null;
        if ($jenisDurasi === 'harian') {
            $days = max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addDays($days);
        } elseif ($jenisDurasi === 'mingguan') {
            $weeks = max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addWeeks($weeks);
        } elseif ($jenisDurasi === 'tahunan') {
            $bulanDurasi = 12 * max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addMonths($bulanDurasi);
        } elseif ($jenisDurasi === 'tiga_bulan') {
            $bulanDurasi = 3 * max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addMonths($bulanDurasi);
        } elseif ($jenisDurasi === 'semester') {
            $bulanDurasi = 6 * max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addMonths($bulanDurasi);
        } else {
            $bulanDurasi = max(1, $nilaiDurasi);
            $tanggalSelesai = $tanggalMulai->copy()->addMonths($bulanDurasi);
        }

        $totalHarga = 0;

        // Validasi ketersediaan kamar untuk periode diminta
        $availableCount = Kamar::where('kosan_id', $kosanId)
            ->where('status_kamar', 'tersedia')
            ->whereDoesntHave('bookings', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('status_booking', 'confirmed')
                  ->where(function ($qq) use ($tanggalMulai, $tanggalSelesai) {
                      $qq->where('tanggal_checkin', '<', $tanggalSelesai)
                         ->where('tanggal_checkout', '>', $tanggalMulai);
                  });
            })
            ->count();

        if ($availableCount < $jumlahKamar) {
            throw new \Exception('Kamar tidak tersedia untuk periode yang dipilih. Silakan pilih tanggal lain atau kurangi jumlah kamar.');
        }

        // Jika kamar belum dipilih, pilih satu kamar tersedia otomatis
        if (empty($data['kamar_id'])) {
            $selectedKamar = Kamar::where('kosan_id', $kosanId)
                ->where('status_kamar', 'tersedia')
                ->whereDoesntHave('bookings', function ($q) use ($tanggalMulai, $tanggalSelesai) {
                    $q->where('status_booking', 'confirmed')
                      ->where(function ($qq) use ($tanggalMulai, $tanggalSelesai) {
                          $qq->where('tanggal_checkin', '<', $tanggalSelesai)
                             ->where('tanggal_checkout', '>', $tanggalMulai);
                      });
                })
                ->first();

            if ($selectedKamar) {
                $data['kamar_id'] = $selectedKamar->kamar_id;
            }
        }

        // Hitung total harga berdasarkan harga bulanan/tahunan (prioritas kamar jika ada)
        if (!empty($data['kamar_id'])) {
            $kamar = Kamar::find($data['kamar_id']);
            if ($kamar) {
                $hargaBulanan = (float) ($kamar->harga_per_bulan ?? 0);
                if ($jenisDurasi === 'harian') {
                    $days = max(1, $nilaiDurasi);
                    $totalHarga = ($hargaBulanan / 30.0) * $days * $jumlahKamar;
                } elseif ($jenisDurasi === 'mingguan') {
                    $weeks = max(1, $nilaiDurasi);
                    $totalHarga = ($hargaBulanan / 4.0) * $weeks * $jumlahKamar;
                } else {
                    $totalHarga = $hargaBulanan * max(1, $bulanDurasi) * $jumlahKamar;
                }
            }
        } else {
            $hargaBulananKos = (float) $kosan->getHargaBulananAttribute();
            if ($jenisDurasi === 'harian') {
                $days = max(1, $nilaiDurasi);
                $totalHarga = ($hargaBulananKos / 30.0) * $days * $jumlahKamar;
            } elseif ($jenisDurasi === 'mingguan') {
                $weeks = max(1, $nilaiDurasi);
                $totalHarga = ($hargaBulananKos / 4.0) * $weeks * $jumlahKamar;
            } else {
                $totalHarga = $hargaBulananKos * max(1, $bulanDurasi) * $jumlahKamar;
            }
        }

        // Update user phone number only - DO NOT update email
        if (isset($data['telepon'])) {
            $user = \App\Models\User::find($userId);

            if ($user && !empty($data['telepon'])) {
                $user->no_telepon = $data['telepon'];
                $user->save();
            }
        }

        $bookingData = [
            'user_id' => $userId,
            'kamar_id' => $data['kamar_id'] ?? null,
            'tanggal_checkin' => $tanggalMulai,
            'tanggal_checkout' => $tanggalSelesai,
            'durasi' => $jenisDurasi === 'harian' ? max(1, $nilaiDurasi) : ($jenisDurasi === 'mingguan' ? max(1, $nilaiDurasi * 7) : $bulanDurasi),
            'total_harga' => $totalHarga,
            'status_booking' => 'pending',
            'catatan' => $data['catatan'] ?? null,
            'kode_booking' => 'BK-' . now()->format('YmdHis') . '-' . $userId,
        ];

        $booking = BookingKosan::create($bookingData);

        // audit dihapus

        return $booking;
    }

    public function addReview($kosanId, $userId, array $data)
    {
        // Periksa apakah pengguna sudah pernah booking kamar di kosan ini
        $hasBooking = BookingKosan::where('user_id', $userId)
            ->where('status_booking', 'confirmed')
            ->exists();

        $hasReviewed = UlasanKosan::where('kosan_id', $kosanId)
            ->where('user_id', $userId)
            ->exists();

        if ($hasReviewed) {
            throw new \Exception('Anda sudah pernah mengulas kosan ini');
        }

        // Buat ulasan baru
        return UlasanKosan::create([
            'kosan_id' => $kosanId,
            'user_id' => $userId,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
            'foto_review' => $data['foto_review'] ?? null,
        ]);
    }

    public function searchKosan(Request $request)
    {
        $query = Kosan::query();

        // Filter hanya kosan yang sudah approved
        $query->where('status_validasi', 'approved');

        if ($request->has('keyword') && !empty($request->keyword)) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_kosan', 'like', '%' . $request->keyword . '%')
                    ->orWhere('alamat', 'like', '%' . $request->keyword . '%')
                    ->orWhere('kota', 'like', '%' . $request->keyword . '%')
                    ->orWhere('deskripsi', 'like', '%' . $request->keyword . '%');
            });
        }

        $query->with(['pemilik']);

        return $query->paginate(12);
    }

    public function getNearbyKosan($latitude, $longitude, $radius = 5)
    {
        if (!$latitude || !$longitude) {
            return Kosan::query()
                ->where('status_validasi', 'approved')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }

        $earthRadius = 6371; // Earth's radius in kilometers.

        // Bounding box calculation
        $lat_rad = deg2rad($latitude);
        $lng_rad = deg2rad($longitude);
        
        // Angular radius in radians
        $angular_radius = $radius / $earthRadius;
        
        $lat_min = $latitude - rad2deg($angular_radius);
        $lat_max = $latitude + rad2deg($angular_radius);
        
        // Adjust longitude bounds based on latitude
        $delta_lng = asin(sin($angular_radius) / cos($lat_rad));
        $lng_min = $longitude - rad2deg($delta_lng);
        $lng_max = $longitude + rad2deg($delta_lng);
        
        $query = Kosan::select('*')
            ->selectRaw(
                '(? * ACOS(LEAST(1.0, COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude))))) AS distance',
                [$earthRadius, $latitude, $longitude, $latitude]
            )
            ->where('status_validasi', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            // Apply the bounding box WHERE clause
            ->whereBetween('latitude', [$lat_min, $lat_max])
            ->whereBetween('longitude', [$lng_min, $lng_max])
            // Now, filter the smaller result set by the exact radius
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->take(20); // Increased limit to give more options

        return $query->get();
    }

    public function getAvailability($kosanId, Carbon $start, int $months)
    {
        $end = (clone $start)->addMonths(max(1, $months));
        $availableRooms = Kamar::where('kosan_id', $kosanId)
            ->where('status_kamar', 'tersedia')
            ->whereDoesntHave('bookings', function ($q) use ($start, $end) {
                $q->where('status_booking', 'confirmed')
                  ->where('tanggal_checkin', '<', $end)
                  ->where('tanggal_checkout', '>', $start);
            })
            ->pluck('kamar_id')
            ->toArray();

        return [
            'available_count' => count($availableRooms),
            'available_rooms' => $availableRooms,
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'status' => count($availableRooms) > 0 ? 'tersedia' : 'kosong',
        ];
    }

    public function getAvailabilityRange($kosanId, Carbon $start, Carbon $end)
    {
        // Verify kosan exists
        $kosan = Kosan::find($kosanId);
        if (!$kosan) {
            return [
                'available_count' => 0,
                'available_rooms' => [],
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'status' => 'not_found',
                'message' => 'Kosan tidak ditemukan'
            ];
        }

        $availableRooms = Kamar::where('kosan_id', $kosanId)
            ->where('status_kamar', 'tersedia')
            ->whereDoesntHave('bookings', function ($q) use ($start, $end) {
                $q->where('status_booking', 'confirmed')
                  ->where('tanggal_checkin', '<', $end)
                  ->where('tanggal_checkout', '>', $start);
            })
            ->pluck('kamar_id')
            ->toArray();

        return [
            'available_count' => count($availableRooms),
            'available_rooms' => $availableRooms,
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'status' => count($availableRooms) > 0 ? 'tersedia' : 'kosong',
        ];
    }

    public function getFeaturedKosan($limit = 4)
    {
        return Kosan::query()
            ->where('status_validasi', 'approved')
            ->orderBy('rating_rata', 'desc')
            ->take($limit)
            ->get();
    }

    public function getPopularKosan($limit = 4)
    {
        return Kosan::query()
            ->where('status_validasi', 'approved')
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take($limit)
            ->get();
    }

    public function getNewKosan($limit = 4)
    {
        return Kosan::query()
            ->where('status_validasi', 'approved')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    public function getKosanCities()
    {
        return Kosan::query()
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota')
            ->toArray();
    }

    public function getPriceRange()
    {
        $min = Kamar::min('harga_per_bulan') ?: 0;
        $max = Kamar::max('harga_per_bulan') ?: 0;

        return [
            'min' => floor($min / 100000) * 100000, // Bulatkan ke bawah 100rb terdekat
            'max' => ceil($max / 100000) * 100000,  // Bulatkan ke atas 100rb terdekat
        ];
    }
}
