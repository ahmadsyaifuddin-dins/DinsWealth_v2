<?php

namespace App\Console\Commands;

use App\Models\AkunKeuangan;
use App\Models\KategoriTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportDinsLegacy extends Command
{
    protected $signature = 'dins:import-legacy';

    protected $description = 'Import data dari file JSON manual ke Supabase';

    public function handle()
    {
        // --- PERUBAHAN UTAMA DI SINI ---
        // Kita tidak lagi menembak URL, tapi membaca file di folder project
        $path = base_path('data_lama.json');

        $this->info("📂 Membaca file lokal dari: $path");

        // 1. Cek Apakah File Ada?
        if (! file_exists($path)) {
            $this->error('❌ File data_lama.json tidak ditemukan! Pastikan sudah didownload dan ditaruh di folder project (sejajar dengan .env).');

            return;
        }

        // 2. Baca Isi File
        try {
            $jsonContent = file_get_contents($path);
            $json = json_decode($jsonContent, true);

            if (! $json) {
                $this->error('❌ File JSON rusak atau formatnya salah.');

                return;
            }

            $dataTransaksi = $json['data'] ?? [];
            $totalData = count($dataTransaksi);

            if ($totalData == 0) {
                $this->warn('⚠️ File JSON terbaca, tapi isinya kosong (0 data).');

                return;
            }

            $this->info("✅ Berhasil membaca file! Ditemukan {$totalData} data transaksi.");

            // Konfirmasi User
            if (! $this->confirm('Yakin ingin mengimport data ini ke Supabase? (Data duplikat akan diskip)', true)) {
                return;
            }

            // 3. Persiapan User (Admin)
            $user = User::first();
            if (! $user) {
                $this->error('❌ User belum ada! Jalankan seeder dulu: php artisan db:seed');

                return;
            }
            $userId = $user->id;

            // Progress Bar
            $bar = $this->output->createProgressBar($totalData);
            $bar->start();

            $masukCount = 0;
            $skipCount = 0;

            // 4. Looping Data
            foreach ($dataTransaksi as $item) {
                // A. Tentukan Jenis
                $jenis = ($item['jenis'] == 1) ? 'pemasukan' : 'pengeluaran';

                // B. Handle Kategori
                $namaKategoriLama = $item['kategori_nama']['nama'] ?? 'Tanpa Kategori';
                $slugKategori = Str::slug($namaKategoriLama);

                $kategori = KategoriTransaksi::firstOrCreate(
                    ['slug' => $slugKategori],
                    [
                        'nama' => $namaKategoriLama,
                        'jenis' => $jenis,
                        'ikon' => null,
                    ]
                );

                // C. Tebak Akun Keuangan
                $namaKatLower = strtolower($namaKategoriLama);
                $namaAkunTarget = 'Lainnya'; // Default

                if (str_contains($namaKatLower, 'seabank')) {
                    $namaAkunTarget = 'SeaBank';
                } elseif (str_contains($namaKatLower, 'dompet') || str_contains($namaKatLower, 'tunai')) {
                    $namaAkunTarget = 'Dompet Fisik';
                } elseif (str_contains($namaKatLower, 'laci') || str_contains($namaKatLower, 'kas')) {
                    $namaAkunTarget = 'Laci Kas';
                }

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
                    // E. INSERT ke Supabase
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

                    // Update Saldo (Opsional)
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

        } catch (\Exception $e) {
            $this->error('❌ Terjadi Error: '.$e->getMessage());
        }
    }
}
