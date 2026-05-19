<?php

namespace Database\Seeders;

use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini membuat ADMIN UTAMA.
     * 
     * Di MongoDB, admin disimpan di collection
     * 'users' dengan role 'root,admin'.
     * ============================================
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $exists = User::where('username', 'root')->exists();

        if (!$exists) {
            User::create([
                'username' => 'root',
                'email' => 'admin@polije.ac.co.id',
                'password' => Hash::make('admin'),
                'role' => 'root,admin',
                'current_role' => 'admin',
                'deleted' => false,
                'is_online' => false,
                'profile' => [
                    'nama_lengkap' => 'Administrator Academy+',
                    'jabatan' => 'Super Admin',
                ],
            ]);

            echo "✅ Admin 'root' berhasil dibuat!\n";
        } else {
            echo "⚠️ Admin 'root' sudah ada, skip.\n";
        }

        // Tambahan: Buat admin cadangan
        $admin2Exists = User::where('username', 'admin')->exists();

        if (!$admin2Exists) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@academy.id',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'current_role' => 'admin',
                'deleted' => false,
                'is_online' => false,
                'profile' => [
                    'nama_lengkap' => 'Admin Academy+',
                    'jabatan' => 'Administrator',
                ],
            ]);

            echo "✅ Admin 'admin' berhasil dibuat!\n";
        }
    }
}