<?php

namespace Database\Seeders;

use App\Models\MongoDB\Mapel;
use Illuminate\Database\Seeder;

class MapelSeeder extends Seeder
{
    /**
     * ============================================
     * Seeder ini membuat MATA PELAJARAN.
     * 
     * Di MongoDB, mapel disimpan di collection
     * 'mata_pelajaran'.
     * ============================================
     */
    public function run(): void
    {
        $mapels = [
            'Bahasa Inggris',
            'Bahasa Indonesia',
            'Matematika',
            'PKN',
            'IPS',
            'BIOLOGI',
            'KIMIA',
            'FISIKA',
        ];

        foreach ($mapels as $namaMapel) {
            // Cek apakah sudah ada
            $exists = Mapel::where('nama_mapel', $namaMapel)->exists();

            if (!$exists) {
                Mapel::create([
                    'nama_mapel' => $namaMapel,
                    'guru_pengajar_ids' => [],
                ]);
                echo "  ✅ Mapel: {$namaMapel}\n";
            }
        }

        echo "✅ Mapel seeder selesai! (" . count($mapels) . " mapel)\n";
    }
}