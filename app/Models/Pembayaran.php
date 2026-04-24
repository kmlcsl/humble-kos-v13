<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants untuk referensi
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESSFUL = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';

    const STATUS_CANCELLED = 'cancelled';

    // Payment method constants
    const METHOD_DANA = 'dana';
    const METHOD_MIDTRANS = 'midtrans';

    /**
     * Get id accessor (alias for pembayaran_id)
     */
    public function getIdAttribute()
    {
        return $this->pembayaran_id;
    }

    /**
     * Get kode_pembayaran accessor (alias for transaction_id atau generate dari pembayaran_id)
     */
    public function getKodePembayaranAttribute()
    {
        return $this->transaction_id ?? 'PAY-' . str_pad($this->pembayaran_id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi dengan booking
     */
    public function booking()
    {
        return $this->belongsTo(BookingKosan::class, 'booking_id', 'booking_id');
    }

    /**
     * Format jumlah pembayaran
     */
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format((float) $this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-warning text-dark">Menunggu Pembayaran</span>',
            self::STATUS_PROCESSING => '<span class="badge bg-info">Sedang Diproses</span>',
            self::STATUS_SUCCESSFUL => '<span class="badge bg-success">Pembayaran Berhasil</span>',
            self::STATUS_FAILED => '<span class="badge bg-danger">Pembayaran Gagal</span>',
            self::STATUS_EXPIRED => '<span class="badge bg-secondary">Kadaluarsa</span>',
        ];

        $status = $this->attributes['status_pembayaran'] ?? null;
        return $badges[$status] ?? '<span class="badge bg-light text-dark">Unknown</span>';
    }

    /**
     * Get status text only (without HTML)
     */
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_SUCCESSFUL => 'Pembayaran Berhasil',
            self::STATUS_FAILED => 'Pembayaran Gagal',
            self::STATUS_EXPIRED => 'Kadaluarsa',
        ];

        $status = $this->attributes['status_pembayaran'] ?? null;
        return $statusTexts[$status] ?? 'Status Tidak Diketahui';
    }

    /**
     * Check if payment is active (can be processed)
     */
    public function getIsActiveAttribute()
    {
        $status = $this->attributes['status_pembayaran'] ?? null;
        return in_array($status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Check if payment can be retried
     */
    public function getCanBeRetriedAttribute()
    {
        $status = $this->attributes['status_pembayaran'] ?? null;
        return in_array($status, [self::STATUS_FAILED, self::STATUS_EXPIRED, self::STATUS_CANCELLED]);
    }

    /**
     * Check if payment is successful
     */
    public function getIsSuccessfulAttribute()
    {
        return ($this->attributes['status_pembayaran'] ?? null) === self::STATUS_SUCCESSFUL;
    }

    /**
     * Check if payment is expired
     */
    public function getIsExpiredAttribute()
    {
        $status = $this->attributes['status_pembayaran'] ?? null;
        if ($status === self::STATUS_EXPIRED) {
            return true;
        }
        // Optional: if you add an expiry policy later, compute here

        return false;
    }

    /**
     * Get time remaining until expiry
     */
    public function getTimeRemainingAttribute()
    {
        return null;
    }

    /**
     * Get formatted expiry time
     */
    public function getFormattedExpiryAttribute()
    {
        return '-';
    }

    /**
     * Get formatted payment time
     */
    public function getFormattedPaymentTimeAttribute()
    {
        if (!$this->tanggal_bayar) {
            return '-';
        }
        return Carbon::parse($this->tanggal_bayar)->format('d M Y H:i');
    }

    /**
     * Get payment method display name
     */
    public function getMethodDisplayNameAttribute()
    {
        $methods = [
            self::METHOD_DANA => 'DANA',
            self::METHOD_MIDTRANS => 'Midtrans',
        ];

        return $methods[$this->metode_pembayaran] ?? ucfirst($this->metode_pembayaran);
    }

    /**
     * Check if payment method is DANA
     */
    public function getIsDanaAttribute()
    {
        return $this->metode_pembayaran === self::METHOD_DANA;
    }

    /**
     * Check if payment method is Midtrans
     */
    public function getIsMidtransAttribute()
    {
        return $this->metode_pembayaran === self::METHOD_MIDTRANS;
    }

    /**
     * Scope untuk filter pembayaran aktif
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status_pembayaran', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    /**
     * Scope untuk filter pembayaran berhasil
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status_pembayaran', self::STATUS_SUCCESSFUL);
    }

    /**
     * Scope untuk filter pembayaran gagal
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status_pembayaran', [self::STATUS_FAILED, self::STATUS_EXPIRED]);
    }

    /**
     * Scope untuk filter berdasarkan metode pembayaran
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('metode_pembayaran', $method);
    }

    /**
     * Boot method untuk model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update booking status when payment is successful
        static::updated(function ($pembayaran) {
            $status = $pembayaran->status_pembayaran ?? null;
            if ($pembayaran->wasChanged('status_pembayaran') && $status === self::STATUS_SUCCESSFUL) {
                $booking = $pembayaran->booking;
                $bookingStatus = $booking ? ($booking->status_booking ?? null) : null;
                if ($booking && $bookingStatus === 'pending') {
                    $booking->status_booking = 'confirmed';
                    $booking->save();
                }
            }
        });
    }

    /**
     * Relasi dengan bukti pembayaran manual
     */
    public function buktiPembayaran()
    {
        return null;
    }

    /**
     * Check if payment method is manual
     */
    public function getIsManualAttribute()
    {
        return $this->tipe_pembayaran === 'manual';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status_pembayaran) {
            'paid' => 'Sukses',
            'pending' => 'Menunggu',
            'failed' => 'Gagal',
            default => ucfirst($this->status_pembayaran),
        };
    }

}
