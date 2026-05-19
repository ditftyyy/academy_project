<?php

namespace App\Exports;

use App\Models\MongoDB\User as MongoUser;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExportGuru implements FromCollection, WithMapping, WithHeadings
{
    /**
     * Koleksi guru dari MongoDB.
     */
    public function collection()
    {
        return MongoUser::where('role', 'guru')->get();
    }

    /**
     * Pemetaan data per guru.
     */
    public function map($user): array
    {
        $guruData = $user->guru_data ?? [];
        $nip = $guruData['nip'] ?? '';
        $nama = $guruData['nama'] ?? $user->nama_lengkap ?? '';

        return [
            'firstname' => $nip . ' ' . $nama,
            'lastname' => $user->role ?? 'guru',
            'username' => $user->username ?? '',
            'email' => $user->email ?? '',
            'password' => 'G123*', // statis, bisa diganti sesuai kebijakan
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