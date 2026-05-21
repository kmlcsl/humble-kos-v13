<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $foto_id
 * @property string $properti_type
 * @property int $properti_id
 * @property string $path_foto
 * @property int $urutan
 * @property bool $is_utama
 * @property string|null $caption
 * @property int|null $ukuran_file
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class FotoProperti extends Model
{
    use HasFactory;

    protected $table = 'foto_properti';
    protected $primaryKey = 'foto_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'properti_type',
        'properti_id',
        'path_foto',
        'urutan',
        'is_utama',
        'caption',
        'ukuran_file',
    ];

    protected $casts = [
        'is_utama' => 'boolean',
        'urutan' => 'integer',
        'ukuran_file' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent properti model (Kosan or Kamar)
     */
    public function properti()
    {
        return $this->morphTo(__FUNCTION__, 'properti_type', 'properti_id');
    }

    /**
     * Scope untuk mendapatkan foto utama
     */
    public function scopeUtama(Builder $query)
    {
        return $query->where('is_utama', true)
                     ->orWhere('urutan', 1)
                     ->orderBy('urutan', 'asc');
    }

    /**
     * Scope untuk mendapatkan foto tambahan
     */
    public function scopeTambahan(Builder $query)
    {
        return $query->where('is_utama', false)
                     ->where('urutan', '>', 1)
                     ->orderBy('urutan', 'asc');
    }

    /**
     * Accessor untuk full path foto
     */
    public function getFullPathAttribute()
    {
        return $this->path_foto ? asset('storage/' . $this->path_foto) : null;
    }
}
