<?php

namespace App\Http\Controllers\Pemilik;

use App\Http\Controllers\Controller;
use App\Models\Kosan;
use App\Models\UlasanKosan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemilikUlasanController extends Controller
{
    /**
     * Display all reviews for owner's properties
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get all kosan IDs owned by this user
        $kosanIds = Kosan::where('owner_id', $user->user_id)->pluck('kosan_id');

        $query = UlasanKosan::with(['user', 'kosan', 'booking'])
            ->whereIn('kosan_id', $kosanIds);

        // Filter by kosan
        if ($request->has('kosan_id') && $request->kosan_id != '') {
            $query->where('kosan_id', $request->kosan_id);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        // Search by user name or comment
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('komentar', 'LIKE', '%' . $request->search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($request) {
                        $userQuery->where('nama_lengkap', 'LIKE', '%' . $request->search . '%');
                    });
            });
        }

        $ulasanList = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get kosan list for filter
        $kosanList = Kosan::where('owner_id', $user->user_id)->get();

        // Statistics
        $totalUlasan = UlasanKosan::whereIn('kosan_id', $kosanIds)->count();
        $rataRataRating = UlasanKosan::whereIn('kosan_id', $kosanIds)->avg('rating');
        $rataRataRating = $rataRataRating ? round($rataRataRating, 2) : 0;

        // Rating distribution
        $ratingDistribution = [
            5 => UlasanKosan::whereIn('kosan_id', $kosanIds)->where('rating', 5)->count(),
            4 => UlasanKosan::whereIn('kosan_id', $kosanIds)->where('rating', 4)->count(),
            3 => UlasanKosan::whereIn('kosan_id', $kosanIds)->where('rating', 3)->count(),
            2 => UlasanKosan::whereIn('kosan_id', $kosanIds)->where('rating', 2)->count(),
            1 => UlasanKosan::whereIn('kosan_id', $kosanIds)->where('rating', 1)->count(),
        ];

        return view('pemilik.ulasan.index', [
            'ulasanList' => $ulasanList,
            'kosanList' => $kosanList,
            'totalUlasan' => $totalUlasan,
            'rataRataRating' => $rataRataRating,
            'ratingDistribution' => $ratingDistribution
        ]);
    }

    /**
     * Display single review details
     */
    public function show($id)
    {
        $user = Auth::user();

        $kosanIds = Kosan::where('owner_id', $user->user_id)->pluck('kosan_id');

        $ulasan = UlasanKosan::with(['user', 'kosan', 'booking.kamar'])
            ->whereIn('kosan_id', $kosanIds)
            ->findOrFail($id);

        return view('pemilik.ulasan.show', [
            'ulasan' => $ulasan
        ]);
    }
}
