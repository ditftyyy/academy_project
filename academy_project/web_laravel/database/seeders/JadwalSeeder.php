<?php

namespace Database\Seeders;

use App\Models\MongoDB\Kelas;
use App\Models\MongoDB\User;
use App\Models\MongoDB\Mapel;
use App\Models\MongoDB\Ruang;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
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
                for ($hour = 0; $hour < 4; $hour++) {
                    $start = Carbon::createFromTime(7 + $hour, 0, 0);
                    $end = $start->copy()->addMinutes(45);
                    $guru = $gurus->random();
                    $mapel = $mapels->random();
                    $ruang = $ruangs->random();
                    $mataPelajaran[] = [
                        'jam_mulai' => $start->format('H:i'),
                        'jam_selesai' => $end->format('H:i'),
                        'mapel' => $mapel->nama_mapel,
                        'guru' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                        'ruang' => $ruang->nama_ruang,
                        'mapel_id' => $mapel->_id,
                        'guru_id' => $guru->_id,
                        'ruang_id' => $ruang->_id,
                    ];
                }
                $jadwal[] = [
                    'hari' => $day,
                    'status' => 'masuk',
                    'mata_pelajaran' => $mataPelajaran,
                ];
            }
            $kelas->jadwal = $jadwal;
            $kelas->save();
        }
        echo "✅ JadwalSeeder selesai.\n";
    }
}