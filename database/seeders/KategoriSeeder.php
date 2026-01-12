<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Jangan lupa import ini

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar Kategori Pemasukan
        $pemasukan = ['Gaji Bulanan', 'Bonus/Tunjangan', 'Hadiah', 'Penjualan Joki'];

        // Daftar Kategori Pengeluaran
        $pengeluaran = ['Makan & Minum', 'Transportasi', 'Kuota Internet', 'Belanja Harian', 'Cicilan', 'Hiburan'];

        foreach ($pemasukan as $item) {
            DB::table('kategori_transaksi')->insert([
                'nama' => $item,
                'slug' => Str::slug($item),
                'jenis' => 'pemasukan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($pengeluaran as $item) {
            DB::table('kategori_transaksi')->insert([
                'nama' => $item,
                'slug' => Str::slug($item),
                'jenis' => 'pengeluaran',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
