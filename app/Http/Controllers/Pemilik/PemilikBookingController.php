<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\Kamar;
use App\Models\BookingKosan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemilikBookingController extends Controller
{
    /**
     * Get all room IDs owned by this user
     */
    private function getOwnedKamarIds()
    {
        $user = Auth::user();

        $kosanIds = Kosan::where('owner_id', $user->user_id)->pluck('kosan_id');

        return Kamar::whereIn('kosan_id', $kosanIds)->pluck('kamar_id');
    }

    /**
     * Display all bookings from owner's properties
     */
    public function index(Request $request)
    {
        $kamarIds = $this->getOwnedKamarIds();

        $bookingsQuery = BookingKosan::with(['user', 'kosan', 'kamar'])->orderBy('booking_id', 'asc');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $bookingsQuery->where('status_booking', $request->input('status'));
        }

        if ($request->filled('nomor_kamar')) {
            $bookingsQuery->whereHas('kamar', function ($q) use ($request) {
                $q->where('nomor_kamar', $request->input('nomor_kamar'));
            });
        }

        // $bookings = $bookingsQuery->orderBy('created_at', 'desc')->paginate(15);
        $bookings = $bookingsQuery->paginate(20);

        // Statistics
        $totalBooking = BookingKosan::whereIn('kamar_id', $kamarIds)->count();
        $pendingCount = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'pending')->count();
        $confirmedCount = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'confirmed')->count();
        $cancelledCount = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'cancelled')->count();

        // Get kosan list for filter
        $user = Auth::user();
        $kosans = Kosan::where('owner_id', $user->user_id)->get();

        $bookingKamars = Kamar::select('nomor_kamar')->distinct()->orderBy('nomor_kamar')->pluck('nomor_kamar');
        $statusOptions = BookingKosan::select('status_booking')->distinct()->orderBy('status_booking')->pluck('status_booking');

        return view('pemilik.bookings.index', [
            'bookings' => $bookings,
            'totalBooking' => $totalBooking,
            'pendingCount' => $pendingCount,
            'confirmedCount' => $confirmedCount,
            'cancelledCount' => $cancelledCount,
            'kosans' => $kosans,
            'bookingKamars' => $bookingKamars,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Display single booking details
     */
    public function show($id)
    {
        $kamarIds = $this->getOwnedKamarIds();

        $bookings = BookingKosan::with(['user', 'kamar.kosan', 'kamar.kosan.pemilik'])
            ->whereIn('kamar_id', $kamarIds)
            ->findOrFail($id);

        // Get payment info
        $pembayaran = $bookings->pembayaran ?? null;

        return view('pemilik.bookings.show', [
            'bookings' => $bookings,
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Display pending bookings (menunggu pembayaran)
     */
    public function pending(Request $request)
    {
        $kamarIds = $this->getOwnedKamarIds();

        $query = BookingKosan::with(['user', 'kamar.kosan'])
            ->whereIn('kamar_id', $kamarIds)
            ->where('status_booking', 'pending');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('kode_booking', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('nama_lengkap', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalCounts = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'pending')->count();

        return view('pemilik.bookings.pending', [
            'bookings' => $bookings,
            'totalCounts' => $totalCounts,
        ]);
    }

    /**
     * Display confirmed bookings (sudah dikonfirmasi/dibayar)
     */
    public function confirmed(Request $request)
    {
        $kamarIds = $this->getOwnedKamarIds();

        $query = BookingKosan::with(['user', 'kamar.kosan'])
            ->whereIn('kamar_id', $kamarIds)
            ->where('status_booking', 'confirmed');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('kode_booking', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('nama_lengkap', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $bookings = $query->orderBy('tanggal_checkin', 'desc')->paginate(15);

        $totalCounts = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'confirmed')->count();

        return view('pemilik.bookings.confirmed', [
            'bookings' => $bookings,
            'totalCounts' => $totalCounts,
        ]);
    }

    /**
     * Display cancelled bookings
     */
    public function cancelled(Request $request)
    {
        $kamarIds = $this->getOwnedKamarIds();

        $query = BookingKosan::with(['user', 'kamar.kosan'])
            ->whereIn('kamar_id', $kamarIds)
            ->where('status_booking', 'cancelled');

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('kode_booking', 'LIKE', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('nama_lengkap', 'LIKE', '%' . $request->search . '%');
                  });
            });
        }

        $bookings = $query->orderBy('updated_at', 'desc')->paginate(15);

        $totalCounts = BookingKosan::whereIn('kamar_id', $kamarIds)->where('status_booking', 'cancelled')->count();

        return view('pemilik.bookings.cancelled', [
            'bookings' => $bookings,
            'totalCounts' => $totalCounts,
        ]);
    }
}
