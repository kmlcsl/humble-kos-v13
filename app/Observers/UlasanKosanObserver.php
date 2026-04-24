<?php

namespace App\Observers;

use App\Models\UlasanKosan;
use App\Models\Kosan;

class UlasanKosanObserver
{
    /**
     * Handle the UlasanKosan "created" event.
     */
    public function created(UlasanKosan $ulasanKosan): void
    {
        $this->updateKosanRating($ulasanKosan);
    }

    /**
     * Handle the UlasanKosan "updated" event.
     */
    public function updated(UlasanKosan $ulasanKosan): void
    {
        $this->updateKosanRating($ulasanKosan);
    }

    /**
     * Handle the UlasanKosan "deleted" event.
     */
    public function deleted(UlasanKosan $ulasanKosan): void
    {
        $this->updateKosanRating($ulasanKosan);
    }

    /**
     * Handle the UlasanKosan "restored" event.
     */
    public function restored(UlasanKosan $ulasanKosan): void
    {
        $this->updateKosanRating($ulasanKosan);
    }

    /**
     * Handle the UlasanKosan "force deleted" event.
     */
    public function forceDeleted(UlasanKosan $ulasanKosan): void
    {
        $this->updateKosanRating($ulasanKosan);
    }

    /**
     * Update the rating of the associated Kosan.
     *
     * @param UlasanKosan $ulasanKosan
     * @return void
     */
    protected function updateKosanRating(UlasanKosan $ulasanKosan): void
    {
        $kosan = $ulasanKosan->kosan;

        if ($kosan) {
            // Calculate the new average rating
            $averageRating = $kosan->ulasanReview()->avg('rating');
            
            // Update the kosan's rating_rata field
            $kosan->rating_rata = $averageRating ?? 0;
            $kosan->save();
        }
    }
}