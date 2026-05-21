<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PemilikPembayaranController extends Controller
{
    /**
     * Get all booking IDs from owner's properties
     */
    private function getOwnedBookingIds()
    {
        $user = Auth::user();
        $kosanIds = Kosan::where('owner_id', $user->user_id)->pluck('kosan_id');
        $kamarIds = Kamar::whereIn('kosan_id', $kosanIds)->pluck('kamar_id');
        return BookingKosan::whereIn('kamar_id', $kamarIds)->pluck('booking_id');
    }

    /**
     * Get payment statistics for the owner
     */
    private function getStats()
    {
        $bookingIds = $this->getOwnedBookingIds();
        $now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        return [
            // Overall Stats
            'total_pembayaran' => Pembayaran::whereIn('booking_id', $bookingIds)->count(),
            'processing_pembayaran' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['pending', 'processing'])
                ->count(),
            'total_processing' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['pending', 'processing'])
                ->count(),
            'successful_pembayaran' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->count(),
            'failed_pembayaran' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['failed', 'cancelled', 'expired'])
                ->count(),
            'total_pendapatan' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->sum('jumlah_bayar'),

            // Today's Stats
            'processing_today' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['pending', 'processing'])
                ->whereDate('created_at', $today)
                ->count(),
            'successful_today' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereDate('updated_at', $today)
                ->count(),
            'failed_today' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['failed', 'cancelled', 'expired'])
                ->whereDate('updated_at', $today)
                ->count(),
            'revenue_today' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereDate('tanggal_bayar', $today)
                ->sum('jumlah_bayar'),

            // Yesterday's Stats
            'processing_yesterday' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['pending', 'processing'])
                ->whereDate('created_at', $yesterday)
                ->count(),
            'successful_yesterday' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereDate('updated_at', $yesterday)
                ->count(),
            'failed_yesterday' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['failed', 'cancelled', 'expired'])
                ->whereDate('updated_at', $yesterday)
                ->count(),
            'revenue_yesterday' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereDate('tanggal_bayar', $yesterday)
                ->sum('jumlah_bayar'),

            // This Month's Stats
            'processing_this_month' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['pending', 'processing'])
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->count(),
            'successful_this_month' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereYear('updated_at', $now->year)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'failed_this_month' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->whereIn('status_pembayaran', ['failed', 'cancelled', 'expired'])
                ->whereYear('updated_at', $now->year)
                ->whereMonth('updated_at', $now->month)
                ->count(),
            'revenue_this_month' => Pembayaran::whereIn('booking_id', $bookingIds)
                ->where('status_pembayaran', 'paid')
                ->whereYear('tanggal_bayar', $now->year)
                ->whereMonth('tanggal_bayar', $now->month)
                ->sum('jumlah_bayar'),
        ];
    }

    /**
     * Display all payments from owner's bookings
     */
    public function index(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $query = Pembayaran::with(['booking.kamar.kosan', 'booking.user'])
            ->whereIn('booking_id', $bookingIds);

        // Filters
        if ($request->has('status') && $request->status != '' && $request->status != 'all') {
            // Map user-friendly status to database status
            $statusMap = [
                'successful' => 'paid',
                'processing' => 'pending',
            ];
            $dbStatus = $statusMap[$request->status] ?? $request->status;
            $query->where('status_pembayaran', $dbStatus);
        }
        if ($request->has('metode') && $request->metode != '') {
            $query->where('metode_pembayaran', $request->metode);
        }
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($request) {
                        $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(15);
        $stats = $this->getStats();

        return view('pemilik.pembayaran.index', [
            'pembayaran' => $pembayaran,
            'stats' => $stats
        ]);
    }

    /**
     * Display single payment details
     */
    public function show(int $id)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $pembayaran = Pembayaran::with(['booking.kamar.kosan', 'booking.user'])
            ->whereIn('booking_id', $bookingIds)
            ->findOrFail($id);

        return view('pemilik.pembayaran.show', [
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Display payments waiting for verification (manual payments)
     */
    public function verifikasi(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $query = Pembayaran::with(['booking.kamar.kosan', 'booking.user'])
            ->whereIn('booking_id', $bookingIds)
            ->where('status_pembayaran', 'pending');

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($request) {
                        $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        $pembayaran = $query->orderBy('created_at', 'asc')->paginate(15);
        $stats = $this->getStats();

        return view('pemilik.pembayaran.verifikasi', [
            'pembayaran' => $pembayaran,
            'stats' => $stats
        ]);
    }

    /**
     * Display successful payments
     */
    public function sukses(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $query = Pembayaran::with(['booking.kamar.kosan', 'booking.user'])
            ->whereIn('booking_id', $bookingIds)
            ->where('status_pembayaran', 'paid');

        if ($request->has('metode') && $request->metode != '' && $request->metode != 'all') {
            $query->where('metode_pembayaran', $request->metode);
        }
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($request) {
                        $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')->paginate(15);
        $stats = $this->getStats();

        return view('pemilik.pembayaran.sukses', [
            'pembayaran' => $pembayaran,
            'stats' => $stats
        ]);
    }

    /**
     * Display expired/failed payments
     */
    public function kadaluarsa(Request $request)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $query = Pembayaran::with(['booking.kamar.kosan', 'booking.user'])
            ->whereIn('booking_id', $bookingIds)
            ->whereIn('status_pembayaran', ['expired', 'failed', 'cancelled']);

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('transaction_id', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('booking', function ($bookingQuery) use ($request) {
                        $bookingQuery->where('kode_booking', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        $pembayaran = $query->orderBy('updated_at', 'desc')->paginate(15);
        $stats = $this->getStats();

        return view('pemilik.pembayaran.kadaluarsa', [
            'pembayaran' => $pembayaran,
            'stats' => $stats
        ]);
    }

    /**
     * Approve a manual payment
     */
    public function approve(int$id)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $pembayaran = Pembayaran::with('booking.kamar')
            ->whereIn('booking_id', $bookingIds)
            ->findOrFail($id);

        $pembayaran->status_pembayaran = 'paid';
        $pembayaran->tanggal_bayar = now();
        $pembayaran->save();

        $booking = $pembayaran->booking;
        if ($booking && $booking->status_booking === 'pending') {
            $booking->status_booking = 'confirmed';
            $booking->save();

            $kamar = $booking->kamar ?? null;
            if ($kamar) {
                $kamar->status_kamar = 'terisi';
                $kamar->save();
            }
        }

        return redirect()->route('pemilik.pembayaran.show', $pembayaran->pembayaran_id)
            ->with('success', 'Pembayaran berhasil disetujui dan booking dikonfirmasi.');
    }

    /**
     * Reject a manual payment
     */
    public function reject(int $id)
    {
        $bookingIds = $this->getOwnedBookingIds();
        $pembayaran = Pembayaran::whereIn('booking_id', $bookingIds)
            ->findOrFail($id);

        $pembayaran->status_pembayaran = 'failed';
        $pembayaran->save();

        return redirect()->route('pemilik.pembayaran.show', $pembayaran->pembayaran_id)
            ->with('success', 'Pembayaran telah ditolak.');
    }
}
