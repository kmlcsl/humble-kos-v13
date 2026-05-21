<?php

namespace App\Services;

use App\Models\BookingKosan;
use App\Models\Kosan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(array $data)
    {
        DB::beginTransaction();

        try {
            $kosan = Kosan::find($data['id_kosan']);

            if (!$kosan) {
                return [
                    'success' => false,
                    'message' => 'Kosan tidak ditemukan.'
                ];
            }

            // Pastikan jumlah_kamar ada dalam data, default ke 1 jika tidak ada
            $jumlahKamar = isset($data['jumlah_kamar']) ? (int) $data['jumlah_kamar'] : 1;

            if ($kosan->kamar_tersedia < $jumlahKamar) {
                return [
                    'success' => false,
                    'message' => 'Jumlah kamar yang tersedia tidak mencukupi.'
                ];
            }

            // Cast nilai_durasi ke integer
            $nilaiDurasi = (int) $data['nilai_durasi'];

            $totalPrice = $this->calculateTotalPrice($kosan, $data['jenis_durasi'], $jumlahKamar);

            $startDate = Carbon::parse($data['tanggal_mulai']);
            $endDate = $this->calculateEndDate($startDate, $data['jenis_durasi'], $nilaiDurasi);

            $booking = new BookingKosan();
            $booking->user_id = $data['user_id'];
            // simpan relasi kamar, akses kosan lewat $booking->kamar->kosan
            $booking->kamar_id = $data['id_kamar'] ?? null;
            $booking->tanggal_checkin = Carbon::parse($data['tanggal_mulai']);
            $booking->tanggal_checkout = Carbon::parse($endDate);
            $booking->durasi = $nilaiDurasi;
            $booking->total_harga = (float) $totalPrice;
            $booking->status_booking = 'pending';
            $booking->kode_booking = 'BK-' . now()->format('YmdHis') . '-' . $data['user_id'];
            if (isset($data['catatan'])) {
                $booking->catatan = $data['catatan'];
            }

            // Update user's phone number if provided
            if (isset($data['telepon']) && !empty($data['telepon'])) {
                $user = \App\Models\User::find($data['user_id']);

                if ($user) {
                    $user->no_hp = $data['telepon'];
                    $user->save();
                }
            }

            $booking->save();

            $kosan->kamar_tersedia -= $jumlahKamar;
            $kosan->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Booking berhasil dibuat.',
                'booking' => $booking
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.'
            ];
        }
    }

    /**
     * @param int $bookingId
     * @param int $userId
     * @param string $durationType
     * @param int|string $durationValue
     */
    public function extendBooking($bookingId, $userId, $durationType, $durationValue)
    {
        DB::beginTransaction();

        try {
            $booking = BookingKosan::where('booking_id', $bookingId)
                ->where('user_id', $userId)
                ->first();

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking tidak ditemukan.'
                ];
            }

            if ($booking->status_booking !== 'confirmed') {
                return [
                    'success' => false,
                    'message' => 'Hanya booking yang dikonfirmasi yang dapat diperpanjang.'
                ];
            }

            $kosan = Kosan::find($booking->kosan_id);
            if (!$kosan) {
                return [
                    'success' => false,
                    'message' => 'Kosan tidak ditemukan.'
                ];
            }

            // Cast durationValue ke integer
            $durationValue = (int) $durationValue;

            $additionalPrice = $this->calculateTotalPrice($kosan, $durationType, 1, $durationValue);

            $currentEndDate = Carbon::parse($booking->tanggal_checkout);
            $newEndDate = $this->calculateEndDate($currentEndDate, $durationType, $durationValue);
            $booking->tanggal_checkout = $newEndDate;
            $booking->total_harga = (float) $booking->total_harga + (float) $additionalPrice;
            $booking->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Booking berhasil diperpanjang.',
                'booking' => $booking
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperpanjang booking. Silakan coba lagi.'
            ];
        }
    }

    /**
     * @param int $bookingId
     * @param int $userId
     */
    public function cancelBooking($bookingId, $userId)
    {
        DB::beginTransaction();

        try {
            $booking = BookingKosan::where('booking_id', $bookingId)
                ->where('user_id', $userId)
                ->first();

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking tidak ditemukan.'
                ];
            }

            if (!in_array($booking->status_booking, ['pending','confirmed'])) {
                return [
                    'success' => false,
                    'message' => 'Booking tidak dapat dibatalkan dengan status saat ini.'
                ];
            }

            $booking->status_booking = 'cancelled';
            $booking->save();

            $kosan = Kosan::find($booking->kosan_id);
            if ($kosan) {
                $kosan->kamar_tersedia += 1;
                $kosan->save();
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Booking berhasil dibatalkan.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan booking. Silakan coba lagi.'
            ];
        }
    }

    /**
     * @param \App\Models\Kosan $kosan
     * @param string $durationType
     * @param int|string $roomCount
     * @param int|string $durationValue
     */
    protected function calculateTotalPrice(Kosan $kosan, $durationType, $roomCount, $durationValue = 1)
    {
        $monthly = (float) $kosan->getHargaSetelahDiskonAttribute();
        $yearly  = (float) ($kosan->harga_tahunan ?? ($monthly * 12));

        $base = 0.0;

        switch ($durationType) {
            case 'harian':
                $days = max(1, (int) $durationValue);
                $base = ($monthly / 30.0) * $days;
                break;
            case 'mingguan':
                $weeks = max(1, (int) $durationValue);
                $base = ($monthly / 4.0) * $weeks;
                break;
            case 'bulanan':
                $base = $monthly * max(1, (int) $durationValue);
                break;
            case 'tiga_bulan':
                $base = (float) ($kosan->harga_tiga_bulan ?? ($monthly * 3));
                break;
            case 'semester':
                $base = (float) ($kosan->harga_semester ?? ($monthly * 6));
                break;
            case 'tahunan':
                // durationValue untuk tahunan diasumsikan jumlah tahun
                $years = max(1, (int) $durationValue);
                $base = $yearly * $years;
                break;
            default:
                $base = $monthly * max(1, (int) $durationValue);
        }

        return (float) ($base * max(1, (int) $roomCount));
    }

    /**
     * @param \Carbon\Carbon $startDate
     * @param string $durationType
     * @param int|string $durationValue
     */
    protected function calculateEndDate(Carbon $startDate, $durationType, $durationValue)
    {
        $endDate = clone $startDate;

        // Cast durationValue ke integer untuk memastikan tipe data yang benar
        $durationValue = (int) $durationValue;

        switch ($durationType) {
            case 'bulanan':
                $endDate->addMonths($durationValue);
                break;
            case 'tiga_bulan':
                // Nilai durationValue sudah berisi 3 dari data-value di form
                $endDate->addMonths($durationValue);
                break;
            case 'semester':
                // Nilai durationValue sudah berisi 6 dari data-value di form
                $endDate->addMonths($durationValue);
                break;
            case 'tahunan':
                // Nilai durationValue sudah berisi 12 dari data-value di form
                $endDate->addMonths($durationValue);
                break;
            default:
                $endDate->addMonths($durationValue);
        }

        return $endDate->format('Y-m-d');
    }
}
