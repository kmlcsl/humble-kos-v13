<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $primaryKey = 'fasilitas_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_fasilitas',
        'icon_fasilitas',
        'deskripsi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the kamars that have this fasilitas (many-to-many relationship)
     */
    public function kamars()
    {
        return $this->belongsToMany(Kamar::class, 'kamar_fasilitas', 'fasilitas_id', 'kamar_id', 'fasilitas_id', 'kamar_id');
    }

    /**
     * Get kamar fasilitas pivot records
     */
    public function kamarFasilitas()
    {
        return $this->hasMany(KamarFasilitas::class, 'fasilitas_id', 'fasilitas_id');
    }
}
