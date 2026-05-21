<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\Notifikasi;

class PembayaranObserver
{
    public function updated(Pembayaran $pembayaran): void
    {
        $status = $pembayaran->status_pembayaran ?? null;
        if ($pembayaran->wasChanged('status_pembayaran') && $status === Pembayaran::STATUS_PAID) {
            $booking = $pembayaran->booking;
            if ($booking && ($booking->status_booking ?? null) === 'pending') {
                $booking->status_booking = 'confirmed';
                $booking->save();

                $kamar = $booking->kamar;
                if ($kamar && $kamar->status_kamar === 'tersedia') {
                    $kamar->status_kamar = 'terisi';
                    $kamar->save();
                }
            }

            $userId = optional($pembayaran->booking)->user_id;
            if ($userId) {
                Notifikasi::create([
                    'user_id' => $userId,
                    'type' => 'payment_confirmed',
                    'title' => 'Pembayaran berhasil dikonfirmasi',
                    'message' => 'Pembayaran untuk booking #' . ($pembayaran->booking_id ?? '-') . ' telah berhasil.',
                    'related_type' => 'pembayaran',
                    'related_id' => $pembayaran->pembayaran_id ?? null,
                    'is_read' => false,
                ]);
                Notifikasi::create([
                    'user_id' => $userId,
                    'type' => 'booking_confirmed',
                    'title' => 'Booking dikonfirmasi',
                    'message' => 'Booking #' . ($pembayaran->booking_id ?? '-') . ' sekarang dikonfirmasi.',
                    'related_type' => 'booking',
                    'related_id' => $pembayaran->booking_id ?? null,
                    'is_read' => false,
                ]);
            }
        }
    }
}
