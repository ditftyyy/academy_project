<?php

namespace Database\Seeders;

use App\Models\MongoDB\Kelas;
use App\Models\MongoDB\User;
use App\Models\MongoDB\Mapel;
use App\Models\MongoDB\Ruang;
use App\Models\MongoDB\Akademik;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat JADWAL PELAJARAN.
     * 
     * Di MongoDB, jadwal disimpan sebagai ARRAY
     * 'jadwal' di dalam dokumen kelas.
     * 
     * Struktur jadwal:
     * [
     *   {
     *     "hari": "senin",
     *     "status": "masuk",
     *     "mata_pelajaran": [
     *       {
     *         "jam_mulai": "07:00",
     *         "jam_selesai": "07:45",
     *         "mapel": "Matematika",
     *         "guru": "Budi Santoso",
     *         "ruang": "X IPA 1"
     *       }
     *     ]
     *   }
     * ]
     * ============================================
     */
    public function run(): void
    {
        $mapels = Mapel::all();
        $ruangs = Ruang::all();
        $gurus = User::where('role', 'guru')->get();
        $kelass = Kelas::where('deleted', false)->get();

        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

        foreach ($kelass as $kelas) {
            $jadwal = [];

            foreach ($days as $day) {
                $mataPelajaran = [];

                // 4 jam pelajaran per hari
                for ($hour = 0; $hour < 4; $hour++) {
                    $startTime = Carbon::createFromTime(7 + $hour, 0, 0);
                    $endTime = $startTime->copy()->addMinutes(45);

                    $guru = $gurus->random();
                    $mapel = $mapels->random();
                    $ruang = $ruangs->random();

                    $mataPelajaran[] = [
                        'jam_mulai' => $startTime->format('H:i'),
                        'jam_selesai' => $endTime->format('H:i'),
                        'mapel' => $mapel->nama_mapel,
                        'guru' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                        'ruang' => $ruang->nama_ruang,
                        'guru_id' => $guru->_id,
                        'mapel_id' => $mapel->_id,
                    ];
                }

                $jadwal[] = [
                    'hari' => $day,
                    'status' => 'masuk',
                    'mata_pelajaran' => $mataPelajaran,
                ];
            }

            // Simpan jadwal ke kelas
            $kelas->jadwal = $jadwal;
            $kelas->save();
        }

        echo "✅ Jadwal seeder selesai! (" . $kelass->count() . " kelas)\n";
    }
}