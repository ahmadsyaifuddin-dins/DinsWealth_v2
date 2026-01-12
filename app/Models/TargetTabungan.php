<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetTabungan extends Model
{
    use HasFactory;

    protected $table = 'target_tabungan';

    protected $guarded = ['id'];

    // Agar kolom tanggal otomatis jadi objek Carbon (bisa diformat tgl)
    protected $casts = [
        'tanggal_deadline' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
