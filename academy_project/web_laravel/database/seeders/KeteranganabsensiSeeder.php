<?php

namespace Database\Seeders;

use App\Models\MongoDB\Pengumuman;
use Illuminate\Database\Seeder;

class KeteranganabsensiSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data WEEKEND (Sabtu-Minggu)
     * untuk kalender akademik.
     * 
     * Di MongoDB, data ini disimpan di collection
     * 'pengumuman' dengan type 'keterangan_absensi'.
     * ============================================
     */
    public function run(): void
    {
        $startDate = '2023-01-01';
        $endDate = '2024-12-31';
        $currentDate = $startDate;

        $count = 0;

        while ($currentDate < $endDate) {
            $dayOfWeek = date('N', strtotime($currentDate));

            // Sabtu (6) atau Minggu (7)
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                Pengumuman::create([
                    'title' => 'Akhir Pekan',
                    'message' => 'akhir pekan',
                    'role' => 'semua',
                    'type' => 'keterangan_absensi',
                    'data_tambahan' => [
                        'tanggal' => $currentDate,
                        'status' => 'weekend',
                        'keterangan' => 'akhir pekan',
                    ],
                    'created_by' => null,
                ]);
                $count++;
            }

            $currentDate = date('Y-m-d', strtotime($currentDate . ' + 1 day'));
        }

        echo "✅ Keterangan absensi seeder selesai! ({$count} weekend events)\n";
    }
}