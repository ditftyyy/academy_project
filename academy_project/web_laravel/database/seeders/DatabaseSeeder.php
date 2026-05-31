<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n";
        echo "========================================\n";
        echo "  MONGODB SEEDER - ACADEMY+\n";
        echo "========================================\n\n";

        echo "[1/7] Membuat admin...\n";
        $this->call(AdminSeeder::class);

        echo "[2/7] Membuat mata pelajaran...\n";
        $this->call(MapelSeeder::class);

        echo "[3/7] Membuat angkatan & tahun ajaran...\n";
        $this->call(AngkatanSeeder::class);

        echo "[4/7] Membuat user (guru & siswa)...\n";
        $this->call(UserSeeder::class);

        echo "[5/7] Membuat data absensi...\n";
        $this->call(AbsensisSeeder::class);

        echo "[6/7] Membuat jadwal...\n";
        $this->call(JadwalSeeder::class);

        echo "[7/7] Membuat data barang & peminjaman...\n";
        $this->call(BarangSeeder::class);
        $this->call(PeminjamanSeeder::class);

        echo "\n========================================\n";
        echo "  ✅ SEMUA SEEDER SELESAI!\n";
        echo "========================================\n";
        echo "\n";
        echo "Akun demo:\n";
        echo "  Admin : admin / admin\n";
        echo "  Guru  : guru / guru\n";
        echo "  Siswa : siswa / siswa\n";
        echo "\n";
    }
}