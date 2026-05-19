<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class Kelas extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',      // X, XI, XII
        'jurusan',      // IPA, IPS
        'deleted',
        
        // Wali kelas
        'wali_kelas',
        
        // Jadwal embedded
        'jadwal',
        
        // Daftar siswa (hanya ID reference)
        'siswa_ids',
        
        // Tahun ajaran aktif
        'tahun_ajaran_aktif',
    ];

    protected $casts = [
        'wali_kelas' => 'array',
        'jadwal' => 'array',
        'siswa_ids' => 'array',
        'deleted' => 'boolean',
    ];

    public $timestamps = true;

    // ========== SCOPES ==========
    
    public function scopeKelasAktif($query)
    {
        return $query->where('deleted', false);
    }

    public function scopeByTingkat($query, $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    public function scopeByJurusan($query, $jurusan)
    {
        return $query->where('jurusan', $jurusan);
    }

    // ========== ACCESSORS ==========
    
    public function getNamaWaliKelasAttribute()
    {
        return $this->wali_kelas['nama'] ?? 'Belum ditentukan';
    }

    public function getJumlahSiswaAttribute()
    {
        return count($this->siswa_ids ?? []);
    }

    public function getJadwalHariIniAttribute()
    {
        $hariIni = strtolower(now()->locale('id')->dayName);
        
        foreach ($this->jadwal ?? [] as $j) {
            if (strtolower($j['hari']) === $hariIni) {
                return $j['mata_pelajaran'] ?? [];
            }
        }

        return [];
    }

    // ========== METHODS ==========
    
    /**
     * Menambah siswa ke kelas
     */
    public function tambahSiswa(string $userId): void
    {
        $siswaIds = $this->siswa_ids ?? [];
        
        if (!in_array($userId, $siswaIds)) {
            $siswaIds[] = $userId;
            $this->siswa_ids = $siswaIds;
            $this->save();
        }
    }

    /**
     * Mengeluarkan siswa dari kelas
     */
    public function keluarkanSiswa(string $userId): void
    {
        $this->siswa_ids = array_values(
            array_filter($this->siswa_ids ?? [], fn($id) => $id !== $userId)
        );
        $this->save();
    }

    /**
     * Mengupdate jadwal
     */
    public function updateJadwal(string $hari, array $mataPelajaran): void
    {
        $jadwal = $this->jadwal ?? [];
        $found = false;

        foreach ($jadwal as &$j) {
            if ($j['hari'] === $hari) {
                $j['mata_pelajaran'] = $mataPelajaran;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $jadwal[] = [
                'hari' => $hari,
                'mata_pelajaran' => $mataPelajaran,
            ];
        }

        $this->jadwal = $jadwal;
        $this->save();
    }
}