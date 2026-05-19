<?php

namespace Database\Seeders;

use App\Models\MongoDB\Pengumuman;
use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;

class TamuRolesSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat data BUKU TAMU dummy.
     * 
     * Di MongoDB, data tamu disimpan di collection
     * 'pengumuman' dengan type='tamu'.
     * ============================================
     */
    public function run(): void
    {
        // Hapus data tamu lama
        Pengumuman::where('type', 'tamu')->delete();

        $users = User::whereIn('role', ['guru', 'siswa'])->get();

        foreach ($users as $user) {
            $tujuan = ($user->role === 'guru') ? 'Guru' : 'Siswa';

            Pengumuman::create([
                'title' => 'Tamu: ' . fake('id_ID')->name(),
                'message' => 'Ingin bertemu',
                'role' => 'admin',
                'type' => 'tamu',
                'data_tambahan' => [
                    'nama_tamu' => fake('id_ID')->name(),
                    'alamat' => fake('id_ID')->address(),
                    'tujuan' => $tujuan,
                    'tujuan_username' => $user->username,
                    'tujuan_nama' => $user->nama_lengkap,
                    'status' => fake()->randomElement(['menunggu', 'pesan_telah_diterima', 'pesan_telah_selesai']),
                ],
                'created_by' => null,
            ]);
        }

        echo "✅ Tamu seeder selesai! (" . $users->count() . " data tamu)\n";
    }
}