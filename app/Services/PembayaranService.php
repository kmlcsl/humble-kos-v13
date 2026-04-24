<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\BookingKosan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranService
{
    /**
     * Process DANA payment
     */
    public function processDanaPayment(Pembayaran $pembayaran)
    {
        try {
            // Mock API response untuk development/testing
            $mockResponse = [
                'transaction_id' => 'DANA' . rand(1000000, 9999999),
                'status' => 'pending',
                'payment_url' => route('users.pembayaran.dana.payment', ['code' => $pembayaran->kode_pembayaran]),
                'qr_code' => 'dana_qr_' . $pembayaran->kode_pembayaran,
                'expires_at' => Carbon::now()->addHours(24)->toIso8601String(),
            ];

            // Update payment with transaction details
            $pembayaran->no_referensi = $mockResponse['transaction_id'];
            $pembayaran->status_pembayaran = 'processing';
            $pembayaran->save();

            return [
                'success' => true,
                'transaction_id' => $mockResponse['transaction_id'],
                'payment_url' => $mockResponse['payment_url'],
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
     * Validate payment callback
     */
    public function validateCallback(Request $request)
    {
        try {
            // Validasi signature atau authentication header jika diperlukan
            // $isValidSignature = $this->validateSignature($request);

            $kode_pembayaran = $request->input('order_id') ?? $request->input('kode_pembayaran');
            $status = $request->input('transaction_status') ?? $request->input('status');
            $reference = $request->input('transaction_id') ?? $request->input('reference');

            // Map status dari provider ke status internal
            $mappedStatus = $this->mapPaymentStatus($status);

            return [
                'success' => true,
                'kode_pembayaran' => $kode_pembayaran,
                'status' => $mappedStatus,
                'no_referensi' => $reference,
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Validasi callback gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus(Pembayaran $pembayaran)
    {
        try {
            // Periksa apakah pembayaran sudah kadaluarsa
            if ($pembayaran->waktu_kadaluarsa && Carbon::now()->isAfter($pembayaran->waktu_kadaluarsa)) {
                if ($pembayaran->status_pembayaran !== 'expired' && !in_array($pembayaran->status_pembayaran, ['successful', 'failed'])) {
                    $pembayaran->status_pembayaran = 'expired';
                    $pembayaran->save();
                }

                return [
                    'status' => 'expired',
                    'transaction_id' => $pembayaran->no_referensi,
                ];
            }

            // Untuk simulasi, kembalikan status saat ini
            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->no_referensi,
            ];
        } catch (\Exception $e) {
            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->no_referensi,
            ];
        }
    }

    /**
     * Complete payment and update booking status
     */
    public function completePayment(Pembayaran $pembayaran)
    {
        try {
            if ($pembayaran->status_pembayaran !== 'successful') {
                return [
                    'success' => false,
                    'message' => 'Pembayaran belum berhasil'
                ];
            }

            // Update booking status
            $booking = BookingKosan::find($pembayaran->booking_id);
            if ($booking) {
                $booking->status_booking = 'dikonfirmasi';
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
     * Cancel payment
     */
    public function cancelPayment(Pembayaran $pembayaran)
    {
        try {
            if (!in_array($pembayaran->status_pembayaran, ['pending', 'processing'])) {
                return [
                    'success' => false,
                    'message' => 'Pembayaran tidak dapat dibatalkan dengan status saat ini'
                ];
            }

            $pembayaran->status_pembayaran = 'failed';
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
     * Map payment status from payment provider to internal status
     */
    private function mapPaymentStatus($providerStatus)
    {
        $statusMap = [
            // Midtrans status mapping
            'pending' => 'pending',
            'settlement' => 'successful',
            'capture' => 'successful',
            'deny' => 'failed',
            'cancel' => 'failed',
            'expire' => 'expired',
            'failure' => 'failed',

            // DANA status mapping (example)
            'PENDING' => 'pending',
            'SUCCESS' => 'successful',
            'FAILED' => 'failed',
            'EXPIRED' => 'expired',

            // Generic mapping
            'successful' => 'successful',
            'processing' => 'processing',
            'failed' => 'failed',
        ];

        return $statusMap[strtolower($providerStatus)] ?? 'pending';
    }

    /**
     * Validate signature for security (implement based on payment provider requirements)
     */
    private function validateSignature(Request $request)
    {
        // Implement signature validation based on payment provider
        // For example, Midtrans uses server key for signature validation

        return true; // Simplified for demo
    }

    /**
     * Check DANA payment status via API (implement when using real DANA API)
     */
    private function checkDanaPaymentStatus(Pembayaran $pembayaran)
    {
        try {
            // $response = Http::timeout(30)->get('https://api.dana.id/v1/payments/' . $pembayaran->no_referensi);
            // return $response->json();

            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->no_referensi,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check Midtrans payment status via API
     */
    private function checkMidtransPaymentStatus(Pembayaran $pembayaran)
    {
        try {
            return [
                'status' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->no_referensi,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
