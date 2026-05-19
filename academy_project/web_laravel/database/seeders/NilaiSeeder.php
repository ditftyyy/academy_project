<?php

namespace Database\Seeders;

use App\Models\MongoDB\User;
use App\Models\MongoDB\Mapel;
use App\Models\MongoDB\Akademik;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data NILAI SISWA.
     * 
     * Di MongoDB, nilai disimpan sebagai ARRAY
     * 'academic_records' di dalam dokumen user siswa.
     * 
     * Struktur academic_records:
     * [
     *   {
     *     "tahun_ajaran": "2025/2026",
     *     "semester": "ganjil",
     *     "jenis_nilai": "UTS",
     *     "kelas_ke": "X",
     *     "nilai": [
     *       {
     *         "mapel": "Matematika",
     *         "nilai_akademik": 85,
     *         "guru": "Budi Santoso"
     *       }
     *     ],
     *     "absensi": { "sakit": 2, "izin": 1, "tanpa_keterangan": 3 }
     *   }
     * ]
     * ============================================
     */
    public function run(): void
    {
        $siswas = User::where('role', 'siswa')->get();
        $mapels = Mapel::all();
        $gurus = User::where('role', 'guru')->get();
        $akademiks = Akademik::all();

        foreach ($siswas as $siswa) {
            $academicRecords = [];

            foreach ($akademiks as $akademik) {
                // Tentukan kelas_ke berdasarkan tahun ajaran
                $tahunMasuk = $siswa->siswa_data['angkatan']['tahun_masuk'] ?? now()->year;
                $tahunAjaran = $akademik->tahun_ajaran;
                $tahunAjaranStart = explode('/', $tahunAjaran)[0];
                $selisih = $tahunAjaranStart - $tahunMasuk + 1;
                $kelasKe = ($selisih >= 3) ? 'XII' : (($selisih == 2) ? 'XI' : 'X');

                // Nilai UTS
                $nilaiUTS = [];
                foreach ($mapels as $mapel) {
                    $nilaiUTS[] = [
                        'mapel' => $mapel->nama_mapel,
                        'nilai_akademik' => random_int(50, 100),
                        'guru' => $gurus->random()->guru_data['nama'] ?? 'Guru',
                    ];
                }

                $academicRecords[] = [
                    'tahun_ajaran' => $tahunAjaran,
                    'semester' => $akademik->semester,
                    'jenis_nilai' => 'UTS',
                    'kelas_ke' => $kelasKe,
                    'nilai' => $nilaiUTS,
                    'absensi' => [
                        'sakit' => random_int(0, 5),
                        'izin' => random_int(0, 3),
                        'tanpa_keterangan' => random_int(0, 2),
                    ],
                ];

                // Nilai UAS
                $nilaiUAS = [];
                foreach ($mapels as $mapel) {
                    $nilaiUAS[] = [
                        'mapel' => $mapel->nama_mapel,
                        'nilai_akademik' => random_int(50, 100),
                        'guru' => $gurus->random()->guru_data['nama'] ?? 'Guru',
                    ];
                }

                $academicRecords[] = [
                    'tahun_ajaran' => $tahunAjaran,
                    'semester' => $akademik->semester,
                    'jenis_nilai' => 'UAS',
                    'kelas_ke' => $kelasKe,
                    'nilai' => $nilaiUAS,
                    'absensi' => [
                        'sakit' => random_int(0, 10),
                        'izin' => random_int(0, 10),
                        'tanpa_keterangan' => random_int(10, 50),
                    ],
                ];
            }

            // Simpan ke user
            $siswa->academic_records = $academicRecords;
            $siswa->save();
        }

        echo "✅ Nilai seeder selesai! (" . $siswas->count() . " siswa)\n";
    }
}