<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunKeuangan extends Model
{
    use HasFactory;

    protected $table = 'akun_keuangan';

    protected $guarded = ['id'];

    // Relasi: Akun milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi: Akun punya banyak history Transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_akun');
    }
}
