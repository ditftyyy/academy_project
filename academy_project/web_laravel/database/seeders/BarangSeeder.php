<?php

namespace Database\Seeders;

use App\Models\MongoDB\Inventaris;
use App\Models\MongoDB\Ruang;
use Illuminate\Database\Seeder;

/**
 * ============================================
 * SEEDER UNTUK DATA BARANG INVENTARIS
 * ============================================
 * 
 * Fungsi: Mengisi collection 'inventaris' dengan data contoh.
 * 
 * Cara menjalankan:
 * php artisan db:seed --class=BarangSeeder
 * 
 * Catatan: Seeder ini membutuhkan data Ruang terlebih dahulu.
 *          Jika belum ada, jalankan seeder Ruang terlebih dulu.
 */
class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data ruangan dari collection 'ruangs'
        $ruangs = Ruang::all();
        
        // Jumlah barang yang akan dibuat per ruangan
        $jumlahBarangPerRuang = 4;

        // Loop setiap ruangan
        foreach ($ruangs as $ruang) {
            for ($j = 0; $j < $jumlahBarangPerRuang; $j++) {
                // Generate data acak yang realistis
                $jumlahSeluruh = random_int(50, 100);   // total barang antara 50-100
                $jumlahRusak = random_int(0, 10);       // rusak antara 0-10
                $jumlahBaik = $jumlahSeluruh - $jumlahRusak;

                // Simpan ke MongoDB
                Inventaris::create([
                    'nama_barang' => fake('id_ID')->word(),         // nama acak dalam bahasa Indonesia
                    'jenis' => fake('id_ID')->randomElement(['meubel', 'elektronik', 'atk']),
                    'tahun_pengadaan' => fake()->year(),            // tahun acak
                    // TIDAK ADA FIELD 'image' LAGI
                    'jumlah_seluruh' => $jumlahSeluruh,
                    'jumlah_baik' => $jumlahBaik,
                    'jumlah_rusak' => $jumlahRusak,
                    'ruang' => [
                        'id' => $ruang->_id,
                        'nama' => $ruang->nama_ruang,
                    ],
                    'riwayat_peminjaman' => [],                      // array kosong
                ]);
            }
        }

        // Tampilkan pesan di console
        $totalBarang = Ruang::count() * $jumlahBarangPerRuang;
        echo "✅ Barang seeder selesai! ($totalBarang barang telah ditambahkan)\n";
    }
}