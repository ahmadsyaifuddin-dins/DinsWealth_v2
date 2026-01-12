<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $guarded = ['id'];

    // Casting tanggal agar jadi objek Carbon
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'adalah_kalibrasi' => 'boolean',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relasi ke Kategori (Ingat, ini bisa null/kosong)
    public function kategori()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori');
    }

    // Relasi ke Akun Keuangan (Dompet/Bank)
    public function akun()
    {
        return $this->belongsTo(AkunKeuangan::class, 'id_akun');
    }
}
