<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akun_keuangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();

            // Nama Akun (Contoh: "Dompet Dins", "Laci Kas", "SeaBank")
            $table->string('nama');

            // Saldo Saat Ini (Akan berubah otomatis saat ada transaksi)
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun_keuangan');
    }
};
