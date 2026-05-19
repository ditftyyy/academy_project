<?php

namespace Database\Seeders;

use App\Models\MongoDB\User;
use App\Models\MongoDB\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat USER GURU & SISWA.
     * 
     * Di MongoDB, SEMUA user disimpan di
     * SATU collection 'users' dengan field
     * berbeda untuk setiap role:
     * 
     * - Guru: field 'guru_data'
     * - Siswa: field 'siswa_data'
     * 
     * Semua info (profile, kelas, ortu) disimpan
     * sebagai embedded document di 1 dokumen user.
     * ============================================
     */
    public function run(): void
    {
        // ========== BUAT GURU ==========
        $jumlahGuru = 20;
        $guruIds = [];

        for ($i = 0; $i < $jumlahGuru; $i++) {
            $nip = random_int(10000, 59999) . '' . random_int(10000, 59999);
            $nama = fake('id_ID')->name();

            $guru = User::create([
                'username' => fake('id_ID')->unique()->userName(),
                'email' => fake('id_ID')->unique()->email(),
                'password' => Hash::make('guru'),
                'role' => 'guru',
                'current_role' => 'guru',
                'deleted' => false,
                'is_online' => false,
                'profile' => [
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => fake('id_ID')->randomElement(['laki-laki', 'perempuan']),
                    'agama' => fake('id_ID')->randomElement(['islam', 'kristen', 'hindu', 'konghucu', 'buddha']),
                    'alamat' => fake('id_ID')->address(),
                    'foto' => 'default_img.png',
                ],
                'guru_data' => [
                    'nip' => $nip,
                    'nama' => $nama,
                    'no_telp' => fake('id_ID')->phoneNumber(),
                    'jenis_kelamin' => fake('id_ID')->randomElement(['laki-laki', 'perempuan']),
                    'agama' => fake('id_ID')->randomElement(['islam', 'kristen', 'hindu', 'konghucu', 'buddha']),
                    'status_pegawai' => fake()->randomElement(['tetap', 'honorer', 'magang']),
                    'tempat_lahir' => fake('id_ID')->city(),
                    'tanggal_lahir' => fake('id_ID')->date('Y-m-d', 'now'),
                    'foto' => 'default_img.png',
                    'signature' => 'default_signature.png',
                    'alamat' => fake('id_ID')->address(),
                ],
                'attendances' => [],
                'schedule' => [],
            ]);

            $guruIds[] = $guru->_id;
        }

        echo "  ✅ {$jumlahGuru} guru dibuat\n";

        // ========== BUAT KELAS & RUANG ==========
        $this->call(KelasSeeder::class);

        // ========== BUAT SISWA ==========
        $kelass = Kelas::where('deleted', false)->get();
        $siswaPerKelas = 2;
        $angkatanTahun = now()->year;
        $siswaCount = 0;

        for ($a = 0; $a < 4; $a++) { // 4 angkatan
            $tahunMasuk = $angkatanTahun - $a;
            $angkatanNama = 'Angkatan ' . $tahunMasuk;

            foreach ($kelass as $kelas) {
                for ($i = 0; $i < $siswaPerKelas; $i++) {
                    $nis = random_int(60000, 99999) . '' . random_int(100, 999);
                    $nama = fake('id_ID')->name();
                    $ayah = fake('id_ID')->name('male');

                    User::create([
                        'username' => fake('id_ID')->unique()->userName(),
                        'email' => fake('id_ID')->unique()->email(),
                        'password' => Hash::make('siswa'),
                        'role' => 'siswa',
                        'current_role' => 'siswa',
                        'deleted' => false,
                        'is_online' => false,
                        'profile' => [
                            'nama_lengkap' => $nama,
                            'jenis_kelamin' => fake('id_ID')->randomElement(['laki-laki', 'perempuan']),
                            'agama' => fake('id_ID')->randomElement(['islam', 'kristen', 'hindu', 'buddha', 'konghucu']),
                            'alamat' => fake('id_ID')->address(),
                            'no_telp' => fake('id_ID')->phoneNumber(),
                            'foto' => 'default_img.png',
                        ],
                        'siswa_data' => [
                            'nis' => $nis,
                            'nisn' => random_int(60000, 99999) . '' . random_int(60000, 99999),
                            'nik' => random_int(90000, 99999) . '' . random_int(90000, 99999) . '' . random_int(1, 1000),
                            'nama' => $nama,
                            'tempat_lahir' => fake('id_ID')->city(),
                            'tanggal_lahir' => fake('id_ID')->date(),
                            'jenis_kelamin' => fake('id_ID')->randomElement(['laki-laki', 'perempuan']),
                            'agama' => fake('id_ID')->randomElement(['islam', 'kristen', 'hindu', 'buddha', 'konghucu']),
                            'no_telp' => fake('id_ID')->phoneNumber(),
                            'alamat' => fake('id_ID')->address(),
                            'foto' => 'default_img.png',
                            'status' => fake('id_ID')->randomElement(['bukan pindahan', 'pindahan']),
                            'orang_tua' => [
                                'nama_ayah' => $ayah,
                                'nama_ibu' => fake('id_ID')->name('female'),
                                'nama_wali' => $ayah,
                            ],
                            'kelas' => [
                                'id' => $kelas->_id,
                                'nama' => $kelas->nama_kelas,
                            ],
                            'angkatan' => [
                                'nama' => $angkatanNama,
                                'tahun_masuk' => $tahunMasuk,
                            ],
                            'asal_sekolah' => null,
                            'tanggal_keluar' => null,
                        ],
                        'academic_records' => [],
                        'attendances' => [],
                        'schedule' => [],
                    ]);

                    $siswaCount++;
                }
            }
        }

        echo "  ✅ {$siswaCount} siswa dibuat\n";

        // ========== BUAT GURU DEMO ==========
        $guruDemoExists = User::where('username', 'guru')->exists();

        if (!$guruDemoExists) {
            User::create([
                'username' => 'guru',
                'email' => 'guru@academy.id',
                'password' => Hash::make('guru'),
                'role' => 'guru',
                'current_role' => 'guru',
                'deleted' => false,
                'is_online' => false,
                'profile' => [
                    'nama_lengkap' => 'Guru Demo',
                    'jenis_kelamin' => 'laki-laki',
                    'agama' => 'islam',
                ],
                'guru_data' => [
                    'nip' => '199001012020011001',
                    'nama' => 'Guru Demo',
                    'status_pegawai' => 'tetap',
                ],
                'attendances' => [],
                'schedule' => [],
            ]);

            echo "  ✅ Guru demo: guru / guru\n";
        }

        // ========== BUAT SISWA DEMO ==========
        $siswaDemoExists = User::where('username', 'siswa')->exists();

        if (!$siswaDemoExists) {
            User::create([
                'username' => 'siswa',
                'email' => 'siswa@academy.id',
                'password' => Hash::make('siswa'),
                'role' => 'siswa',
                'current_role' => 'siswa',
                'deleted' => false,
                'is_online' => false,
                'profile' => [
                    'nama_lengkap' => 'Siswa Demo',
                    'jenis_kelamin' => 'laki-laki',
                    'agama' => 'islam',
                ],
                'siswa_data' => [
                    'nis' => '2024001',
                    'nisn' => '0001234567',
                    'nama' => 'Siswa Demo',
                    'kelas' => ['id' => '1', 'nama' => 'X IPA 1'],
                    'status' => 'bukan pindahan',
                ],
                'academic_records' => [],
                'attendances' => [],
                'schedule' => [],
            ]);

            echo "  ✅ Siswa demo: siswa / siswa\n";
        }

        // ========== UPDATE SISWA LULUS ==========
        // Siswa yang tahun masuknya >= 3 tahun lalu, status jadi 'lulus'
        $siswasLama = User::where('role', 'siswa')
            ->where('siswa_data.angkatan.tahun_masuk', '<=', now()->year - 3)
            ->get();

        foreach ($siswasLama as $s) {
            $siswaData = $s->siswa_data;
            $siswaData['status'] = 'lulus';
            $s->siswa_data = $siswaData;
            $s->save();
        }

        echo "  ✅ " . $siswasLama->count() . " siswa diupdate menjadi lulus\n";
        echo "✅ User seeder selesai!\n";
    }
}