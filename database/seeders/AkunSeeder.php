<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        // Kita asumsikan ID user 1 adalah Dins (Admin)
        $userId = 1;

        $akuns = ['Dompet Fisik', 'Laci Kas', 'SeaBank', 'GoPay'];

        foreach ($akuns as $akun) {
            DB::table('akun_keuangan')->insert([
                'id_user' => $userId,
                'nama' => $akun,
                'saldo_saat_ini' => 0, // Mulai dari 0
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
