<?php

namespace Database\Seeders;

use App\Models\MongoDB\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AbsensisSeeder extends Seeder
{
    /**
     * ============================================
     * CATATAN UNTUK PEMULA:
     * Seeder ini mengisi data ABSENSI untuk
     * semua user (guru & siswa).
     * 
     * Di MongoDB, absensi disimpan sebagai ARRAY
     * 'attendances' di dalam dokumen user.
     * 
     * CARA KERJA:
     * 1. Cari semua user (kecuali admin)
     * 2. Untuk setiap user, generate absensi
     *    untuk 5 hari kerja terakhir
     * 3. Simpan sebagai array di field 'attendances'
     * ============================================
     */
    public function run(): void
    {
        // Ambil semua user kecuali admin
        $users = User::where('role', '!=', 'root,admin')
            ->where('role', '!=', 'admin')
            ->get();

        $endDate = now()->subDays(2);
        $startDate = $endDate->copy()->subDays(7);

        foreach ($users as $user) {
            $attendances = [];
            $currentDate = $startDate->copy();
            $role = ($user->role === 'guru') ? 'guru' : 'siswa';

            while ($currentDate <= $endDate) {
                $dayOfWeek = $currentDate->dayOfWeek;

                // Skip Sabtu (6) dan Minggu (0)
                if ($dayOfWeek != 6 && $dayOfWeek != 0) {
                    $attendances[] = [
                        'tanggal' => $currentDate->format('Y-m-d'),
                        'status' => fake('id_ID')->randomElement(['masuk', 'sakit', 'izin', 'tidak masuk']),
                        'role' => $role,
                        'created_at' => $currentDate->format('Y-m-d H:i:s'),
                    ];
                }

                $currentDate->addDay();
            }

            // Simpan absensi ke user
            $user->attendances = $attendances;
            $user->save();
        }

        echo "✅ Absensi seeder selesai! (" . $users->count() . " users)\n";
    }
}