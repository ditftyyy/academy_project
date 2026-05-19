<?php

namespace Database\Seeders;

use App\Models\MongoDB\Kelas;
use App\Models\MongoDB\Akademik;
use Illuminate\Database\Seeder;

class JadwalSeeder2 extends Seeder
{
    /**
     * ============================================
     * Seeder ini membuat JADWAL KOSONG (libur)
     * untuk semua kelas. Digunakan jika ingin
     * mereset jadwal.
     * ============================================
     */
    public function run(): void
    {
        $kelass = Kelas::where('deleted', false)->get();
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        foreach ($kelass as $kelas) {
            $jadwal = [];

            foreach ($days as $day) {
                $jadwal[] = [
                    'hari' => $day,
                    'status' => 'libur',
                    'mata_pelajaran' => [],
                ];
            }

            $kelas->jadwal = $jadwal;
            $kelas->save();
        }

        echo "✅ Jadwal kosong seeder selesai! (" . $kelass->count() . " kelas)\n";
    }
}