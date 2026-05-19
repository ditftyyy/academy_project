<?php

namespace App\Traits;

use App\Models\MongoDB\User as MongoUser;
use App\Models\MongoDB\Kelas as MongoKelas;

trait MongoHelper
{
    /**
     * Generate username dari NIP/NIS
     */
    private function generateUsername(string $nip): string
    {
        return strtolower(str_replace(' ', '', $nip));
    }

    /**
     * Generate email dari NIP/NIS
     */
    private function generateEmail(string $nip, string $role): string
    {
        $domain = $role === 'guru' ? '@school.teacher.com' : '@student.sch.id';
        return $nip . $domain;
    }

    /**
     * Format alamat dari array
     */
    private function formatAlamat(array $alamat): string
    {
        return implode(', ', array_map('ucfirst', $alamat));
    }

    /**
     * Upload file dan return nama file
     */
    private function uploadFile($file, string $path, ?string $prefix = null): string
    {
        if (!$file) return 'default_img.png';
        
        $filename = ($prefix ? $prefix . '_' : '') . time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($path), $filename);
        
        return $filename;
    }

    /**
     * Ambil user berdasarkan role
     */
    private function getUsersByRole(string $role, array $select = [])
    {
        return MongoUser::byRole($role)->get();
    }
}