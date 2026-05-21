<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $kamar_id
 * @property int $kosan_id
 * @property string $nomor_kamar
 * @property string $tipe_kamar
 * @property float $harga_per_bulan
 * @property string $ukuran_kamar
 * @property int $kapasitas
 * @property string|null $deskripsi
 * @property string|null $foto_kamar
 * @property string $status_kamar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Kamar extends Model
{
    use HasFactory;
    protected $table = 'kamar';
    protected $primaryKey = 'kamar_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kosan_id',
        'nomor_kamar',
        'tipe_kamar',
        'harga_per_bulan',
        'ukuran_kamar',
        'kapasitas',
        'deskripsi',
        'foto_kamar',
        'status_kamar',
    ];

    protected $casts = [
        'harga_per_bulan' => 'decimal:2',
        'kapasitas' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function kosan()
    {
        return $this->belongsTo(Kosan::class, 'kosan_id', 'kosan_id');
    }

    public function kosanById()
    {
        return $this->belongsTo(Kosan::class, 'kosan_id', 'id');
    }

    /**
     * Get the fasilitas that are assigned to this kamar (many-to-many relationship)
     */
    public function fasilitas()
    {
        return $this->belongsToMany(
            Fasilitas::class,      // Related model
            'kamar_fasilitas',     // Pivot table name
            'kamar_id',            // Foreign key on pivot table for THIS model
            'fasilitas_id'         // Foreign key on pivot table for RELATED model
        );
    }

    /**
     * Polymorphic relationship untuk foto-foto kamar
     */
    public function fotos()
    {
        return $this->morphMany(FotoProperti::class, 'properti');
    }

    /**
     * Get foto tambahan kamar (urutan 2-4)
     */
    public function fotoTambahan()
    {
        return $this->morphMany(FotoProperti::class, 'properti')
                    ->where('urutan', '>', 1)
                    ->orderBy('urutan', 'asc');
    }

    public function getFotoUtamaAttribute()
    {
        // Main photo path is now stored exclusively in the 'foto_kamar' column.
        $path = $this->attributes['foto_kamar'] ?? null;
        return $path ? (object) ['path_gambar' => $path] : null;
    }

    public function bookings()
    {
        return $this->hasMany(BookingKosan::class, 'kamar_id', 'kamar_id');
    }

    public function activeBooking()
    {
        return $this->hasOne(BookingKosan::class, 'kamar_id', 'kamar_id')
            ->where('status_booking', 'confirmed')
            ->whereDate('tanggal_checkout', '>=', now())
            ->latest();
    }

    public function getIsAvailableAttribute()
    {
        return ($this->attributes['status_kamar'] ?? null) === 'tersedia' && !$this->activeBooking;
    }

    public function getHargaSetelahDiskonAttribute()
    {
        $harga = $this->attributes['harga_per_bulan'] ?? 0;
        return $harga;
    }

    public function isUnderMaintenance()
    {
        return in_array($this->attributes['status_kamar'] ?? null, ['maintenance', 'pemeliharaan']);
    }
}
