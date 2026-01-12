<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // 1. Identitas Pemilik Data
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();

            // 2. Relasi ke Kategori (Nullable, jaga-jaga kalau transaksi Kalibrasi tidak butuh kategori)
            $table->foreignId('id_kategori')->nullable()->constrained('kategori_transaksi')->nullOnDelete();

            // 3. Relasi ke Akun Keuangan (Sumber Dananya dari mana? Dompet/Bank?)
            // Ini menghubungkan transaksi ke tabel 'akun_keuangan' (file no 2 diatas)
            $table->foreignId('id_akun')->nullable()->constrained('akun_keuangan')->nullOnDelete();

            // 4. Data Utama
            $table->enum('jenis', ['pemasukan', 'pengeluaran']); // Biar query cepat
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_transaksi');

            // 5. Penanda Khusus (Untuk Fitur Real Case)
            // Jika TRUE, berarti ini transaksi otomatis sistem untuk menyeimbangkan saldo
            $table->boolean('adalah_kalibrasi')->default(false);

            // 6. Bukti Foto Struk
            $table->string('bukti_foto')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
