<?php

namespace App\Exports;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExportSiswa implements FromCollection, WithMapping, WithHeadings
{
    /**
     * Koleksi siswa dari MongoDB.
     */
    public function collection()
    {
        return MongoUser::where('role', 'siswa')->get();
    }

    /**
     * Pemetaan data per siswa.
     */
    public function map($user): array
    {
        $siswaData = $user->siswa_data ?? [];
        $nisn = $siswaData['nisn'] ?? '';
        $nama = $siswaData['nama'] ?? $user->nama_lengkap ?? '';

        return [
            'firstname' => $nisn . ' ' . $nama,
            'lastname' => $user->role ?? 'siswa',
            'username' => $user->username ?? '',
            'email' => $user->email ?? '',
            'password' => 'S123*',
        ];
    }

    public function headings(): array
    {
        return [
            'firstname',
            'lastname',
            'username',
            'email',
            'password',
        ];
    }
}