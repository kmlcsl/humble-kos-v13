<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingKosan;
use App\Models\Pembayaran;
use App\Models\Kosan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminLaporanController extends Controller
{
    /**
     * Display admin reports dashboard.
     */
    public function index()
    {
        // Summary Statistics
        $totalBookings = BookingKosan::count();
        $pendingBookings = BookingKosan::where('status_booking', 'pending')->count();
        $confirmedBookings = BookingKosan::where('status_booking', 'confirmed')->count();
        $cancelledBookings = BookingKosan::where('status_booking', 'cancelled')->count();

        $totalRevenue = Pembayaran::where('status_pembayaran', 'paid')->sum('jumlah_bayar');
        $pendingPayments = Pembayaran::where('status_pembayaran', 'pending')->count();

        $totalKosan = Kosan::count();
        $totalUsers = User::count();

        // Recent Bookings
        $recentBookings = BookingKosan::with(['user', 'kosan', 'kamar'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly Revenue (last 6 months)
        $monthlyRevenue = Pembayaran::where('status_pembayaran', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('admin.laporan.index', [
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'confirmedBookings' => $confirmedBookings,
            'cancelledBookings' => $cancelledBookings,
            'totalRevenue' => $totalRevenue,
            'pendingPayments' => $pendingPayments,
            'totalKosan' => $totalKosan,
            'totalUsers' => $totalUsers,
            'recentBookings' => $recentBookings,
            'monthlyRevenue' => $monthlyRevenue,
        ]);
    }
}
