<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class Pengumuman extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'pengumuman';

    protected $fillable = [
        'title',
        'message',
        'role',         // 'admin', 'guru', 'siswa', 'semua'
        'type',         // 'pengumuman', 'tamu', 'kerjasama'
        'created_by',
        
        // Data tambahan sesuai type
        'data_tambahan',
    ];

    protected $casts = [
        'data_tambahan' => 'array',
    ];

    public $timestamps = true;

    // ========== SCOPES ==========
    
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role)
                     ->orWhere('role', 'semua');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeTerbaru($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // ========== METHODS ==========
    
    /**
     * Membuat pengumuman
     */
    public static function buat(string $title, string $message, string $role = 'semua'): self
    {
        return self::create([
            'title' => $title,
            'message' => $message,
            'role' => $role,
            'type' => 'pengumuman',
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Mencatat tamu
     */
    public static function catatTamu(array $data): self
    {
        return self::create([
            'title' => 'Tamu: ' . $data['nama'],
            'message' => $data['keterangan'] ?? '',
            'role' => 'admin',
            'type' => 'tamu',
            'data_tambahan' => [
                'nama_tamu' => $data['nama'],
                'alamat' => $data['alamat'] ?? '',
                'tujuan' => $data['tujuan'] ?? '',
                'status' => 'menunggu',
            ],
            'created_by' => null,
        ]);
    }

    /**
     * Mencatat kerjasama (MoU)
     */
    public static function catatKerjasama(array $data): self
    {
        return self::create([
            'title' => 'MoU: ' . $data['nama_mitra'],
            'message' => $data['deskripsi'] ?? '',
            'role' => 'admin',
            'type' => 'kerjasama',
            'data_tambahan' => [
                'nama_mitra' => $data['nama_mitra'],
                'asal_mitra' => $data['asal_mitra'] ?? '',
                'pt_mitra' => $data['pt_mitra'] ?? '',
                'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
                'tanggal_berakhir' => $data['tanggal_berakhir'] ?? null,
                'file' => $data['file'] ?? null,
                'original_name_file' => $data['original_name_file'] ?? '',
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Update status tamu
     */
    public function updateStatusTamu(string $status): void
    {
        if ($this->type === 'tamu') {
            $data = $this->data_tambahan;
            $data['status'] = $status;
            $this->data_tambahan = $data;
            $this->save();
        }
    }
}