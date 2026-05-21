<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_type',
        'related_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
