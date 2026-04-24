<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UlasanKosan extends Model
{
    protected $table = 'ulasan_review';
    protected $primaryKey = 'review_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'kosan_id',
        'booking_id',
        'rating',
        'komentar',
        'foto_review',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function kosan()
    {
        return $this->belongsTo(Kosan::class, 'kosan_id', 'kosan_id');
    }

    public function booking()
    {
        return $this->belongsTo(BookingKosan::class, 'booking_id', 'booking_id');
    }
}
