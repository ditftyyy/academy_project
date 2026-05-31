<?php

namespace Database\Seeders;

use App\Models\MongoDB\User;
use App\Models\MongoDB\Kelas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // untuk slug

class UserSeeder extends Seeder
{
    /**
     * Generate username unik dari nama
     */
    private function generateUsername($nama)
    {
        $base = Str::slug($nama, '.');
        if (empty($base)) $base = 'siswa';
        $username = $base;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        return $username;
    }

    public function run(): void
    {
        // ========== BUAT ADMIN ROOT ==========
        if (!User::where('username', 'root')->exists()) {
            User::create([
                'username' => 'root',
                'email' => 'root@academy.id',
                'password' => Hash::make('root'),
                'role' => 'root,admin',
                'current_role' => 'admin',
                'deleted' => false,
                'is_online' => false,
                'profile' => ['nama_lengkap' => 'Super Admin'],
                'attendances' => [],
                'schedule' => [],
            ]);
            echo "  ✅ Root user created\n";
        }

        // ========== BUAT ADMIN BIASA ==========
        if (!User::where('username', 'admin')->exists()) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@academy.id',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'current_role' => 'admin',
                'deleted' => false,
                'profile' => ['nama_lengkap' => 'Administrator'],
                'attendances' => [],
                'schedule' => [],
            ]);
            echo "  ✅ Admin user created\n";
        }

        // ========== BUAT GURU DEMO ==========
        if (!User::where('username', 'guru')->exists()) {
            User::create([
                'username' => 'guru',
                'email' => 'guru@academy.id',
                'password' => Hash::make('guru'),
                'role' => 'guru',
                'current_role' => 'guru',
                'deleted' => false,
                'profile' => ['nama_lengkap' => 'Guru Demo'],
                'guru_data' => ['nip' => '199001012020011001', 'nama' => 'Guru Demo'],
                'attendances' => [],
                'schedule' => [],
            ]);
            echo "  ✅ Guru demo created\n";
        }

        // ========== BUAT SISWA DEMO ==========
        if (!User::where('username', 'siswa.demo')->exists()) {
            $kelas = Kelas::first();
            if (!$kelas) {
                $kelas = Kelas::create([
                    'nama_kelas' => 'X IPA 1',
                    'tingkat' => 'X',
                    'jurusan' => 'IPA',
                    'deleted' => false,
                ]);
            }

            $nama = 'Siswa Demo';
            $username = $this->generateUsername($nama); // "siswa.demo"

            User::create([
                'username' => $username,
                'email' => $username . '@student.academy.id',
                'password' => Hash::make('siswa'),
                'role' => 'siswa',
                'current_role' => 'siswa',
                'deleted' => false,
                'profile' => ['nama_lengkap' => $nama],
                'siswa_data' => [
                    'nis' => '2024001',
                    'nisn' => '1234567890',
                    'nik' => '1234567890123456',
                    'nama' => $nama,
                    'jenis_kelamin' => 'laki-laki',
                    'tempat_lahir' => 'Kota',
                    'tanggal_lahir' => '2000-01-01',
                    'agama' => 'islam',
                    'no_telp' => '081234567890',
                    'alamat' => 'Jl. Contoh No.1',
                    'foto' => 'default_img.png',
                    'status' => 'bukan pindahan',
                    'orang_tua' => [
                        'nama_ayah' => 'Ayah Demo',
                        'nama_ibu' => 'Ibu Demo',
                        'nama_wali' => 'Wali Demo',
                    ],
                    'kelas' => [
                        'id' => $kelas->_id,
                        'nama' => $kelas->nama_kelas,
                    ],
                    'angkatan' => [
                        'nama' => 'Angkatan ' . now()->year,
                        'tahun_masuk' => now()->year,
                    ],
                    'tanggal_masuk' => now()->format('Y-m-d'),
                ],
                'attendances' => [],
                'schedule' => [],
            ]);
            echo "  ✅ Siswa demo created with username: $username\n";
        }

        // ========== BUAT GURU ACAK (5 orang) ==========
        for ($i = 1; $i <= 5; $i++) {
            $username = "guru$i";
            if (!User::where('username', $username)->exists()) {
                User::create([
                    'username' => $username,
                    'email' => "$username@academy.id",
                    'password' => Hash::make('guru'),
                    'role' => 'guru',
                    'current_role' => 'guru',
                    'profile' => ['nama_lengkap' => "Guru $i"],
                    'guru_data' => ['nip' => "G$i", 'nama' => "Guru $i"],
                    'attendances' => [],
                    'schedule' => [],
                ]);
            }
        }
        echo "  ✅ 5 guru tambahan siap\n";

        // ========== BUAT SISWA ACAK (10 orang) dengan username dari nama ==========
        $kelas = Kelas::first();
        if (!$kelas) {
            $kelas = Kelas::create([
                'nama_kelas' => 'X IPA 1',
                'tingkat' => 'X',
                'jurusan' => 'IPA',
                'deleted' => false,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $nama = "Siswa $i";
            $username = $this->generateUsername($nama);
            if (!User::where('username', $username)->exists()) {
                User::create([
                    'username' => $username,
                    'email' => $username . '@student.academy.id',
                    'password' => Hash::make('siswa'),
                    'role' => 'siswa',
                    'current_role' => 'siswa',
                    'profile' => ['nama_lengkap' => $nama],
                    'siswa_data' => [
                        'nis' => "S$i",
                        'nisn' => "12345$i",
                        'nik' => "1234567890$i",
                        'nama' => $nama,
                        'jenis_kelamin' => $i % 2 == 0 ? 'perempuan' : 'laki-laki',
                        'tempat_lahir' => 'Jakarta',
                        'tanggal_lahir' => '2000-01-01',
                        'agama' => 'islam',
                        'no_telp' => '0812345678' . $i,
                        'alamat' => 'Jl. Contoh No.' . $i,
                        'foto' => 'default_img.png',
                        'status' => 'bukan pindahan',
                        'orang_tua' => [
                            'nama_ayah' => "Ayah $i",
                            'nama_ibu' => "Ibu $i",
                            'nama_wali' => "Wali $i",
                        ],
                        'kelas' => [
                            'id' => $kelas->_id,
                            'nama' => $kelas->nama_kelas,
                        ],
                        'angkatan' => [
                            'nama' => 'Angkatan ' . now()->year,
                            'tahun_masuk' => now()->year,
                        ],
                        'tanggal_masuk' => now()->format('Y-m-d'),
                    ],
                    'attendances' => [],
                    'schedule' => [],
                ]);
                echo "  ✅ Siswa $username ($nama) created\n";
            }
        }
        echo "  ✅ 10 siswa tambahan siap\n";

        echo "✅ UserSeeder selesai!\n";
    }
}