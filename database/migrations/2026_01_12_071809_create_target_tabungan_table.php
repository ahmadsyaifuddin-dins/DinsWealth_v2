<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();

            $table->string('nama_barang'); // Contoh: "Laptop Axioo Pongo 725"
            $table->decimal('harga_target', 15, 2); // Target: 13.600.000
            $table->decimal('terkumpul_saat_ini', 15, 2)->default(0); // Uang yg sudah disisihkan

            $table->date('tanggal_deadline')->nullable(); // Target beli
            $table->text('catatan')->nullable();
            $table->string('foto_barang')->nullable(); // Path gambar

            // Status: aktif, tercapai, dibatalkan
            $table->enum('status', ['aktif', 'tercapai', 'dibatalkan'])->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_tabungan');
    }
};
