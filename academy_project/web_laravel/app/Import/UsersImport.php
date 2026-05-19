<?php

namespace App\Import;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * ================================================
     * CATATAN UNTUK PEMULA:
     *
     * Interface ToModel        -> setiap baris Excel
     *                             akan dikonversi menjadi
     *                             model User (MongoDB).
     *
     * Interface WithHeadingRow -> baris pertama Excel
     *                             dianggap sebagai judul
     *                             kolom (header).
     * ================================================
     */

    /**
     * Dipanggil untuk setiap baris data Excel.
     *
     * @param array $row  Data satu baris dari file Excel.
     *                    Contoh: ['username' => 'john', 'email' => 'john@mail.com', 'password' => 'rahasia']
     * @return MongoUser|null
     */
    public function model(array $row)
    {
        // --------------------------------------------------
        // 1. JIKA BARIS KOSONG, LEWATI
        // --------------------------------------------------
        // Kadang Excel memiliki baris kosong di akhir.
        // Kita hanya proses jika kolom 'username' ada isinya.
        if (empty($row['username'])) {
            return null;
        }

        // --------------------------------------------------
        // 2. CEK DUPLIKASI (USERNAME / EMAIL SUDAH TERDAFTAR)
        // --------------------------------------------------
        // MongoDB akan error jika kita mencoba insert
        // username/email yang sudah ada (unique constraint).
        $existing = MongoUser::where('username', $row['username'])
            ->orWhere('email', $row['email'])
            ->first();

        if ($existing) {
            // Lewati, jangan masukkan lagi.
            return null;
        }

        // --------------------------------------------------
        // 3. TENTUKAN ROLE
        // --------------------------------------------------
        // Jika kolom 'role' ada di Excel, gunakan.
        // Jika tidak, default menjadi 'siswa'.
        $role = $row['role'] ?? 'siswa';

        // --------------------------------------------------
        // 4. SIAPKAN DATA PROFILE (MINIMAL)
        // --------------------------------------------------
        // Model MongoDB kita mengharapkan field 'profile'
        // sebagai array. Isi dengan nama lengkap jika ada,
        // atau biarkan array kosong.
        $profile = [
            'nama_lengkap' => $row['nama_lengkap'] ?? $row['username'] ?? 'Tanpa Nama',
        ];

        // --------------------------------------------------
        // 5. BUAT USER BARU DI MONGODB
        // --------------------------------------------------
        return new MongoUser([
            'username'     => $row['username'],
            'email'        => $row['email'],
            'password'     => Hash::make($row['password']),
            'role'         => $role,
            'current_role' => $role,
            'profile'      => $profile,
            // Field tambahan default
            'deleted'      => false,
            'is_online'    => false,
        ]);
    }
}