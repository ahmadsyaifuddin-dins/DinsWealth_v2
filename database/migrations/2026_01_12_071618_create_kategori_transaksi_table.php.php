<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_transaksi', function (Blueprint $table) {
            $table->id();

            // Nama Kategori (Contoh: "Makan Siang")
            $table->string('nama');

            // Slug (Contoh: "makan-siang")
            // Wajib ada biar di Flutter nanti gampang nentuin icon berdasarkan slug ini
            $table->string('slug')->unique();

            // Jenis (Pemasukan / Pengeluaran)
            // Ini pengganti tabel 'transaction_types' & 'foreignId' yang ribet itu.
            // Cukup pakai enum, lebih cepat dan hemat storage.
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);

            // Path/Nama Icon (Opsional, kalau mau simpan nama icon material design)
            $table->string('ikon')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_transaksi');
    }
};
