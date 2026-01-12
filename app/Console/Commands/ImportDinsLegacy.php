<?php

namespace App\Console\Commands;

use App\Models\AkunKeuangan;
use App\Models\KategoriTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportDinsLegacy extends Command
{
    // Nama command yang akan kita ketik di terminal
    protected $signature = 'dins:import-legacy';

    // Deskripsi command
    protected $description = 'Menyedot data JSON dari dins-wealth.page.gd ke database local';

    public function handle()
    {
        // --- BAGIAN INI KITA MATIKAN (KASIH KOMENTAR) ---
        // $url = 'https://dins-wealth.page.gd/api-test/json';
        // $this->info("🚀 Sedang menghubungi server InfinityFree: $url ...");
        // ... dst (bagian Http::get jangan dipakai)

        // --- GANTI DENGAN KODE INI (BACA FILE LOKAL) ---
        $path = base_path('data_lama.json'); // Cari file di folder root project
        $this->info("📂 Membaca file lokal dari: $path");

        if (! file_exists($path)) {
            $this->error('❌ File data_lama.json tidak ditemukan! Pastikan sudah didownload dan ditaruh di folder project.');

            return;
        }

        // Baca isi file
        $jsonContent = file_get_contents($path);

        // Ubah jadi Array PHP
        $json = json_decode($jsonContent, true);

        // Validasi isinya
        if (! $json) {
            $this->error('❌ File JSON rusak atau kosong.');

            return;
        }

        $dataTransaksi = $json['data'] ?? [];
        $totalData = count($dataTransaksi);

        if ($totalData == 0) {
            $this->warn('⚠️ File JSON terbaca, tapi isinya kosong (0 data).');

            return;
        }

        $this->info("✅ Berhasil membaca file! Ditemukan {$totalData} data transaksi.");

        if (! $this->confirm('Yakin ingin mengimport data ini ke database local?', true)) {
            return;
        }

        // --- LANJUT KE PROSES SEPERTI BIASA (CODE KE BAWAH TETAP SAMA) ---
        // Ambil User Dins
        $user = User::first();
        if (! $user) {
            $this->error('❌ User belum ada! Jalankan seeder dulu.');

            return;
        }
        $userId = $user->id;

        $bar = $this->output->createProgressBar($totalData);
        $bar->start();

        $masukCount = 0;
        $skipCount = 0;

        foreach ($dataTransaksi as $item) {
            // ... (Biarkan kode foreach di bawah sini apa adanya, jangan diubah) ...
            // A. Tentukan Jenis
            $jenis = ($item['jenis'] == 1) ? 'pemasukan' : 'pengeluaran';

            // B. Handle Kategori
            $namaKategoriLama = $item['kategori_nama']['nama'] ?? 'Tanpa Kategori';
            $slugKategori = Str::slug($namaKategoriLama);

            // Cari atau Buat Kategori Baru jika belum ada
            $kategori = KategoriTransaksi::firstOrCreate(
                ['slug' => $slugKategori],
                [
                    'nama' => $namaKategoriLama,
                    'jenis' => $jenis,
                    'ikon' => null,
                ]
            );

            // C. Tebak Akun Keuangan (Logic Pintar)
            $namaKatLower = strtolower($namaKategoriLama);
            $namaAkunTarget = 'Lainnya';

            if (str_contains($namaKatLower, 'seabank')) {
                $namaAkunTarget = 'SeaBank';
            } elseif (str_contains($namaKatLower, 'dompet') || str_contains($namaKatLower, 'tunai')) {
                $namaAkunTarget = 'Dompet Fisik';
            } elseif (str_contains($namaKatLower, 'laci') || str_contains($namaKatLower, 'kas')) {
                $namaAkunTarget = 'Laci Kas';
            }

            // Cari ID Akun
            $akun = AkunKeuangan::where('nama', 'like', "%$namaAkunTarget%")->first();
            $akunId = $akun ? $akun->id : null;

            // D. Cek Duplikat
            $tanggal = Carbon::parse($item['created_at'])->format('Y-m-d');
            $exists = Transaksi::where('id_user', $userId)
                ->where('tanggal_transaksi', $tanggal)
                ->where('nominal', $item['nominal'])
                ->where('keterangan', $item['keterangan'])
                ->exists();

            if (! $exists) {
                // E. INSERT
                Transaksi::create([
                    'id_user' => $userId,
                    'id_kategori' => $kategori->id,
                    'id_akun' => $akunId,
                    'jenis' => $jenis,
                    'nominal' => $item['nominal'],
                    'keterangan' => $item['keterangan'],
                    'tanggal_transaksi' => $tanggal,
                    'created_at' => Carbon::parse($item['created_at']),
                    'updated_at' => Carbon::parse($item['updated_at']),
                    'adalah_kalibrasi' => false,
                ]);

                // Update Saldo
                if ($akun) {
                    if ($jenis == 'pemasukan') {
                        $akun->increment('saldo_saat_ini', $item['nominal']);
                    } else {
                        $akun->decrement('saldo_saat_ini', $item['nominal']);
                    }
                }

                $masukCount++;
            } else {
                $skipCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("🎉 Selesai! Sukses Import: $masukCount data. Skip Duplikat: $skipCount data.");
    }
}
