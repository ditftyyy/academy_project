<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class Mapel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'mata_pelajaran';

    protected $fillable = [
        'nama_mapel',
        'kode_mapel',
        'deskripsi',
        'kelompok',     // A (wajib), B (peminatan), C (lintas minat)
        'guru_pengajar_ids', // Array of user IDs
    ];

    protected $casts = [
        'guru_pengajar_ids' => 'array',
    ];

    public $timestamps = true;

    // ========== SCOPES ==========
    
    public function scopeByKelompok($query, $kelompok)
    {
        return $query->where('kelompok', $kelompok);
    }

    // ========== METHODS ==========
    
    /**
     * Menambah guru pengajar
     */
    public function tambahGuruPengajar(string $guruId): void
    {
        $guruIds = $this->guru_pengajar_ids ?? [];
        
        if (!in_array($guruId, $guruIds)) {
            $guruIds[] = $guruId;
            $this->guru_pengajar_ids = $guruIds;
            $this->save();
        }
    }

    /**
     * Menghapus guru pengajar
     */
    public function hapusGuruPengajar(string $guruId): void
    {
        $this->guru_pengajar_ids = array_values(
            array_filter($this->guru_pengajar_ids ?? [], fn($id) => $id !== $guruId)
        );
        $this->save();
    }
}