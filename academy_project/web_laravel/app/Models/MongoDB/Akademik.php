<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

class Akademik extends Model
{
    protected $connection = 'mongodb';
   protected $collection = 'akademik';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'selected',
        'kalender',
        'konfigurasi',
    ];

    protected $casts = [
        'kalender'    => 'array',
        'konfigurasi' => 'array',
        'selected'    => 'boolean',
    ];

    public $timestamps = true;

    // Scope
    public function scopeAktif($query)
    {
        return $query->where('selected', true);
    }

    // Accessor
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
     * Tambah event kalender (aman untuk data awal berupa array)
     */
    public function tambahEvent(array $event): void
    {
        // Pastikan kalender selalu array
        $current = $this->kalender ?? [];
        if (!is_array($current)) {
            $current = [];
        }

        $current[] = [
            'id'         => uniqid(),
            'title'      => $event['title'],
            'start_date' => $event['start_date'],
            'end_date'   => $event['end_date'],
            'status'     => $event['status'] ?? 'libur',
            'created_at' => now()->toDateTimeString(),
        ];

        $this->kalender = $current;
        $this->save();
    }

    /**
     * Update event kalender
     */
    public function updateEvent(string $eventId, array $data): bool
    {
        $kalender = $this->kalender ?? [];
        if (!is_array($kalender)) {
            return false;
        }

        foreach ($kalender as &$event) {
            if (($event['id'] ?? '') === $eventId) {
                if (isset($data['start_date'])) $event['start_date'] = $data['start_date'];
                if (isset($data['end_date']))   $event['end_date']   = $data['end_date'];
                if (isset($data['title']))      $event['title']      = $data['title'];
                if (isset($data['status']))     $event['status']     = $data['status'];
                $this->kalender = $kalender;
                $this->save();
                return true;
            }
        }
        return false;
    }

    /**
     * Hapus event kalender
     */
    public function hapusEvent(string $eventId): bool
    {
        $kalender = $this->kalender ?? [];
        if (!is_array($kalender)) {
            return false;
        }

        $newKalender = array_filter($kalender, function($e) use ($eventId) {
            return ($e['id'] ?? '') !== $eventId;
        });

        $this->kalender = array_values($newKalender);
        $this->save();
        return true;
    }

    /**
     * Cek apakah tanggal tertentu libur
     */
    public function isHariLibur(string $tanggal): bool
    {
        $kalender = $this->kalender ?? [];
        if (!is_array($kalender)) return false;

        return collect($kalender)
            ->where('status', 'libur')
            ->contains(function ($event) use ($tanggal) {
                return $tanggal >= $event['start_date'] && $tanggal <= $event['end_date'];
            });
    }

    /**
     * Set tahun ajaran aktif
     */
    public static function setAktif(string $id): void
    {
        self::query()->update(['selected' => false]);
        self::where('_id', $id)->update(['selected' => true]);
    }
}