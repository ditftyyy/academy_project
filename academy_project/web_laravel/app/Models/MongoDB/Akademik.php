<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class Akademik extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'akademik';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'selected',
        
        // Kalender akademik
        'kalender',
        
        // Konfigurasi
        'konfigurasi',
    ];

    protected $casts = [
        'kalender' => 'array',
        'konfigurasi' => 'array',
        'selected' => 'boolean',
    ];

    public $timestamps = true;

    // ========== SCOPES ==========
    
    public function scopeAktif($query)
    {
        return $query->where('selected', true);
    }

    public function scopeByTahunAjaran($query, $tahunAjaran)
    {
        return $query->where('tahun_ajaran', $tahunAjaran);
    }

    // ========== ACCESSORS ==========
    
    public function getDisplayNameAttribute()
    {
        $semesterDisplay = $this->semester === 'ganjil' ? 'Ganjil' : 'Genap';
        return "TA {$this->tahun_ajaran} - Semester {$semesterDisplay}";
    }

    public function getJumlahHariEfektifAttribute()
    {
        return collect($this->kalender ?? [])
            ->where('status', 'masuk')
            ->count();
    }

    // ========== METHODS ==========
    
    /**
     * Menambah event kalender
     */
    public function tambahEvent(array $event): void
    {
        $this->push('kalender', [
            'title' => $event['title'],
            'start_date' => $event['start_date'],
            'end_date' => $event['end_date'],
            'status' => $event['status'] ?? 'masuk',
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Cek apakah tanggal tertentu adalah hari libur
     */
    public function isHariLibur(string $tanggal): bool
    {
        return collect($this->kalender ?? [])
            ->where('status', 'libur')
            ->contains(function ($event) use ($tanggal) {
                return $tanggal >= $event['start_date'] && 
                       $tanggal <= $event['end_date'];
            });
    }

    /**
     * Set sebagai tahun ajaran aktif
     */
    public static function setAktif(string $id): void
    {
        // Nonaktifkan semua
        self::query()->update(['selected' => false]);
        
        // Aktifkan yang dipilih
        self::where('_id', $id)->update(['selected' => true]);
    }
}