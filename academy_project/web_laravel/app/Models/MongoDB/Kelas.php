<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

class Kelas extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'deleted',
        'wali_kelas',
        'jadwal',
        'siswa_ids',
        'tahun_ajaran_aktif',
    ];

    protected $casts = [
        'wali_kelas' => 'array',
        'siswa_ids' => 'array',
        'deleted' => 'boolean',
        // jadwal tidak di-cast agar kita handle manual
    ];

    public $timestamps = true;

    // Accessor untuk jadwal (mengubah string JSON menjadi array)
    public function getJadwalAttribute($value)
    {
        if (is_null($value)) return [];
        if (is_array($value)) return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // Mutator untuk jadwal (mengubah array menjadi string JSON saat disimpan)
    public function setJadwalAttribute($value)
    {
        $this->attributes['jadwal'] = json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    // Scope
    public function scopeKelasAktif($query)
    {
        return $query->where('deleted', false);
    }

    public function getNamaWaliKelasAttribute()
    {
        return $this->wali_kelas['nama'] ?? 'Belum ditentukan';
    }

    public function getJumlahSiswaAttribute()
    {
        return count($this->siswa_ids ?? []);
    }

    // ========== METHOD UNTUK MENGELOLA SISWA ==========
    public function tambahSiswa(string $siswaId): void
    {
        $siswaIds = $this->siswa_ids ?? [];
        if (!in_array($siswaId, $siswaIds)) {
            $siswaIds[] = $siswaId;
            $this->siswa_ids = $siswaIds;
            $this->save();
        }
    }

    public function keluarkanSiswa(string $siswaId): void
    {
        $siswaIds = $this->siswa_ids ?? [];
        $siswaIds = array_values(array_filter($siswaIds, fn($id) => $id !== $siswaId));
        $this->siswa_ids = $siswaIds;
        $this->save();
    }

    // ========== METHOD JADWAL ==========
    public function updateStatusHari(string $hari, string $status)
    {
        $jadwal = $this->jadwal; // menggunakan accessor
        $found = false;
        foreach ($jadwal as &$j) {
            if (strtolower($j['hari'] ?? '') === strtolower($hari)) {
                $j['status'] = $status;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $jadwal[] = ['hari' => $hari, 'status' => $status, 'mata_pelajaran' => []];
        }
        $this->jadwal = $jadwal; // trigger mutator
        $this->save();
    }

    public function tambahMataPelajaran(string $hari, array $data)
    {
        $jadwal = $this->jadwal;
        $found = false;
        foreach ($jadwal as &$j) {
            if (strtolower($j['hari'] ?? '') === strtolower($hari)) {
                $j['mata_pelajaran'][] = $data;
                usort($j['mata_pelajaran'], function($a, $b) {
                    return strcmp($a['jam_mulai'] ?? '', $b['jam_mulai'] ?? '');
                });
                $found = true;
                break;
            }
        }
        if (!$found) {
            $jadwal[] = [
                'hari' => $hari,
                'status' => 'masuk',
                'mata_pelajaran' => [$data]
            ];
        }
        $this->jadwal = $jadwal;
        $this->save();
    }

    public function updateMataPelajaran(string $hari, int $index, array $data)
    {
        $jadwal = $this->jadwal;
        foreach ($jadwal as &$j) {
            if (strtolower($j['hari'] ?? '') === strtolower($hari)) {
                if (isset($j['mata_pelajaran'][$index])) {
                    $j['mata_pelajaran'][$index] = $data;
                    usort($j['mata_pelajaran'], function($a, $b) {
                        return strcmp($a['jam_mulai'] ?? '', $b['jam_mulai'] ?? '');
                    });
                }
                break;
            }
        }
        $this->jadwal = $jadwal;
        $this->save();
    }

    public function hapusMataPelajaran(string $hari, int $index)
    {
        $jadwal = $this->jadwal;
        foreach ($jadwal as &$j) {
            if (strtolower($j['hari'] ?? '') === strtolower($hari)) {
                if (isset($j['mata_pelajaran'][$index])) {
                    array_splice($j['mata_pelajaran'], $index, 1);
                }
                break;
            }
        }
        $this->jadwal = $jadwal;
        $this->save();
    }
}