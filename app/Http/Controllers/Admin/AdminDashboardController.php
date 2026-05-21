<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Kosan;
use App\Models\BookingKosan;
use App\Models\Pembayaran;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Cek apakah user login sebagai admin
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();

        // Double check role
        if ($admin->role !== 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors([
                'message' => 'Access denied. Admin privileges required.'
            ]);
        }

        $stats = $this->getDashboardStats();
        $recentActivities = $this->getRecentActivities();
        $upcomingTasks = $this->getUpcomingTasks();

        return view('admin.dashboard', [
            'admin' => $admin,
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'upcomingTasks' => $upcomingTasks,
        ]);
    }

    /**
     * Get statistics for the dashboard.
     */
    private function getDashboardStats()
    {
        return [
            'total_users' => User::count(),
            'total_kosan' => Kosan::count(),
            'total_bookings' => BookingKosan::count(),
            'total_pembayaran' => Pembayaran::count(),
            'pending_bookings' => BookingKosan::where('status_booking', 'pending')->count(),
            'confirmed_bookings' => BookingKosan::where('status_booking', 'confirmed')->count(),
            'cancelled_bookings' => BookingKosan::where('status_booking', 'cancelled')->count(),
            'pending_pembayaran' => Pembayaran::where('status_pembayaran', 'pending')->count(),
            'total_revenue' => Pembayaran::where('status_pembayaran', 'paid')->sum('jumlah_bayar'),
        ];
    }


    /**
     * Get recent activities for the dashboard feed.
     */
    private function getRecentActivities()
    {
        $recentBookings = BookingKosan::with(['user', 'kosan', 'kamar'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'booking';
                return $item;
            });

        $recentPayments = Pembayaran::with(['booking.user', 'booking.kosan'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'payment';
                return $item;
            });

        return $recentBookings->toBase()->merge($recentPayments->toBase())
            ->sortByDesc('created_at')
            ->take(5);
    }

    /**
     * Get upcoming tasks for the dashboard.
     */
    private function getUpcomingTasks()
    {
        $pendingKosan = Kosan::where('status_validasi', 'pending')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->kosan_id,
                    'type' => 'kosan_verification',
                    'title' => 'Verifikasi Kosan Baru',
                    'description' => $item->nama_kosan . ' menunggu verifikasi.',
                    'created_at' => $item->created_at,
                    'link' => route('admin.manajemen-kosan.update', $item->kosan_id)
                ];
            });

        $pendingPayments = Pembayaran::where('status_pembayaran', 'pending')
            ->with('booking.user')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->pembayaran_id,
                    'type' => 'payment_approval',
                    'title' => 'Approve Pembayaran Pending',
                    'description' => 'Pembayaran Rp ' . number_format((float) $item->jumlah_bayar, 0, ',', '.') . ' dari ' . (optional($item->booking)->user->nama_lengkap ?? optional($item->booking)->user->username ?? 'Pengguna') . ' menunggu persetujuan.',
                    'created_at' => $item->created_at,
                    'link' => route('admin.pembayaran.show', $item->pembayaran_id)
                ];
            });

        return $pendingKosan->toBase()->merge($pendingPayments->toBase())
            ->sortBy('created_at')
            ->take(5);
    }
}
