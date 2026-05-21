<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\BookingKosan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranService
{
    /**
     * Memproses pembayaran DANA (Simulasi)
     */
    public function processDanaPayment(Pembayaran $pembayaran): array
    {
        try {
            // Mock API response
            $mockResponse = [
                'transaction_id' => 'DANA' . rand(1000000, 9999999),
                'status' => Pembayaran::STATUS_PENDING,
                'qr_code' => 'dana_qr_' . ($pembayaran->transaction_id ?? time()),
                'expires_at' => Carbon::now()->addHours(24)->toIso8601String(),
            ];

            // Update data pembayaran
            $pembayaran->status_pembayaran = Pembayaran::STATUS_PENDING;
            $pembayaran->save();

            return [
                'success' => true,
                'transaction_id' => $mockResponse['transaction_id'],
                'qr_code' => $mockResponse['qr_code'],
                'expires_at' => $mockResponse['expires_at'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memproses pembayaran DANA: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Memeriksa status pembayaran
     */
    public function checkPaymentStatus(Pembayaran $pembayaran): array
    {
        try {
            // Jika ada logika kadaluarsa (misal 24 jam dari created_at)
            if ($pembayaran->created_at->addHours(24)->isPast()) {
                if ($pembayaran->status_pembayaran === Pembayaran::STATUS_PENDING) {
                    $pembayaran->status_pembayaran = Pembayaran::STATUS_EXPIRED;
                    $pembayaran->save();
                }
            }

            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->transaction_id,
            ];
        } catch (\Exception $e) {
            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->transaction_id,
            ];
        }
    }

    /**
     * Menyelesaikan pembayaran dan update status booking
     */
    public function completePayment(Pembayaran $pembayaran): array
    {
        try {
            if ($pembayaran->status_pembayaran !== Pembayaran::STATUS_PAID) {
                return [
                    'success' => false,
                    'message' => 'Pembayaran belum berhasil'
                ];
            }

            // Update status booking (dilakukan juga di Observer, tapi ini untuk kepastian)
            $booking = BookingKosan::query()->find($pembayaran->booking_id);
            if ($booking && $booking->status_booking === 'pending') {
                $booking->status_booking = 'confirmed';
                $booking->save();
            }

            return [
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menyelesaikan pembayaran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Membatalkan pembayaran
     */
    public function cancelPayment(Pembayaran $pembayaran): array
    {
        try {
            if ($pembayaran->status_pembayaran !== Pembayaran::STATUS_PENDING) {
                return [
                    'success' => false,
                    'message' => 'Pembayaran tidak dapat dibatalkan dengan status saat ini'
                ];
            }

            $pembayaran->status_pembayaran = Pembayaran::STATUS_FAILED;
            $pembayaran->save();

            return [
                'success' => true,
                'message' => 'Pembayaran berhasil dibatalkan'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal membatalkan pembayaran: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Map status dari provider ke status internal
     */
    public function mapPaymentStatus(string $providerStatus): string
    {
        $statusMap = [
            'pending' => Pembayaran::STATUS_PENDING,
            'settlement' => Pembayaran::STATUS_PAID,
            'capture' => Pembayaran::STATUS_PAID,
            'deny' => Pembayaran::STATUS_FAILED,
            'cancel' => Pembayaran::STATUS_FAILED,
            'expire' => Pembayaran::STATUS_EXPIRED,
            'failure' => Pembayaran::STATUS_FAILED,
            'success' => Pembayaran::STATUS_PAID,
            'paid' => Pembayaran::STATUS_PAID,
        ];

        return $statusMap[strtolower($providerStatus)] ?? Pembayaran::STATUS_PENDING;
    }
}
