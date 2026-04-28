<?php

namespace App\Http\Controllers;

use App\Models\Kosan;
use App\Models\BookingKosan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller 
{
    public function index()
    {
        $user = Auth::user();

        // Redirect admin to admin dashboard
        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Redirect pemilik_kos to pemilik dashboard
        if ($user && $user->role === 'pemilik_kos') {
            return redirect()->route('pemilik.dashboard');
        }

        // Defaults for guests
        $wishlist_count = 0;
        $active_booking_count = 0;
        $pending_booking_count = 0;
        $notification_count = 0;
        $recent_bookings = collect();
        $favorite_kosan_ids = [];

        if ($user) {
            $wishlist_count = Kosan::whereJsonContains('favorit', $user->user_id)->count();
            $favorite_kosan_ids = Kosan::whereJsonContains('favorit', $user->user_id)->pluck('kosan_id')->toArray();

            $active_booking_count = BookingKosan::where('user_id', $user->user_id)
                ->where('status_booking', 'confirmed')
                ->count();

            $pending_booking_count = BookingKosan::where('user_id', $user->user_id)
                ->where('status_booking', 'pending')
                ->count();

            $notification_count = \App\Models\Notifikasi::where('user_id', $user->user_id)
                ->where('is_read', false)
                ->count();

            $recent_bookings = BookingKosan::where('user_id', $user->user_id)
                ->with(['kamar.kosan', 'kamar'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            //
        }

        $recommended_kosans = $this->getRecommendedKosans($user ? $user->user_id : null);

        $nearby_kosans = [];
        $latitude = request()->session()->get('user_latitude');
        $longitude = request()->session()->get('user_longitude');

        if ($latitude && $longitude) {
            $nearby_kosans = $this->getNearbyKosans($latitude, $longitude, 5); // 5km radius
        }

        return view('users.dashboard', [
            'wishlist_count' => $wishlist_count,
            'active_booking_count' => $active_booking_count,
            'pending_booking_count' => $pending_booking_count,
            'notification_count' => $notification_count,
            'recent_bookings' => $recent_bookings,
            'recommended_kosans' => $recommended_kosans,
            'nearby_kosans' => $nearby_kosans,
            'favorite_kosan_ids' => $favorite_kosan_ids,
        ]);
    }

    private function getRecommendedKosans($userId)
    {
        // 1. Ambil kosan unggulan yang diatur oleh admin (berdasarkan rating tertinggi)
        $featuredKosans = Kosan::query()
            ->with(['kamars.fasilitas'])
            ->where('status_validasi', 'approved')
            ->orderBy('rating_rata', 'desc')
            ->limit(4);

        // 2. Ambil kosan terbaru
        $newKosans = Kosan::query()
            ->with(['kamars.fasilitas'])
            ->where('status_validasi', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(4);

        // 3. Dapatkan data kota dari kosan yang pernah di-bookmark atau di-booking oleh user
        $preferredCities = [];

        $cityBasedKosans = collect();
        if (!empty($preferredCities)) {
            $cityBasedKosans = Kosan::query()
                ->with(['kamars.fasilitas'])
                ->where('status_validasi', 'approved')
                ->whereIn('kota', $preferredCities)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        }

        // 4. Ambil kosan dengan rating tertinggi berdasarkan field rating_rata pada kosan
        $highRatedKosans = Kosan::query()
            ->with(['kamars.fasilitas'])
            ->where('status_validasi', 'approved')
            ->orderBy('rating_rata', 'desc')
            ->limit(4)
            ->get();

        $allRecommended = $featuredKosans->get()
            ->merge($newKosans->get())
            ->merge($cityBasedKosans);

        $allRecommended = $allRecommended->merge($highRatedKosans);

        $allRecommended = $allRecommended
            ->unique('kosan_id')
            ->take(8);

        foreach ($allRecommended as $kosan) {
            $kosan->avgRating = $kosan->rating_rata ?? 0;
        }

        return $allRecommended;
    }

    private function getNearbyKosans($latitude, $longitude, $radius = 5)
    {
        if (!$latitude || !$longitude) {
            return collect();
        }

        $kosans = Kosan::select('*')
            ->with(['kamars.fasilitas'])
            ->selectRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->where('status_validasi', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->limit(5)
            ->get();

        foreach ($kosans as $kosan) {
            $kosan->distance_text = $this->formatDistance($kosan->distance);
        }

        return $kosans;
    }

    private function formatDistance($distance)
    {
        if ($distance < 1) {
            $meters = round($distance * 1000);
            return "{$meters} meter";
        } else {
            return round($distance, 1) . " km";
        }
    }

    public function saveUserLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $request->session()->put('user_latitude', $request->latitude);
        $request->session()->put('user_longitude', $request->longitude);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil disimpan'
        ]);
    }
}
