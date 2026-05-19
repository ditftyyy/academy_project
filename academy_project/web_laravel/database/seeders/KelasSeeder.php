<?php

namespace Database\Seeders;

use App\Models\MongoDB\Kelas;
use App\Models\MongoDB\Ruang;
use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data KELAS dan RUANGAN.
     * 
     * Di MongoDB:
     * - Kelas disimpan di collection 'kelas'
     * - Ruang disimpan di collection 'ruang'
     * - Wali kelas diambil dari collection 'users'
     * ============================================
     */
    public function run(): void
    {
        $tingkats = ['X', 'XI', 'XII'];
        $jurusans = [
            'IPA' => 2,  // 2 kelas IPA
            'IPS' => 3,  // 3 kelas IPS
        ];

        $gurus = User::where('role', 'guru')->get();
        $guruIndex = 0;

        foreach ($jurusans as $jurusan => $jumlahKelas) {
            for ($i = 1; $i <= $jumlahKelas; $i++) {
                foreach ($tingkats as $tingkat) {
                    $namaKelas = strtoupper($tingkat . ' ' . $jurusan . ' ' . $i);

                    // Cek apakah kelas sudah ada
                    $exists = Kelas::where('nama_kelas', $namaKelas)->exists();

                    if (!$exists) {
                        // Ambil guru untuk wali kelas
                        $waliKelas = null;
                        if (isset($gurus[$guruIndex])) {
                            $guru = $gurus[$guruIndex];
                            $waliKelas = [
                                'id' => $guru->_id,
                                'nip' => $guru->guru_data['nip'] ?? '',
                                'nama' => $guru->guru_data['nama'] ?? $guru->nama_lengkap,
                            ];
                            $guruIndex++;
                        }

                        Kelas::create([
                            'nama_kelas' => $namaKelas,
                            'tingkat' => $tingkat,
                            'jurusan' => $jurusan,
                            'wali_kelas' => $waliKelas,
                            'jadwal' => [],
                            'siswa_ids' => [],
                            'deleted' => false,
                        ]);

                        echo "  ✅ Kelas: {$namaKelas}\n";
                    }

                    // Buat ruangan sesuai nama kelas
                    $ruangExists = Ruang::where('nama_ruang', $namaKelas)->exists();

                    if (!$ruangExists) {
                        Ruang::create([
                            'nama_ruang' => $namaKelas,
                            'luas' => random_int(10, 14),
                            'lokasi' => fake('id_ID')->word(),
                        ]);
                    }
                }
            }
        }

        echo "✅ Kelas seeder selesai!\n";
    }
}