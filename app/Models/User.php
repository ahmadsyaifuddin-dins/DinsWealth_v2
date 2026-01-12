<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_user');
    }

    public function akunKeuangan()
    {
        return $this->hasMany(AkunKeuangan::class, 'id_user');
    }

    public function targetTabungan()
    {
        return $this->hasMany(TargetTabungan::class, 'id_user');
    }
}
