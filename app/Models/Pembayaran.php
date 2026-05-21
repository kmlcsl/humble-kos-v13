<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

/**
 * @property int $pembayaran_id
 * @property int $booking_id
 * @property string|null $tipe_pembayaran
 * @property string $metode_pembayaran
 * @property string|null $payment_gateway
 * @property string|null $transaction_id
 * @property float $jumlah_bayar
 * @property string|null $bukti_transfer
 * @property string $status_pembayaran
 * @property \Illuminate\Support\Carbon|null $tanggal_bayar
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \App\Models\BookingKosan|null $booking
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Pembayaran query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pembayaran active()
 * @method static \Illuminate\Database\Eloquent\Builder|Pembayaran successful()
 * @method static \Illuminate\Database\Eloquent\Builder|Pembayaran failed()
 */
class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'pembayaran_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'booking_id',
        'tipe_pembayaran',
        'metode_pembayaran',
        'payment_gateway',
        'transaction_id',
        'jumlah_bayar',
        'bukti_transfer',
        'status_pembayaran',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_bayar' => 'float',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status pembayaran sesuai enum database
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    /**
     * Relasi ke Booking
     */
    public function booking()
    {
        return $this->belongsTo(BookingKosan::class, 'booking_id', 'booking_id');
    }

    /**
     * Scope pembayaran aktif (pending)
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status_pembayaran', self::STATUS_PENDING);
    }

    /**
     * Scope pembayaran berhasil (paid)
     */
    public function scopeSuccessful(Builder $query)
    {
        return $query->where('status_pembayaran', self::STATUS_PAID);
    }

    /**
     * Scope pembayaran gagal (failed/expired)
     */
    public function scopeFailed(Builder $query)
    {
        return $query->whereIn('status_pembayaran', [self::STATUS_FAILED, self::STATUS_EXPIRED]);
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update status booking otomatis jika pembayaran lunas
        static::updated(function ($pembayaran) {
            if ($pembayaran->wasChanged('status_pembayaran') && $pembayaran->status_pembayaran === self::STATUS_PAID) {
                $booking = $pembayaran->booking;
                if ($booking && $booking->status_booking === 'pending') {
                    $booking->status_booking = 'confirmed';
                    $booking->save();
                }
            }
        });
    }

    /**
     * Accessor label status pembayaran
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status_pembayaran) {
            self::STATUS_PAID => 'Sukses',
            self::STATUS_PENDING => 'Menunggu',
            self::STATUS_FAILED => 'Gagal',
            self::STATUS_EXPIRED => 'Kadaluarsa',
            default => ucfirst($this->status_pembayaran),
        };
    }

    /**
     * Format rupiah jumlah bayar
     */
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }
}
