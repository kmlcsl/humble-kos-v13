<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use App\Models\BuktiPembayaran;
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
    protected $pembayaranService;

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

    public function index($bookingId)
    {
        $bookings = BookingKosan::findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }

        // Ambil pembayaran terbaru, tetapi JANGAN membuat otomatis bila belum ada.
        $pembayaran = Pembayaran::where('booking_id', $bookings->booking_id)
            ->latest()
            ->first();

        return view('users.pembayaran.index', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    public function process(Request $request, $bookingId)
    {
        $booking = BookingKosan::findOrFail($bookingId);

        if ($booking->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'metode_pembayaran' => 'required|in:manual,midtrans',
        ]);

        try {
            // Cari pembayaran yang masih aktif atau buat baru
            $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                ->where('status_pembayaran', 'pending')
                ->where(function ($q) use ($request) {
                    if ($request->metode_pembayaran === 'manual') {
                        $q->where('tipe_pembayaran', 'manual');
                    } else {
                        $q->where('tipe_pembayaran', 'gateway')->where('payment_gateway', 'midtrans');
                    }
                })
                ->latest()
                ->first();

            // Jika tidak ada pembayaran aktif atau metode berbeda, buat baru
            if (!$pembayaran) {
                if ($request->metode_pembayaran === 'manual') {
                    $pembayaran = new Pembayaran([
                        'booking_id' => $booking->booking_id,
                        'tipe_pembayaran' => 'manual',
                        'metode_pembayaran' => 'transfer',
                        'jumlah_bayar' => $booking->getCorrectedTotalHargaAttribute(),
                        'transaction_id' => 'PAY-' . strtoupper(Str::random(6)) . '-' . time(),
                        'status_pembayaran' => 'pending',
                    ]);
                } else {
                    $pembayaran = new Pembayaran([
                        'booking_id' => $booking->booking_id,
                        'tipe_pembayaran' => 'gateway',
                        'payment_gateway' => 'midtrans',
                        'metode_pembayaran' => 'e-wallet',
                        'jumlah_bayar' => $booking->getCorrectedTotalHargaAttribute(),
                        'transaction_id' => 'PAY-' . strtoupper(Str::random(6)) . '-' . time(),
                        'status_pembayaran' => 'pending',
                    ]);
                }
                $pembayaran->save();
            }

            if ($request->metode_pembayaran === 'manual') {
                return redirect()->route('users.pembayaran.manual', $booking->booking_id);
            } elseif ($request->metode_pembayaran === 'midtrans') {
                return $this->processMidtransPayment($pembayaran);
            }

            return redirect()->route('users.pembayaran.index', $booking->booking_id)
                ->with('error', 'Metode pembayaran tidak valid.');
        } catch (\Exception $e) {
            return redirect()->route('users.pembayaran.index', $booking->booking_id)
                ->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
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

    public function konfirmasi($bookingId)
    {
        $bookings = BookingKosan::findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }

        // Prioritaskan pembayaran gateway (Midtrans) yang paid
        $pembayaran = Pembayaran::where('booking_id', $bookings->booking_id)
            ->where('tipe_pembayaran', 'gateway')
            ->where('status_pembayaran', 'paid')
            ->latest()
            ->first();

        // Jika tidak ada yang paid, cek yang pending
        if (!$pembayaran) {
            $pembayaran = Pembayaran::where('booking_id', $bookings->booking_id)
                ->where('tipe_pembayaran', 'gateway')
                ->where('status_pembayaran', 'pending')
                ->latest()
                ->first();
        }

        // Jika masih tidak ada, ambil pembayaran manual
        if (!$pembayaran) {
            $pembayaran = Pembayaran::where('booking_id', $bookings->booking_id)
                ->latest()
                ->first();
        }

        if (!$pembayaran) {
            return redirect()->route('users.bookings.show', $bookings->booking_id)
                ->with('error', 'Tidak ada data pembayaran untuk booking ini.');
        }

        return view('users.pembayaran.konfirmasi', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    /**
     * Handle Midtrans callback and auto-confirm payment
     */
    public function callback(Request $request)
    {
        try {
            $notif = $request->all();

            // Validasi signature Midtrans (opsional untuk sandbox)
            $serverKey = config('services.midtrans.serverKey');
            $orderId = $notif['order_id'];
            $statusCode = $notif['status_code'];
            $grossAmount = $notif['gross_amount'];
            $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            // Untuk sandbox, skip signature validation
            if (config('services.midtrans.isProduction') && $signatureKey !== $notif['signature_key']) {
                return response()->json(['status' => 'error'], 400);
            }

            $pembayaran = Pembayaran::where('transaction_id', $orderId)->first();

            if (!$pembayaran) {
                return response()->json(['status' => 'error'], 404);
            }

            // Map Midtrans status to our status
            $transactionStatus = $notif['transaction_status'];
            $fraudStatus = $notif['fraud_status'] ?? '';

            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $pembayaran->status_pembayaran = 'pending';
                } else if ($fraudStatus == 'accept') {
                    $pembayaran->status_pembayaran = 'paid';
                }
            } else if ($transactionStatus == 'settlement') {
                $pembayaran->status_pembayaran = 'paid';
            } else if ($transactionStatus == 'pending') {
                $pembayaran->status_pembayaran = 'pending';
            } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $pembayaran->status_pembayaran = 'failed';
            }

            $pembayaran->no_referensi = $notif['transaction_id'] ?? $notif['order_id'];
            $pembayaran->tanggal_bayar = $pembayaran->status_pembayaran === 'paid' ? now() : $pembayaran->tanggal_bayar;

            $paymentType = $notif['payment_type'] ?? null;
            if ($paymentType) {
                $mapping = [
                    'bank_transfer' => 'transfer',
                    'echannel' => 'transfer',
                    'bca_va' => 'transfer',
                    'bni_va' => 'transfer',
                    'bri_va' => 'transfer',
                    'permata_va' => 'transfer',
                    'other_va' => 'transfer',
                    'qris' => 'qris',
                    'gopay' => 'e-wallet',
                    'shopeepay' => 'e-wallet',
                    'ovo' => 'e-wallet',
                    'dana' => 'e-wallet',
                    'credit_card' => 'kartu_kredit',
                    'cstore' => 'convenience_store',
                    'indomaret' => 'convenience_store',
                    'alfamart' => 'convenience_store',
                ];
                $pembayaran->metode_pembayaran = $mapping[$paymentType] ?? $pembayaran->metode_pembayaran;

                // Simpan payment code untuk convenience store
                if (in_array($paymentType, ['cstore', 'indomaret', 'alfamart']) && isset($notif['payment_code'])) {
                    $pembayaran->no_referensi = $notif['payment_code'];
                }
            }
            $pembayaran->save();

            // Auto-confirm booking jika pembayaran berhasil
            if ($pembayaran->status_pembayaran === 'paid') {
                $booking = $pembayaran->booking;
                if ($booking && $booking->status_booking === 'pending') {
                    $booking->status_booking = 'confirmed';
                    $booking->save();

                    // Update status_kamar to 'terisi'
                    $kamar = Kamar::find($booking->kamar_id);
                    if ($kamar) {
                        $kamar->status_kamar = 'terisi';
                        $kamar->save();
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status (AJAX)
     */
    public function checkStatus($bookingId)
    {
        try {
            $booking = BookingKosan::findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Prioritaskan pembayaran gateway (Midtrans)
            $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                ->where('tipe_pembayaran', 'gateway')
                ->latest()
                ->first();

            // Jika tidak ada gateway, ambil yang manual
            if (!$pembayaran) {
                $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                    ->latest()
                    ->first();
            }

            if (!$pembayaran) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Jika masih pending dan gateway, refresh status dari Midtrans agar lebih cepat
            if ($pembayaran->status_pembayaran === 'pending' && $pembayaran->payment_gateway === 'midtrans' && $pembayaran->transaction_id) {
                try {
                    $midtransStatus = Transaction::status($pembayaran->transaction_id);
                    $transactionStatus = is_array($midtransStatus) ? ($midtransStatus['transaction_status'] ?? null) : ($midtransStatus->transaction_status ?? null);
                    $paymentType = is_array($midtransStatus) ? ($midtransStatus['payment_type'] ?? null) : ($midtransStatus->payment_type ?? null);

                    if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && (is_array($midtransStatus) ? ($midtransStatus['fraud_status'] ?? '') : ($midtransStatus->fraud_status ?? '')) === 'accept')) {
                        $pembayaran->status_pembayaran = 'paid';
                        $pembayaran->tanggal_bayar = now();
                    } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                        $pembayaran->status_pembayaran = 'failed';
                    } else {
                        $pembayaran->status_pembayaran = 'pending';
                    }

                    // Map payment_type ke enum metode_pembayaran
                    if ($paymentType) {
                        $mapping = [
                            'bank_transfer' => 'transfer',
                            'echannel' => 'transfer',
                            'bca_va' => 'transfer',
                            'bni_va' => 'transfer',
                            'bri_va' => 'transfer',
                            'permata_va' => 'transfer',
                            'other_va' => 'transfer',
                            'qris' => 'qris',
                            'gopay' => 'e-wallet',
                            'shopeepay' => 'e-wallet',
                            'ovo' => 'e-wallet',
                            'dana' => 'e-wallet',
                            'credit_card' => 'kartu_kredit',
                            'cstore' => 'convenience_store',
                            'indomaret' => 'convenience_store',
                            'alfamart' => 'convenience_store',
                        ];
                        $pembayaran->metode_pembayaran = $mapping[$paymentType] ?? $pembayaran->metode_pembayaran;

                        // Simpan payment code untuk convenience store
                        if (in_array($paymentType, ['cstore', 'indomaret', 'alfamart'])) {
                            if (is_array($midtransStatus) && isset($midtransStatus['payment_code'])) {
                                $pembayaran->no_referensi = $midtransStatus['payment_code'];
                            } elseif (isset($midtransStatus->payment_code)) {
                                $pembayaran->no_referensi = $midtransStatus->payment_code;
                            }
                        }
                    }
                    $pembayaran->save();
                } catch (\Exception $e) {
                    // Log error tapi lanjutkan
                }
            }

            return response()->json([
                'status_pembayaran' => $pembayaran->status_pembayaran,
                'transaction_id' => $pembayaran->transaction_id,
                'booking_status' => $booking->status_booking,
                'payment_method' => $pembayaran->tipe_pembayaran
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Cancel payment
     */
    public function cancel($bookingId)
    {
        try {
            $booking = BookingKosan::findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return redirect()->route('users.dashboard')->with('error', 'Unauthorized access.');
            }

            $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                ->where('status_pembayaran', 'pending')
                ->first();

            if ($pembayaran) {
                $pembayaran->status_pembayaran = 'failed';
                $pembayaran->save();
            }

            return redirect()->route('users.bookings.show', $booking->booking_id)
                ->with('info', 'Pembayaran dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan pembayaran.');
        }
    }

    /**
     * Redirect by payment code
     */
    public function redirectByCode($transactionId)
    {
        try {
            $pembayaran = Pembayaran::where('transaction_id', $transactionId)->first();

            if (!$pembayaran) {
                return redirect()->route('users.dashboard')->with('error', 'Transaction ID tidak ditemukan.');
            }

            return redirect()->route('users.pembayaran.index', $pembayaran->booking_id);
        } catch (\Exception $e) {
            return redirect()->route('users.dashboard')->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Show manual payment page
     */
    public function manualPayment($bookingId)
    {
        $bookings = BookingKosan::findOrFail($bookingId);

        if ($bookings->user_id != Auth::id()) {
            return redirect()->route('users.dashboard')->with('error', 'Unauthorized access.');
        }

        $pembayaran = Pembayaran::where('booking_id', $bookings->booking_id)
            ->where('tipe_pembayaran', 'manual')
            ->latest()
            ->first();

        if (!$pembayaran) {
            return redirect()->route('users.pembayaran.index', $bookings->booking_id)
                ->with('error', 'Data pembayaran tidak ditemukan.');
        }

        return view('users.pembayaran.manual-payment', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran
        ]);
    }

    /**
     * Upload bukti pembayaran manual
     */
    public function uploadBukti(Request $request, $bookingId)
    {
        $request->validate([
            'nama_pengirim' => 'required|string|max:100',
            'bank_pengirim' => 'required|string|max:50',
            'nomor_rekening_pengirim' => 'required|string|max:30',
            'jumlah_transfer' => 'required|numeric|min:1000',
            'tanggal_transfer' => 'required|date',
            'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            $booking = BookingKosan::findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return redirect()->route('users.dashboard')->with('error', 'Unauthorized access.');
            }

            $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                ->where('tipe_pembayaran', 'manual')
                ->latest()
                ->first();

            if (!$pembayaran) {
                return redirect()->route('users.pembayaran.index', $booking->booking_id)
                    ->with('error', 'Data pembayaran tidak ditemukan.');
            }

            // Upload file
            $file = $request->file('file_bukti');
            $fileName = 'bukti_' . $pembayaran->transaction_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_pembayaran', $fileName, 'public');

            // Simpan bukti pembayaran di kolom pembayaran
            $pembayaran->bukti_transfer = $filePath;
            $pembayaran->keterangan = $request->catatan;
            $pembayaran->tanggal_bayar = null;
            $pembayaran->status_pembayaran = 'pending';
            $pembayaran->save();

            return redirect()->route('users.pembayaran.konfirmasi', $booking->booking_id)
                ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupload bukti pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Update payment status from frontend (AJAX)
     */
    public function updateStatus(Request $request, $bookingId)
    {
        try {
            $booking = BookingKosan::findOrFail($bookingId);

            if ($booking->user_id != Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $pembayaran = Pembayaran::where('booking_id', $booking->booking_id)
                ->where('tipe_pembayaran', 'gateway')
                ->latest()
                ->first();

            if (!$pembayaran) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $request->validate([
                'status' => 'required|in:paid,pending,failed,expired',
                'transaction_id' => 'nullable|string',
                'payment_type' => 'nullable|string'
            ]);

            // Update pembayaran
            $pembayaran->status_pembayaran = $request->status;
            if ($request->transaction_id) {
                $pembayaran->transaction_id = $request->transaction_id;
            }
            if ($request->status === 'paid') {
                $pembayaran->tanggal_bayar = now();
            }

            $paymentType = $request->payment_type;
            if ($paymentType) {
                $mapping = [
                    'bank_transfer' => 'transfer',
                    'echannel' => 'transfer',
                    'bca_va' => 'transfer',
                    'bni_va' => 'transfer',
                    'bri_va' => 'transfer',
                    'permata_va' => 'transfer',
                    'other_va' => 'transfer',
                    'qris' => 'qris',
                    'gopay' => 'e-wallet',
                    'shopeepay' => 'e-wallet',
                    'ovo' => 'e-wallet',
                    'dana' => 'e-wallet',
                    'credit_card' => 'kartu_kredit',
                    'cstore' => 'convenience_store',
                    'indomaret' => 'convenience_store',
                    'alfamart' => 'convenience_store',
                ];
                $pembayaran->metode_pembayaran = $mapping[$paymentType] ?? $pembayaran->metode_pembayaran;
            }
            $pembayaran->save();

            // Auto-confirm booking if paid
            if ($request->status === 'paid') {
                $booking->status_booking = 'confirmed';
                $booking->save();

                $kamar = Kamar::find($booking->kamar_id);
                if ($kamar) {
                    $kamar->status_kamar = 'terisi';
                    $kamar->save();
                }
            }

            return response()->json([
                'success' => true,
                'status_pembayaran' => $pembayaran->status_pembayaran,
                'booking_status' => $booking->status_booking
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
