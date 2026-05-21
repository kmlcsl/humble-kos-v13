<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use App\Models\Kamar;
use App\Services\PembayaranService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranController extends Controller
{
    protected PembayaranService $pembayaranService;

    public function __construct(PembayaranService $pembayaranService)
    {
        $this->pembayaranService = $pembayaranService;

        // Inisialisasi konfigurasi Midtrans dengan pengecekan
        $serverKey = config('services.midtrans.serverKey');
        $clientKey = config('services.midtrans.clientKey');

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = config('services.midtrans.isProduction', false);
        Config::$isSanitized = config('services.midtrans.isSanitized', true);
        Config::$is3ds = config('services.midtrans.is3ds', true);
    }

    public function index(int $bookingId)
    {
        $bookings = BookingKosan::query()->findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }

        // Ambil pembayaran terbaru
        $pembayaran = Pembayaran::query()->where('booking_id', $bookings->booking_id)
            ->latest()
            ->first();

        return view('users.pembayaran.index', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    public function process(Request $request, int $bookingId)
    {
        $booking = BookingKosan::query()->findOrFail($bookingId);

        if ($booking->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'metode_pembayaran' => 'required|in:manual,midtrans',
        ]);

        try {
            // Cari pembayaran pending
            $pembayaran = Pembayaran::query()->where('booking_id', $booking->booking_id)
                ->where('status_pembayaran', Pembayaran::STATUS_PENDING)
                ->where(function ($q) use ($request) {
                    if ($request->input('metode_pembayaran') === 'manual') {
                        $q->where('tipe_pembayaran', 'manual');
                    } else {
                        $q->where('tipe_pembayaran', 'gateway')->where('payment_gateway', 'midtrans');
                    }
                })
                ->latest()
                ->first();

            // Buat pembayaran baru jika tidak ada
            if (!$pembayaran) {
                if ($request->input('metode_pembayaran') === 'manual') {
                    $pembayaran = new Pembayaran([
                        'booking_id' => $booking->booking_id,
                        'tipe_pembayaran' => 'manual',
                        'metode_pembayaran' => 'transfer',
                        'jumlah_bayar' => $booking->getCorrectedTotalHargaAttribute(),
                        'transaction_id' => 'PAY-' . strtoupper(Str::random(6)) . '-' . time(),
                        'status_pembayaran' => Pembayaran::STATUS_PENDING,
                    ]);
                } else {
                    $pembayaran = new Pembayaran([
                        'booking_id' => $booking->booking_id,
                        'tipe_pembayaran' => 'gateway',
                        'payment_gateway' => 'midtrans',
                        'metode_pembayaran' => 'e-wallet',
                        'jumlah_bayar' => $booking->getCorrectedTotalHargaAttribute(),
                        'transaction_id' => 'PAY-' . strtoupper(Str::random(6)) . '-' . time(),
                        'status_pembayaran' => Pembayaran::STATUS_PENDING,
                    ]);
                }
                $pembayaran->save();
            }

            if ($request->input('metode_pembayaran') === 'manual') {
                return redirect()->route('users.pembayaran.manual', $booking->booking_id);
            } elseif ($request->input('metode_pembayaran') === 'midtrans') {
                return $this->processMidtransPayment($pembayaran);
            }

            return redirect()->route('users.pembayaran.index', $booking->booking_id)
                ->with('error', 'Metode tidak valid.');
        } catch (\Exception $e) {
            return redirect()->route('users.pembayaran.index', $booking->booking_id)
                ->with('error', 'Gagal memproses pembayaran.');
        }
    }

    /**
     * Process DANA payment - menggunakan service
     */
    private function processDanaPaymentController(Pembayaran $pembayaran)
    {
        try {
            // Panggil method dari service, bukan dari controller
            $result = $this->pembayaranService->processDanaPayment($pembayaran);

            if ($result['success']) {
                return redirect()->route('users.pembayaran.index', $pembayaran->booking_id)
                    ->with('success', 'Pembayaran DANA berhasil diproses.')
                    ->with('dana_data', [
                        'transaction_id' => $result['transaction_id'],
                        'qr_code' => $result['qr_code'],
                        'expires_at' => $result['expires_at']
                    ]);
            } else {
                return redirect()->route('users.pembayaran.index', $pembayaran->booking_id)
                    ->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('users.pembayaran.index', $pembayaran->booking_id)
                ->with('error', 'Gagal memproses pembayaran DANA: ' . $e->getMessage());
        }
    }

    /**
     * Process Midtrans payment
     */
    private function processMidtransPayment(Pembayaran $pembayaran)
    {
        try {
            // Validasi konfigurasi
            $serverKey = config('services.midtrans.serverKey');
            $clientKey = config('services.midtrans.clientKey');

            if (empty($serverKey) || empty($clientKey)) {
                throw new \Exception('Konfigurasi Midtrans tidak lengkap. Server Key atau Client Key tidak ditemukan.');
            }

            // Set ulang konfigurasi untuk memastikan
            Config::$serverKey = $serverKey;
            Config::$clientKey = $clientKey;
            Config::$isProduction = config('services.midtrans.isProduction', false);
            Config::$isSanitized = config('services.midtrans.isSanitized', true);
            Config::$is3ds = config('services.midtrans.is3ds', true);

            // Tidak menyimpan snap_token ke DB, hanya gunakan sekali pakai via session
            $order = [
                'transaction_details' => [
                    'order_id' => $pembayaran->transaction_id,
                    'gross_amount' => (int) $pembayaran->jumlah_bayar,
                ],
                'enabled_payments' => [
                    'credit_card',
                    'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va', // Virtual Account
                    'gopay',
                    'shopeepay',
                    'qris',
                    'cstore', // Alfamart & Indomaret
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name ?? 'Customer',
                    'last_name' => '',
                    'email' => Auth::user()->email ?? 'customer@example.com',
                    'phone' => Auth::user()->no_hp ?? '081234567890',
                ],
                'item_details' => [
                    [
                        'id' => $pembayaran->booking_id,
                        'price' => (int) $pembayaran->jumlah_bayar,
                        'quantity' => 1,
                        'name' => 'Pembayaran Booking Kos #' . $pembayaran->booking_id,
                    ],
                ],
                // Konfigurasi khusus untuk Convenience Store (Alfamart & Indomaret)
                'custom_field1' => 'Pembayaran Kos',
                'custom_field2' => 'Booking ID: ' . $pembayaran->booking_id,
                'custom_field3' => Auth::user()->name ?? 'Customer',
                'callbacks' => [
                    'finish' => route('users.pembayaran.konfirmasi', $pembayaran->booking_id),
                ],
                'expiry' => [
                    'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                    'unit' => 'day',
                    'duration' => 1,
                ],
            ];

            $snapToken = Snap::getSnapToken($order);

            return redirect()->route('users.pembayaran.index', $pembayaran->booking_id)
                ->with('snap_token', $snapToken)
                ->with('midtrans_client_key', $clientKey)
                ->with('success', 'Pembayaran Midtrans siap diproses.');
        } catch (\Exception $e) {
            // Handle duplicate order_id error
            if (strpos($e->getMessage(), 'order_id has already been taken') !== false) {
                // Generate transaction_id baru
                $newTransactionId = 'PAY-' . strtoupper(Str::random(6)) . '-' . time();
                $pembayaran->transaction_id = $newTransactionId;
                $pembayaran->save();

                // Retry dengan transaction_id baru
                return $this->processMidtransPayment($pembayaran);
            }

            return redirect()->route('users.pembayaran.index', $pembayaran->booking_id)
                ->with('error', 'Gagal memproses pembayaran Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi pembayaran
     */
    public function konfirmasi(int $bookingId)
    {
        $bookings = BookingKosan::query()->findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Akses ditolak.');
        }

        // Cari pembayaran gateway yang sudah lunas
        $pembayaran = Pembayaran::query()->where('booking_id', $bookings->booking_id)
            ->where('tipe_pembayaran', 'gateway')
            ->where('status_pembayaran', Pembayaran::STATUS_PAID)
            ->latest()
            ->first();

        // Jika tidak ada yang lunas, cek yang pending
        if (!$pembayaran) {
            $pembayaran = Pembayaran::query()->where('booking_id', $bookings->booking_id)
                ->where('tipe_pembayaran', 'gateway')
                ->where('status_pembayaran', Pembayaran::STATUS_PENDING)
                ->latest()
                ->first();
        }

        // Jika masih tidak ada, ambil pembayaran manual terbaru
        if (!$pembayaran) {
            $pembayaran = Pembayaran::query()->where('booking_id', $bookings->booking_id)
                ->latest()
                ->first();
        }

        if (!$pembayaran) {
            return redirect()->route('users.bookings.show', $bookings->booking_id)
                ->with('error', 'Data tidak ditemukan.');
        }

        return view('users.pembayaran.konfirmasi', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    /**
     * Callback Midtrans
     */
    public function callback(Request $request)
    {
        try {
            $notif = $request->all();
            $serverKey = config('services.midtrans.serverKey');
            $orderId = $notif['order_id'] ?? null;
            $statusCode = $notif['status_code'] ?? null;
            $grossAmount = $notif['gross_amount'] ?? null;
            $signatureKey = $notif['signature_key'] ?? null;

            if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
                return response()->json(['status' => 'error'], 400);
            }

            $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if ($localSignature !== $signatureKey) {
                return response()->json(['status' => 'error'], 400);
            }

            $pembayaran = Pembayaran::query()->where('transaction_id', $orderId)->first();
            if (!$pembayaran) {
                return response()->json(['status' => 'error'], 404);
            }

            // Map status Midtrans
            $transactionStatus = $notif['transaction_status'];
            $fraudStatus = $notif['fraud_status'] ?? '';

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $pembayaran->status_pembayaran = Pembayaran::STATUS_PENDING;
                } else if ($fraudStatus == 'accept') {
                    $pembayaran->status_pembayaran = Pembayaran::STATUS_PAID;
                }
            } else if ($transactionStatus == 'settlement') {
                $pembayaran->status_pembayaran = Pembayaran::STATUS_PAID;
            } else if ($transactionStatus == 'pending') {
                $pembayaran->status_pembayaran = Pembayaran::STATUS_PENDING;
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $pembayaran->status_pembayaran = Pembayaran::STATUS_FAILED;
            }

            $pembayaran->no_referensi = $notif['transaction_id'] ?? $notif['order_id'];
            if ($pembayaran->status_pembayaran === Pembayaran::STATUS_PAID && !$pembayaran->tanggal_bayar) {
                $pembayaran->tanggal_bayar = now();
            }

            $pembayaran->save();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Cek status pembayaran (AJAX)
     */
    public function checkStatus(int $bookingId)
    {
        try {
            $booking = BookingKosan::query()->findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $pembayaran = Pembayaran::query()->where('booking_id', $booking->booking_id)
                ->latest()
                ->first();

            if (!$pembayaran) {
                return response()->json(['error' => 'Not found'], 404);
            }

            // Sinkronisasi status Midtrans
            if ($pembayaran->tipe_pembayaran === 'gateway' && $pembayaran->payment_gateway === 'midtrans' && $pembayaran->transaction_id) {
                try {
                    $midtransStatus = Transaction::status($pembayaran->transaction_id);
                    $pembayaran->status_pembayaran = $this->pembayaranService->mapPaymentStatus(
                        is_array($midtransStatus) ? ($midtransStatus['transaction_status'] ?? '') : ($midtransStatus->transaction_status ?? '')
                    );
                    
                    if ($pembayaran->status_pembayaran === Pembayaran::STATUS_PAID && !$pembayaran->tanggal_bayar) {
                        $pembayaran->tanggal_bayar = now();
                    }
                    $pembayaran->save();
                } catch (\Exception $e) {}
            }

            return response()->json([
                'status_pembayaran' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->transaction_id,
                'booking_status' => $booking->status_booking
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error'], 500);
        }
    }

    /**
     * Batalkan pembayaran
     */
    public function cancel(int $bookingId)
    {
        try {
            $booking = BookingKosan::query()->findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return redirect()->route('users.dashboard')->with('error', 'Akses ditolak.');
            }

            $pembayaran = Pembayaran::query()->where('booking_id', $booking->booking_id)
                ->where('status_pembayaran', Pembayaran::STATUS_PENDING)
                ->first();

            if ($pembayaran) {
                if ($pembayaran->payment_gateway === 'midtrans' && $pembayaran->transaction_id) {
                    try { Transaction::cancel($pembayaran->transaction_id); } catch (\Exception $e) {}
                }

                $pembayaran->status_pembayaran = Pembayaran::STATUS_FAILED;
                $pembayaran->save();
            }

            return redirect()->route('users.bookings.show', $booking->booking_id)
                ->with('info', 'Pembayaran dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan.');
        }
    }

    /**
     * Redirect berdasarkan kode transaksi
     */
    public function redirectByCode(string $transactionId)
    {
        try {
            $pembayaran = Pembayaran::query()->where('transaction_id', $transactionId)->first();
            if (!$pembayaran) {
                return redirect()->route('users.dashboard')->with('error', 'ID tidak ditemukan.');
            }
            return redirect()->route('users.pembayaran.index', $pembayaran->booking_id);
        } catch (\Exception $e) {
            return redirect()->route('users.dashboard')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Halaman pembayaran manual
     */
    public function manualPayment(int $bookingId)
    {
        $bookings = BookingKosan::query()->findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Akses ditolak.');
        }

        $pembayaran = Pembayaran::query()->where('booking_id', $bookings->booking_id)
            ->where('tipe_pembayaran', 'manual')
            ->latest()
            ->first();

        if (!$pembayaran) {
            return redirect()->route('users.pembayaran.index', $bookings->booking_id)
                ->with('error', 'Data tidak ditemukan.');
        }

        return view('users.pembayaran.manual-payment', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    /**
     * Upload bukti bayar manual
     */
    public function uploadBukti(Request $request, int $bookingId)
    {
        $request->validate([
            'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            $booking = BookingKosan::query()->findOrFail($bookingId);
            if ($booking->user_id != Auth::id()) {
                return redirect()->route('users.dashboard')->with('error', 'Akses ditolak.');
            }

            $pembayaran = Pembayaran::query()->where('booking_id', $booking->booking_id)
                ->where('tipe_pembayaran', 'manual')
                ->latest()
                ->first();

            if (!$pembayaran) {
                return redirect()->route('users.pembayaran.index', $booking->booking_id)->with('error', 'Data tidak ditemukan.');
            }

            $path = $request->file('file_bukti')->store('bukti_pembayaran', 'public');

            $pembayaran->bukti_transfer = $path;
            $pembayaran->status_pembayaran = Pembayaran::STATUS_PENDING;
            $pembayaran->save();

            return redirect()->route('users.pembayaran.konfirmasi', $booking->booking_id)
                ->with('success', 'Bukti berhasil diupload.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal upload: ' . $e->getMessage());
        }
    }

    /**
     * Update status pembayaran
     */
    public function updateStatus(Request $request, int $bookingId)
    {
        return $this->checkStatus($bookingId);
    }
}
