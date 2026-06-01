<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'username', 'email', 'password', 'role', 'current_role', 'remember_token',
        'deleted', 'is_online', 'last_online',
        'profile', 'guru_data', 'siswa_data',
        'academic_records', 'attendances', 'schedule', 'password_resets',
    ];

    // ========== CASTING (HANYA UNTUK FIELD YANG AMAN) ==========
    protected $casts = [
        'deleted'          => 'boolean',
        'is_online'        => 'boolean',
        'last_online'      => 'datetime',
        'email_verified_at'=> 'datetime',
        // TIDAK ADA CASTING ARRAY UNTUK FIELD YANG BISA STRING!
    ];

    protected $hidden = ['password', 'remember_token'];
    public $timestamps = true;

    // ========== ACCESSORS (SEMUA MENGGUNAKAN ensureArray) ==========
    
    public function getProfileAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getGuruDataAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getSiswaDataAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getAcademicRecordsAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getAttendancesAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getScheduleAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getPasswordResetsAttribute($value)
    {
        return $this->ensureArray($value);
    }

    public function getNamaLengkapAttribute()
    {
        return $this->profile['nama_lengkap'] ?? 
               $this->guru_data['nama'] ?? 
               $this->siswa_data['nama'] ?? 
               $this->username;
    }

    public function getRoleDisplayAttribute()
    {
        $roles = ['admin' => 'Administrator', 'guru' => 'Guru', 'siswa' => 'Siswa', 'tamu' => 'Tamu', 'pegawai' => 'Pegawai'];
        return $roles[$this->role] ?? ucfirst($this->role);
    }

    public function getInfoKelasAttribute()
    {
        if ($this->role === 'siswa') return $this->siswa_data['kelas']['nama'] ?? '-';
        if ($this->role === 'guru') return $this->guru_data['kelas_wali']['nama'] ?? '-';
        return '-';
    }

    public function getRekapAbsensiAttribute()
    {
        $absensi = $this->getAbsensiBulanIni();
        return [
            'masuk'       => $absensi->where('status', 'masuk')->count(),
            'sakit'       => $absensi->where('status', 'sakit')->count(),
            'izin'        => $absensi->where('status', 'izin')->count(),
            'tidak_masuk' => $absensi->where('status', 'tidak masuk')->count(),
            'total'       => $absensi->count(),
        ];
    }

    public function getRataRataNilaiAttribute()
    {
        $records = $this->academic_records; // sudah lewat accessor
        $last = collect($records)->last();
        if (!$last || empty($last['nilai'])) return null;
        $total = 0; $count = 0;
        foreach ($last['nilai'] as $n) {
            if (isset($n['nilai_akademik'])) {
                $total += $n['nilai_akademik'];
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 2) : null;
    }

    // ========== SCOPES ==========
    public function scopeByRole($query, $role) { return $query->where('role', $role); }
    public function scopeSiswaAktif($query) { return $query->where('role', 'siswa')->where('siswa_data.status', '!=', 'lulus'); }
    public function scopeGuruAktif($query) { return $query->where('role', 'guru')->where('deleted', false); }
    public function scopeCariNama($query, $nama) { return $query->where('profile.nama_lengkap', 'like', "%{$nama}%"); }
    public function scopeByKelas($query, $kelasId) { return $query->where('siswa_data.kelas.id', $kelasId); }

    // ========== METHODS ==========
    public function hasRole(...$roles): bool
    {
        $current = $this->current_role ?? $this->role;
        return in_array($current, $roles);
    }

    public function tambahAbsensi(string $status, ?string $filePath = null): void
    {
        $this->push('attendances', [
            'tanggal'   => now()->format('Y-m-d'),
            'status'    => $status,
            'role'      => $this->role,
            'file_path' => $filePath,
            'created_at'=> now()->toDateTimeString(),
        ]);
    }

    public function getAbsensiBulanIni()
    {
        $bulanIni = now()->format('Y-m');
        $attendances = $this->attendances;
        return collect($attendances)->filter(fn($a) => 
            isset($a['tanggal']) && str_starts_with($a['tanggal'], $bulanIni)
        );
    }

    public function getJadwalHariIni()
    {
        $hariIni = strtolower(now()->locale('id')->dayName);
        $schedule = $this->schedule;
        foreach ($schedule as $s) {
            $jadwalItems = $this->ensureArray($s['jadwal'] ?? []);
            foreach ($jadwalItems as $j) {
                if (strtolower($j['hari'] ?? '') === $hariIni) {
                    return $this->ensureArray($j['mata_pelajaran'] ?? []);
                }
            }
        }
        return [];
    }

    public function simpanTokenResetPassword(string $token): void
    {
        $this->push('password_resets', [
            'token'      => $token,
            'expired_at' => now()->addHours(24)->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Helper: Pastikan data berbentuk array (untuk backward compatibility)
     * AMAN untuk string kosong, null, array, atau string JSON tidak valid.
     */
    private function ensureArray($data)
    {
        if (is_null($data)) return [];
        if (is_array($data)) return $data;
        if (is_string($data)) {
            // Coba parse JSON
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            // Jika string kosong atau tidak valid, kembalikan array kosong
            return [];
        }
        return [];
    }
}