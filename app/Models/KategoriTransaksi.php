<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTransaksi extends Model
{
    use HasFactory;

    // PENTING: Beritahu Laravel nama tabelnya
    protected $table = 'kategori_transaksi';

    // Izinkan semua kolom diisi (biar import lancar)
    protected $guarded = ['id'];

    // Relasi: Satu Kategori punya banyak Transaksi
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_kategori');
    }
}
