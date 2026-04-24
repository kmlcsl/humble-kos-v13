<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property int $booking_id
 * @property int $user_id
 * @property int|null $kamar_id
 * @property \Illuminate\Support\Carbon|string $tanggal_checkin
 * @property \Illuminate\Support\Carbon|string $tanggal_checkout
 * @property int $durasi
 * @property float|string $total_harga
 * @property string $kode_booking
 * @property string $status_booking
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
/**
 * @property \Illuminate\Support\Carbon $tanggal_checkin
 * @property \Illuminate\Support\Carbon $tanggal_checkout
 * @property float $total_harga
 */
class BookingKosan extends Model
{
    protected $table = 'booking';
    protected $primaryKey = 'booking_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'tanggal_checkin',
        'tanggal_checkout',
        'durasi',
        'total_harga',
        'kode_booking',
        'status_booking',
        'catatan',
    ];

    protected $casts = [
        'tanggal_checkin' => 'date',
        'tanggal_checkout' => 'date',
        'total_harga' => 'decimal:2',
        'durasi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->booking_id;
    }

    public function kosan()
    {
        return $this->hasOneThrough(
            Kosan::class,
            Kamar::class,
            'kamar_id', // Foreign key on the Kamar table...
            'kosan_id', // Foreign key on the Kosan table...
            'kamar_id', // Local key on the BookingKosan table...
            'kosan_id'  // Local key on the Kamar table...
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id', 'kamar_id');
    }

    public function getNoHpAttribute()
    {
        return $this->user ? ($this->user->no_telepon ?? null) : null;
    }

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    public function getHargaKamarAttribute()
    {
        if (!$this->kamar)
            return 0;
        return $this->kamar->getHargaSetelahDiskonAttribute();
    }

    public function getFormattedHargaKamarAttribute()
    {
        return 'Rp ' . number_format($this->getHargaKamarAttribute(), 0, ',', '.');
    }

    public function getFormattedTotalHargaAttribute()
    {
        return 'Rp ' . number_format((float) $this->total_harga, 0, ',', '.');
    }

    /**
     * Get corrected total harga berdasarkan harga kamar dan durasi
     */
    public function getCorrectedTotalHargaAttribute()
    {
        // Hitung total harga yang benar: harga kamar * durasi
        $hargaKamar = $this->getHargaKamarAttribute();
        $durasi = (int) $this->durasi;
        return $hargaKamar * $durasi;
    }

    public function getFormattedCorrectedTotalHargaAttribute()
    {
        return 'Rp ' . number_format($this->getCorrectedTotalHargaAttribute(), 0, ',', '.');
    }

    public function getFormattedTanggalMulaiAttribute()
    {
        return Carbon::parse($this->tanggal_checkin)->translatedFormat('d F Y');
    }

    public function getFormattedTanggalSelesaiAttribute()
    {
        return Carbon::parse($this->tanggal_checkout)->translatedFormat('d F Y');
    }

    public function getDurasiTextAttribute()
    {
        $nilaiDurasi = (int) $this->durasi;

        if ($nilaiDurasi >= 12) {
            return ($nilaiDurasi / 12) . ' Tahun';
        } else {
            return $nilaiDurasi . ' Bulan';
        }
    }

    public function getDaysRemainingAttribute()
    {
        if ($this->status_booking !== 'confirmed') {
            return 0;
        }

        $today = Carbon::today();
        $endDate = Carbon::parse($this->tanggal_checkout);

        if ($today->gt($endDate)) {
            return 0;
        }

        return $today->diffInDays($endDate);
    }

    public function getIsActiveAttribute()
    {
        return $this->status_booking === 'confirmed' && Carbon::parse($this->tanggal_checkout)->isFuture();
    }

    public function getStatusBadgeAttribute()
    {
        $class = '';
        switch ($this->status_booking) {
            case 'pending':
                $class = 'bg-warning text-dark';
                break;
            case 'confirmed':
                $class = 'bg-success';
                break;
            case 'cancelled':
                $class = 'bg-danger';
                break;
            case 'selesai':
                $class = 'bg-secondary';
                break;
            default:
                $class = 'bg-info';
        }
        $texts = [
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            'selesai' => 'Selesai',
        ];
        $label = $texts[$this->status_booking] ?? ucfirst($this->status_booking);
        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    public function getCanBeCanceledAttribute()
    {
        return in_array($this->status_booking, ['pending', 'confirmed']);
    }

    public function getCanBeExtendedAttribute()
    {
        return $this->status_booking === 'confirmed' && Carbon::parse($this->tanggal_checkout)->isFuture();
    }

    public function getCanBeCompletedAttribute()
    {
        return $this->status_booking === 'confirmed' && Carbon::parse($this->tanggal_checkout)->isPast();
    }

    /**
     * Get all payments for this booking
     */
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the latest payment attempt for this booking
     */
    public function latestPembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'booking_id', 'booking_id')->latestOfMany();
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status_booking) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status_booking),
        };
    }
}
