<?php

namespace Database\Seeders;

use App\Models\MongoDB\Inventaris;
use App\Models\MongoDB\Ruang;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini mengisi data BARANG INVENTARIS.
     * 
     * Di MongoDB, barang disimpan di collection
     * 'inventaris'.
     * ============================================
     */
    public function run(): void
    {
        $ruangs = Ruang::all();
        $jumlahBarangPerRuang = 4;

        foreach ($ruangs as $ruang) {
            for ($j = 0; $j < $jumlahBarangPerRuang; $j++) {
                $jumlahSeluruh = random_int(50, 100);
                $jumlahRusak = random_int(0, 10);
                $jumlahBaik = $jumlahSeluruh - $jumlahRusak;

                Inventaris::create([
                    'nama_barang' => fake('id_ID')->word(),
                    'jenis' => fake('id_ID')->randomElement(['meubel', 'elektronik', 'atk']),
                    'tahun_pengadaan' => fake()->year(),
                    'image' => null,
                    'jumlah_seluruh' => $jumlahSeluruh,
                    'jumlah_baik' => $jumlahBaik,
                    'jumlah_rusak' => $jumlahRusak,
                    'ruang' => [
                        'id' => $ruang->_id,
                        'nama' => $ruang->nama_ruang,
                    ],
                    'riwayat_peminjaman' => [],
                ]);
            }
        }

        echo "✅ Barang seeder selesai! (" . (Ruang::count() * $jumlahBarangPerRuang) . " barang)\n";
    }
}