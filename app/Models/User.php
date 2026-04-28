<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $user_id
 * @property string $nama_lengkap
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $no_telepon
 * @property string $role
 * @property string|null $alamat
 * @property string|null $foto_profil
 * @property string $status_akun
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_lengkap',
        'username',
        'email',
        'password',
        'remember_token',
        'no_telepon',
        'role',
        'alamat',
        'foto_profil',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    public function getNameAttribute()
    {
        return $this->attributes['nama_lengkap'] ?? null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama_lengkap'] = $value;
    }

    public function getNoHpAttribute()
    {
        return $this->attributes['no_telepon'] ?? null;
    }

    public function setNoHpAttribute($value)
    {
        $this->attributes['no_telepon'] = $value;
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->foto_profil && !empty($this->foto_profil)) {
            return Storage::url($this->foto_profil);
        }

        // Fallback to a default SVG image if no photo is set
        return asset('images/default-avatar.svg');
    }

    /**
     * Get all of the reviews for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ulasanKosan()
    {
        return $this->hasMany(UlasanKosan::class, 'user_id', 'user_id');
    }

    /**
     * The kosans that the user has favorited.
     */
    public function favoriteKosans()
    {
        return $this->belongsToMany(Kosan::class, 'user_favorite_kosan', 'user_id', 'kosan_id');
    }
}
