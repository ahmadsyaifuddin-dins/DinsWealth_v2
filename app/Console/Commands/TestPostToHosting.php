<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestPostToHosting extends Command
{
    protected $signature = 'test:post-hosting';

    protected $description = 'Mencoba kirim data POST ke InfinityFree';

    public function handle()
    {
        $url = 'https://dins-wealth.page.gd/tabungan'; // URL Store Laravel kamu

        $this->info("🚀 Mencoba mengirim data ke: $url");
        $this->info('📦 Paket: Nominal 5.000 (Testing)');

        // Simulasi Data dari Flutter
        $dataKirim = [
            'nama' => 2, // ID Kategori (misal jajan)
            'jenis' => 2, // Pengeluaran
            'nominal' => '5000',
            'keterangan' => 'Test Tembak API dari Terminal',
        ];

        try {
            // Kita coba nyamar jadi Browser Chrome biar gak langsung ditendang
            // Tapi biasanya InfinityFree tetap tau ini bot
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept' => 'application/json', // Kita minta balasan JSON
            ])->post($url, $dataKirim);

            $this->newLine();
            $this->info('--- HASIL RESPONS SERVER ---');
            $this->info('Status Code: '.$response->status());

            // Kita cek apa isi balasannya
            $body = $response->body();

            if (str_contains($body, 'aes.js') || str_contains($body, 'cookie')) {
                $this->error('⛔ BLOKIR TERDETEKSI: Server membalas dengan Script Keamanan (Javascript Challenge).');
                $this->line('Ini artinya Flutter TIDAK AKAN BISA masuk.');
            } elseif ($response->status() == 419) {
                $this->error('❌ CSRF TOKEN ERROR: Laravel menolak karena tidak ada Token.');
            } elseif ($response->status() == 401 || $response->status() == 302) {
                $this->warn('🔒 AUTH ERROR: Kamu belum login (Redirect ke halaman login).');
                $this->line('Tapi setidaknya server merespon (Lolos dari satpam depan).');
            } else {
                $this->info('✅ AJAIB! Data berhasil masuk (Sangat tidak mungkin terjadi).');
            }

            $this->newLine();
            $this->info('Cuplikan Isi Body:');
            $this->line(substr($body, 0, 300).'...'); // Tampilkan 300 huruf pertama

        } catch (\Exception $e) {
            $this->error('Terjadi Error Koneksi: '.$e->getMessage());
        }
    }
}
