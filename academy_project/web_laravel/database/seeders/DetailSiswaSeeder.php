<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DetailSiswaSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN: Seeder ini tidak digunakan lagi.
     * 
     * Di MongoDB, detail siswa sudah menjadi
     * bagian dari field 'siswa_data' di dokumen
     * user. Tidak perlu collection terpisah.
     * ============================================
     */
    public function run(): void
    {
        // Tidak diperlukan di MongoDB
        echo "⏭️ DetailSiswaSeeder: Skip (tidak diperlukan di MongoDB)\n";
    }
}