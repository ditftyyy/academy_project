<?php

namespace Database\Seeders;

use App\Models\MongoDB\Kelas;
use App\Models\MongoDB\Ruang;
use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = [
            'IPA' => 2,  // 2 kelas IPA per tingkat
            'IPS' => 3,  // 3 kelas IPS per tingkat
        ];

        $gurus = User::where('role', 'guru')->get();
        $guruIndex = 0;

        foreach ($jurusans as $jurusan => $jumlahKelas) {
            for ($i = 1; $i <= $jumlahKelas; $i++) {
                foreach ($tingkats as $tingkat) {
                    $namaKelas = strtoupper($tingkat . ' ' . $jurusan . ' ' . $i);

                    // Cek apakah kelas sudah ada (case-insensitive)
                    $kelasExist = Kelas::whereRaw([
                        '$expr' => [
                            '$eq' => [
                                ['$toLower' => '$nama_kelas'],
                                strtolower($namaKelas)
                            ]
                        ]
                    ])->first();

                    if (!$kelasExist) {
                        // Ambil guru untuk wali kelas (rotasi)
                        $waliKelas = null;
                        if ($gurus->count() > 0 && isset($gurus[$guruIndex % $gurus->count()])) {
                            $guru = $gurus[$guruIndex % $gurus->count()];
                            $waliKelas = [
                                'id' => $guru->_id,
                                'nip' => $guru->guru_data['nip'] ?? '',
                                'nama' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                            ];
                            $guruIndex++;
                        }

                        // Generate jadwal kosong
                        $jadwal = [];
                        foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari) {
                            $jadwal[] = [
                                'hari' => $hari,
                                'status' => 'libur',
                                'mata_pelajaran' => [],
                            ];
                        }

                        Kelas::create([
                            'nama_kelas' => $namaKelas,
                            'tingkat' => $tingkat,
                            'jurusan' => $jurusan,
                            'wali_kelas' => $waliKelas,
                            'jadwal' => $jadwal,
                            'siswa_ids' => [],
                            'deleted' => false,
                        ]);

                        echo "✅ Kelas: {$namaKelas} dengan wali " . ($waliKelas['nama'] ?? '-') . "\n";
                    } else {
                        echo "⏭️ Kelas {$namaKelas} sudah ada, dilewati.\n";
                    }

                    // Buat ruangan sesuai nama kelas (jika belum ada)
                    $ruangExist = Ruang::where('nama_ruang', $namaKelas)->exists();
                    if (!$ruangExist) {
                        Ruang::create([
                            'nama_ruang' => $namaKelas,
                            'luas' => random_int(10, 14),
                            'lokasi' => fake('id_ID')->word(),
                        ]);
                        echo "  ✅ Ruang {$namaKelas} dibuat.\n";
                    }
                }
            }
        }

        echo "✅ Kelas seeder selesai!\n";
    }
}