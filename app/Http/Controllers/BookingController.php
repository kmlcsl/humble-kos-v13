<?php

namespace App\Http\Controllers;

use App\Models\BookingKosan;
use App\Models\Kosan;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index()
    {
        $bookings = BookingKosan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.bookings.index', [
            'bookings' => $bookings
        ]);
    }

    public function show($id)
    {
        $bookings = BookingKosan::where('booking_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('users.bookings.show', [
            'bookings' => $bookings
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $booking = BookingKosan::where('booking_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_booking !== 'pending' && $booking->status_booking !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking tidak dapat dibatalkan dengan status saat ini.');
        }

        $booking->status_booking = 'cancelled';
        $booking->save();

        // audit dihapus

        // Increase available rooms in the kosan
        $kosan = $booking->kamar ? $booking->kamar->kosan : null;
        if ($kosan) {
            // Jika kolom kamar_tersedia ada di tabel kosan
            if (isset($kosan->kamar_tersedia)) {
                $kosan->kamar_tersedia += 1;
                $kosan->save();
            }
        }

        return redirect()->route('users.bookings.index')->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Fungsi ini mengarahkan pengguna ke halaman pembayaran untuk booking tertentu.
     * Tidak lagi langsung mengkonfirmasi pembayaran.
     */
    public function processPayment(Request $request, $id)
    {
        $booking = BookingKosan::where('booking_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_booking !== 'pending') {
            return redirect()->back()->with('error', 'Booking tidak dapat diproses dengan status saat ini.');
        }

        // Arahkan pengguna ke halaman pembayaran dengan booking ID
        return redirect()->route('users.pembayaran.index', $booking->booking_id);
    }

    public function complete($id)
    {
        $booking = BookingKosan::where('booking_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_booking !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking tidak dapat diselesaikan dengan status saat ini.');
        }

        $endDate = Carbon::parse($booking->tanggal_checkout);
        if ($endDate->isFuture()) {
            return redirect()->back()->with('error', 'Booking belum melewati tanggal selesai.');
        }

        $booking->status_booking = 'selesai';
        $booking->save();

        // audit dihapus

        return redirect()->route('users.bookings.index')->with('success', 'Booking telah diselesaikan.');
    }

    public function extendForm($id)
    {
        $booking = BookingKosan::where('booking_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_booking !== 'confirmed') {
            return redirect()->back()->with('error', 'Hanya booking yang dikonfirmasi yang dapat diperpanjang.');
        }

        return view('users.bookings.extend', [
            'booking' => $booking
        ]);
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'jenis_durasi' => 'required|in:bulanan,tiga_bulan,semester,tahunan',
            'nilai_durasi' => 'required|integer|min:1',
        ], [
            'jenis_durasi.required' => 'Jenis durasi harus dipilih.',
            'jenis_durasi.in' => 'Jenis durasi tidak valid.',
            'nilai_durasi.required' => 'Nilai durasi harus diisi.',
            'nilai_durasi.integer' => 'Nilai durasi harus berupa angka.',
            'nilai_durasi.min' => 'Nilai durasi minimal 1.',
        ]);

        try {
            $result = $this->bookingService->extendBooking($id, Auth::id(), $request->jenis_durasi, $request->nilai_durasi);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return redirect()->route('users.bookings.show', $id)->with('success', 'Booking berhasil diperpanjang.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperpanjang booking: ' . $e->getMessage());
        }
    }
}
