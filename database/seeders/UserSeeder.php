<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Akun Admin Utama (Dins)
        DB::table('users')->insert([
            'name' => 'Dins Admin',
            'email' => 'admin@gmail.com', // Email login
            'password' => Hash::make('password'), // Password login (ganti nanti)
            'role' => 'admin', // Role admin sesuai enum di migration
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Buat Akun Member Dummy (Untuk Tes Multi-user)
        DB::table('users')->insert([
            'name' => 'Member',
            'email' => 'member@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
