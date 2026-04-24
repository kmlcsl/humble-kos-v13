<?php

namespace App\Services;

use App\Models\UlasanKosan;
use App\Models\Kosan;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function createReview(array $data)
    {
        DB::beginTransaction();

        try {
            // Check if the kosan exists
            $kosan = Kosan::find($data['kosan_id']);

            if (!$kosan) {
                return [
                    'success' => false,
                    'message' => 'Kosan tidak ditemukan.'
                ];
            }

            // Check if user already reviewed this kosan
            $existingReview = UlasanKosan::where('kosan_id', $data['kosan_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if ($existingReview) {
                return [
                    'success' => false,
                    'message' => 'Anda sudah memberikan ulasan untuk kosan ini. Silakan edit ulasan Anda yang sudah ada.'
                ];
            }

            // Create the review
            $review = new UlasanKosan();
            $review->kosan_id = $data['kosan_id'];
            $review->user_id = $data['user_id'];
            $review->rating = $data['rating'];
            $review->komentar = $data['komentar'];
            if (isset($data['booking_id'])) {
                $review->booking_id = $data['booking_id'];
            }

            $review->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Ulasan berhasil dibuat.',
                'review' => $review
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat ulasan. Silakan coba lagi.'
            ];
        }
    }

    public function updateReview(UlasanKosan $review, int $rating, string $komentar)
    {
        DB::beginTransaction();

        try {
            // Update the review
            $review->rating = $rating;
            $review->komentar = $komentar;

            $review->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Ulasan berhasil diperbarui.',
                'review' => $review
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui ulasan. Silakan coba lagi.'
            ];
        }
    }

    public function deleteReview(UlasanKosan $review)
    {
        DB::beginTransaction();

        try {
            // Delete the review
            $review->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Ulasan berhasil dihapus.'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus ulasan. Silakan coba lagi.'
            ];
        }
    }

    public function verifyReview(UlasanKosan $review)
    {
        DB::beginTransaction();

        try {
            // Verify the review
            $review->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Ulasan berhasil diverifikasi.',
                'review' => $review
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi ulasan. Silakan coba lagi.'
            ];
        }
    }
}
