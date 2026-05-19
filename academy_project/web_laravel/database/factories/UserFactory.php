<?php

namespace Database\Factories;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MongoDB\User>
 */
class UserFactory extends Factory
{
    /**
     * Nama model yang digunakan factory ini.
     *
     * @var string
     */
    protected $model = MongoUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username'       => $this->faker->unique()->userName(),
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role'           => $this->faker->randomElement(['siswa', 'guru']),
            'current_role'   => $this->faker->randomElement(['siswa', 'guru']),
            'remember_token' => Str::random(20),
            'deleted'        => false,
            'is_online'      => false,

            // Profile minimal (semua role punya)
            'profile' => [
                'nama_lengkap'  => $this->faker->name(),
                'jenis_kelamin' => $this->faker->randomElement(['laki-laki', 'perempuan']),
                'agama'         => $this->faker->randomElement(['islam', 'kristen', 'hindu', 'buddha', 'konghucu']),
                'no_telp'       => $this->faker->phoneNumber(),
                'alamat'        => $this->faker->address(),
                'foto'          => 'default_img.png',
            ],

            // Data guru (akan diisi hanya jika role = guru)
            'guru_data' => null,

            // Data siswa (akan diisi hanya jika role = siswa)
            'siswa_data' => null,

            // Array‑array kosong sebagai default
            'academic_records' => [],
            'attendances'      => [],
            'schedule'         => [],
        ];
    }

    /**
     * State untuk user dengan role guru.
     */
    public function guru(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'         => 'guru',
            'current_role' => 'guru',
            'guru_data'    => [
                'nip'            => $this->faker->unique()->numerify('19########2020##'),
                'nama'           => $attributes['profile']['nama_lengkap'] ?? $this->faker->name(),
                'no_telp'        => $attributes['profile']['no_telp'] ?? $this->faker->phoneNumber(),
                'jenis_kelamin'  => $attributes['profile']['jenis_kelamin'] ?? 'laki-laki',
                'agama'          => $attributes['profile']['agama'] ?? 'islam',
                'status_pegawai' => $this->faker->randomElement(['tetap', 'honorer', 'magang']),
                'tempat_lahir'   => $this->faker->city(),
                'tanggal_lahir'  => $this->faker->date('Y-m-d', '2000-01-01'),
                'foto'           => $attributes['profile']['foto'] ?? 'default_img.png',
                'signature'      => 'default_signature.png',
                'alamat'         => $attributes['profile']['alamat'] ?? $this->faker->address(),
            ],
            'siswa_data' => null,
        ]);
    }

    /**
     * State untuk user dengan role siswa.
     */
    public function siswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role'         => 'siswa',
            'current_role' => 'siswa',
            'siswa_data'   => [
                'nis'           => $this->faker->unique()->numerify('2024####'),
                'nisn'          => $this->faker->unique()->numerify('000######'),
                'nik'           => $this->faker->unique()->numerify('################'),
                'nama'          => $attributes['profile']['nama_lengkap'] ?? $this->faker->name(),
                'tempat_lahir'  => $this->faker->city(),
                'tanggal_lahir' => $this->faker->date('Y-m-d', '2010-01-01'),
                'jenis_kelamin' => $attributes['profile']['jenis_kelamin'] ?? 'laki-laki',
                'agama'         => $attributes['profile']['agama'] ?? 'islam',
                'no_telp'       => $attributes['profile']['no_telp'] ?? $this->faker->phoneNumber(),
                'alamat'        => $attributes['profile']['alamat'] ?? $this->faker->address(),
                'foto'          => $attributes['profile']['foto'] ?? 'default_img.png',
                'status'        => $this->faker->randomElement(['bukan pindahan', 'pindahan']),
                'kelas'         => [
                    'id'   => (string) $this->faker->randomElement(['6601a2c4e3f4b3a2c4e3f4b1', '6601a2c4e3f4b3a2c4e3f4b2']),
                    'nama' => $this->faker->randomElement(['X IPA 1', 'XI IPA 2']),
                ],
                'angkatan' => [
                    'nama'        => 'Angkatan ' . $this->faker->year(),
                    'tahun_masuk' => (int) $this->faker->year(),
                ],
                'orang_tua' => [
                    'nama_ayah' => $this->faker->name('male'),
                    'nama_ibu'  => $this->faker->name('female'),
                    'nama_wali' => $this->faker->name('male'),
                ],
            ],
            'guru_data' => null,
        ]);
    }
}