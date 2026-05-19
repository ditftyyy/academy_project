<?php

namespace Database\Seeders;

use App\Models\MongoDB\Ruang;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data PEMINJAMAN RUANGAN.
     * 
     * Di MongoDB, peminjaman disimpan sebagai ARRAY
     * 'peminjaman' di dalam dokumen ruang
     * di collection 'ruang'.
     * ============================================
     */
    public function run(): void
    {
        $ruangs = Ruang::all()->take(5); // Ambil 5 ruang pertama

        foreach ($ruangs as $ruang) {
            $peminjaman = [];

            // Generate 2 peminjaman per ruang
            for ($i = 0; $i < 2; $i++) {
                $tanggalPinjam = fake()->dateTimeBetween('-1 month', '+1 month');
                $tanggalKembali = (clone $tanggalPinjam)->modify('+' . random_int(2, 10) . ' days');

                $peminjaman[] = [
                    '_id' => (string) new \MongoDB\BSON\ObjectId(),
                    'nama_peminjam' => fake('id_ID')->name(),
                    'tanggal_pinjam' => $tanggalPinjam->format('Y-m-d'),
                    'tanggal_kembali' => $tanggalKembali->format('Y-m-d'),
                    'surat' => null,
                    'status' => fake()->randomElement(['dipinjam', 'dikembalikan']),
                    'created_at' => now()->toDateTimeString(),
                ];
            }

            $ruang->peminjaman = $peminjaman;
            $ruang->save();
        }

        echo "✅ Peminjaman seeder selesai! (" . $ruangs->count() . " ruang)\n";
    }
}