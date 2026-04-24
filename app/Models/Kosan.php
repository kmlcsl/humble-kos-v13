<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kosan extends Model
{
    use HasFactory;
    protected $table = 'kosan';
    protected $primaryKey = 'kosan_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'owner_id',
        'nama_kosan',
        'alamat',
        'kota',
        'deskripsi',
        'tipe_kosan',
        'peraturan',
        'foto_kosan',
        'rating_rata',
        'status_validasi',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'rating_rata' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'favorit' => 'array',
    ];

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'owner_id', 'user_id');
    }

    public function kamars()
    {
        return $this->hasMany(Kamar::class, 'kosan_id', 'kosan_id');
    }

    public function bookings()
    {
        return $this->hasManyThrough(
            BookingKosan::class,
            Kamar::class,
            'kosan_id',   // Foreign key on Kamar
            'kamar_id',   // Foreign key on BookingKosan
            'kosan_id',   // Local key on Kosan
            'kamar_id'    // Local key on Kamar
        );
    }

    public function ulasanReview()
    {
        return $this->hasMany(\App\Models\UlasanKosan::class, 'kosan_id', 'kosan_id');
    }

    /**
     * Polymorphic relationship untuk foto-foto kosan
     */
    public function fotos()
    {
        return $this->morphMany(FotoProperti::class, 'properti');
    }

    /**
     * Get foto tambahan kosan (urutan 2-4)
     */
    public function fotoTambahan()
    {
        return $this->morphMany(FotoProperti::class, 'properti')
                    ->where('urutan', '>', 1)
                    ->orderBy('urutan', 'asc');
    }

    /**
     * Get foto utama kosan
     */
    public function getFotoUtamaAttribute()
    {
        // Main photo path is now stored exclusively in the 'foto_kosan' column.
        $path = $this->attributes['foto_kosan'] ?? null;
        return $path ? (object) ['path_gambar' => $path] : null;
    }

    public function difavoritkanOleh($idPengguna)
    {
        if (!$idPengguna || !$this->favorit) {
            return false;
        }
        return in_array($idPengguna, $this->favorit);
    }

    public function getHargaSetelahDiskonAttribute()
    {
        $bulanan = (float) $this->getHargaBulananAttribute();
        $diskon = (float) ($this->attributes['persentase_diskon'] ?? 0);
        if ($diskon > 0) {
            $bulanan = $bulanan * (1 - ($diskon / 100));
        }
        return $bulanan;
    }

    /**
     * Get harga bulanan (dari kamar termurah)
     */
    public function getHargaBulananAttribute()
    {
        // Cek apakah relasi kamars sudah di-load
        if ($this->relationLoaded('kamars') && $this->kamars->isNotEmpty()) {
            return $this->kamars->min('harga_per_bulan');
        }

        // Jika belum di-load, query langsung
        return $this->kamars()->min('harga_per_bulan') ?? 0;
    }

    /**
     * Harga tahunan kosan (fallback ke 12×harga bulanan bila tidak ada kolom)
     */
    public function getHargaTahunanAttribute()
    {
        $explicit = $this->attributes['harga_tahunan'] ?? null;
        $bulanan = (float) $this->getHargaBulananAttribute();
        return $explicit !== null ? (float) $explicit : ($bulanan * 12);
    }

    /**
     * Harga tahunan setelah diskon (menggunakan persentase_diskon bila ada)
     */
    public function getHargaTahunanSetelahDiskonAttribute()
    {
        $tahunan = (float) $this->getHargaTahunanAttribute();
        $diskon = (float) ($this->attributes['persentase_diskon'] ?? 0);
        if ($diskon > 0) {
            $tahunan = $tahunan * (1 - ($diskon / 100));
        }
        return $tahunan;
    }

    /**
     * Alias untuk ulasanReview (kompatibilitas dengan view lama)
     */
    public function ulasanKosan()
    {
        return $this->ulasanReview();
    }

    public function getRataRataRatingAttribute()
    {
        return (float) ($this->attributes['rating_rata'] ?? 0);
    }

    public function getJumlahUlasanAttribute()
    {
        if ($this->relationLoaded('ulasanReview')) {
            return (int) $this->ulasanReview->count();
        }
        return (int) $this->ulasanReview()->count();
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status_validasi) {
            'pending' => 'Menunggu Verifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status_validasi),
        };
    }
}
