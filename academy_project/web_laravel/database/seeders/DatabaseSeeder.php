<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Ini adalah SEEDER UTAMA yang memanggil
     * semua seeder lainnya.
     * 
     * URUTAN PENTING:
     * 1. AdminSeeder (harus ada user dulu)
     * 2. MapelSeeder (data referensi)
     * 3. AngkatanSeeder (tahun ajaran)
     * 4. UserSeeder (guru & siswa)
     * 5. AbsensisSeeder (absensi)
     * 6. JadwalSeeder (jadwal pelajaran)
     * 7. BarangSeeder (inventaris)
     * 8. PeminjamanSeeder (peminjaman ruangan)
     * 9. KeteranganabsensiSeeder (event kalender)
     * ============================================
     */
    public function run(): void
    {
        echo "\n";
        echo "========================================\n";
        echo "  MONGODB SEEDER - ACADEMY+\n";
        echo "========================================\n\n";

        // 1. Admin
        echo "[1/7] Membuat admin...\n";
        $this->call(AdminSeeder::class);

        // 2. Mapel
        echo "[2/7] Membuat mata pelajaran...\n";
        $this->call(MapelSeeder::class);

        // 3. Angkatan & Tahun Ajaran
        echo "[3/7] Membuat angkatan & tahun ajaran...\n";
        $this->call(AngkatanSeeder::class);

        // 4. User (Guru & Siswa) + Kelas + Ruang
        echo "[4/7] Membuat user (guru & siswa)...\n";
        $this->call(UserSeeder::class);

        // 5. Absensi
        echo "[5/7] Membuat data absensi...\n";
        $this->call(AbsensisSeeder::class);

        // 6. Jadwal
        echo "[6/7] Membuat jadwal...\n";
        $this->call(JadwalSeeder::class);

        // 7. Barang & Peminjaman
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