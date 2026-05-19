<?php

namespace App\Exports;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * Koleksi data yang akan diekspor.
     * Di sini kita ambil semua user (bisa difilter hanya siswa/guru sesuai kebutuhan).
     */
    public function collection()
    {
        // Ambil semua user yang bukan admin (opsional, sesuaikan)
        return MongoUser::whereIn('role', ['siswa', 'guru'])->get();
    }

    /**
     * Petakan setiap baris data menjadi array kolom Excel.
     */
    public function map($user): array
    {
        return [
            'username' => $user->username ?? '',
            'email' => $user->email ?? '',
            'role' => $user->role ?? '',
            'nama_lengkap' => $user->nama_lengkap ?? '',
        ];
    }

    /**
     * Judul kolom di baris pertama.
     */
    public function headings(): array
    {
        return [
            'username',
            'email',
            'role',
            'nama_lengkap',
        ];
    }
}