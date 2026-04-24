<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Kamar;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    /**
     * Display a listing of all bookings.
     */
    public function index(Request $request)
    {
        $bookingsQuery = BookingKosan::with(['user', 'kosan', 'kamar'])->orderBy('booking_id', 'asc');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $bookingsQuery->where('status_booking', $request->input('status'));
        }

        if ($request->filled('nomor_kamar')) {
            $bookingsQuery->whereHas('kamar', function ($q) use ($request) {
                $q->where('nomor_kamar', $request->input('nomor_kamar'));
            });
        }

        $bookings = $bookingsQuery->paginate(20);

        $bookingKamars = Kamar::select('nomor_kamar')->distinct()->orderBy('nomor_kamar')->pluck('nomor_kamar');
        $statusOptions = BookingKosan::select('status_booking')->distinct()->orderBy('status_booking')->pluck('status_booking');

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'bookingKamars' => $bookingKamars,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Display pending bookings.
     */
    public function pending()
    {
        $bookings = BookingKosan::with(['user', 'kosan', 'kamar'])
            ->where('status_booking', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.pending', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Display confirmed bookings.
     */
    public function confirmed()
    {
        $bookings = BookingKosan::with(['user', 'kosan', 'kamar'])
            ->where('status_booking', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.confirmed', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Display cancelled bookings.
     */
    public function cancelled()
    {
        $bookings = BookingKosan::with(['user', 'kosan', 'kamar'])
            ->where('status_booking', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.cancelled', [
            'bookings' => $bookings,
        ]);
    }
}
