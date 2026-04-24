<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KamarFasilitas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kamar_fasilitas';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'kamar_fasilitas_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kamar_id',
        'fasilitas_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the kamar that owns the kamar fasilitas.
     */
    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id', 'kamar_id');
    }

    /**
     * Get the fasilitas that owns the kamar fasilitas.
     */
    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id', 'fasilitas_id');
    }
}
