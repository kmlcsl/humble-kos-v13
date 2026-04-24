<?php

namespace App\Http\Controllers;

use App\Models\UlasanKosan;
use App\Models\Kosan;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index()
    {
        $user = Auth::user();
        $reviews = UlasanKosan::where('user_id', $user->user_id)
            ->with('kosan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('users.reviews.index', [
            'reviews' => $reviews,
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $review = UlasanKosan::where('review_id', $id)
            ->where('user_id', $user->user_id)
            ->with('kosan')
            ->firstOrFail();

        return view('users.reviews.show', [
            'review' => $review,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Jika request GET, tampilkan form edit
        if ($request->isMethod('get')) {
            $review = UlasanKosan::where('review_id', $id)
                ->where('user_id', $user->user_id)
                ->with('kosan')
                ->firstOrFail();

            return view('users.reviews.update', [
                'review' => $review,
            ]);
        }

        // Jika request PUT, proses update
        $review = UlasanKosan::where('review_id', $id)
            ->where('user_id', $user->user_id)
            ->firstOrFail();

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'komentar' => 'required|min:10',
        ]);

        $result = $this->reviewService->updateReview($review, $request->rating, $request->komentar);

        if ($result['success']) {
            return redirect()->route('users.reviews.index')->with('success', 'Ulasan berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', $result['message'])->withInput();
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $review = UlasanKosan::where('review_id', $id)
            ->where('user_id', $user->user_id)
            ->firstOrFail();

        $result = $this->reviewService->deleteReview($review);

        if ($result['success']) {
            return redirect()->route('users.reviews.index')->with('success', 'Ulasan berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }
}
