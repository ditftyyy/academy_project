<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    // ========== FIELD UTAMA ==========
    protected $fillable = [
        // Data login
        'username',
        'email',
        'password',
        'role',
        'current_role',
        'remember_token',
        
        // Status
        'deleted',
        'is_online',
        'last_online',
        
        // Profile (embedded document)
        'profile',
        
        // Data spesifik role (embedded documents)
        'guru_data',
        'siswa_data',
        
        // Arrays
        'academic_records',
        'attendances',
        'schedule',
        'password_resets',
    ];

    // ========== CASTING ==========
    protected $casts = [
    'profile' => 'array',
    'guru_data' => 'array',
    'siswa_data' => 'array',
    'academic_records' => 'array',
    'attendances' => 'array',
    'schedule' => 'array',
    'password_resets' => 'array',
    'deleted' => 'boolean',
    'is_online' => 'boolean',
    'last_online' => 'datetime',
    'email_verified_at' => 'datetime',
    // HAPUS 'password' => 'hashed' !!!
];

    // ========== HIDDEN FIELDS ==========
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ========== TIMESTAMPS ==========
    public $timestamps = true;

    // ========== SCOPES ==========
    
    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk siswa aktif
     */
    public function scopeSiswaAktif($query)
    {
        return $query->where('role', 'siswa')
                     ->where('siswa_data.status', '!=', 'lulus');
    }

    /**
     * Scope untuk guru aktif
     */
    public function scopeGuruAktif($query)
    {
        return $query->where('role', 'guru')
                     ->where('deleted', false);
    }

    /**
     * Scope untuk cari berdasarkan nama
     */
    public function scopeCariNama($query, $nama)
    {
        return $query->where('profile.nama_lengkap', 'like', "%{$nama}%");
    }

    /**
     * Scope untuk filter kelas
     */
    public function scopeByKelas($query, $kelasId)
    {
        return $query->where('siswa_data.kelas.id', $kelasId);
    }

    // ========== ACCESSORS ==========
    
    /**
     * Mendapatkan nama lengkap (bisa dari guru atau siswa)
     */
    public function getNamaLengkapAttribute()
    {
        return $this->profile['nama_lengkap'] ?? 
               $this->guru_data['nama'] ?? 
               $this->siswa_data['nama'] ?? 
               $this->username;
    }

    /**
     * Mendapatkan role display
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'guru' => 'Guru',
            'siswa' => 'Siswa',
            'tamu' => 'Tamu',
        ];
        
        return $roles[$this->role] ?? $this->role;
    }

    /**
     * Mendapatkan kelas (untuk siswa) atau kelas wali (untuk guru)
     */
    public function getInfoKelasAttribute()
    {
        if ($this->role === 'siswa') {
            return $this->siswa_data['kelas']['nama'] ?? '-';
        }
        
        if ($this->role === 'guru') {
            return $this->guru_data['kelas_wali']['nama'] ?? '-';
        }
        
        return '-';
    }

    // ========== METHODS ==========
    
    /**
     * Cek apakah user memiliki role tertentu
     */
    public function hasRole(...$roles): bool
    {
        return in_array($this->current_role ?? $this->role, $roles);
    }

    /**
     * Menambahkan absensi baru
     */
    public function tambahAbsensi(string $status, ?string $filePath = null): void
    {
        $this->push('attendances', [
            'tanggal' => now()->format('Y-m-d'),
            'status' => $status,
            'role' => $this->role,
            'file_path' => $filePath,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Mendapatkan absensi bulan ini
     */
    public function getAbsensiBulanIni()
    {
        $bulanIni = now()->format('Y-m');
        
        return collect($this->attendances ?? [])
            ->filter(function ($absen) use ($bulanIni) {
                return str_starts_with($absen['tanggal'], $bulanIni);
            });
    }

    /**
     * Menghitung rekap absensi
     */
    public function getRekapAbsensiAttribute()
    {
        $absensi = $this->getAbsensiBulanIni();
        
        return [
            'masuk' => $absensi->where('status', 'masuk')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'tidak_masuk' => $absensi->where('status', 'tidak masuk')->count(),
            'total' => $absensi->count(),
        ];
    }

    /**
     * Mendapatkan rata-rata nilai
     */
    public function getRataRataNilaiAttribute()
    {
        if (empty($this->academic_records)) {
            return null;
        }

        // Ambil record terbaru
        $latestRecord = collect($this->academic_records)->last();
        
        if (!isset($latestRecord['nilai'])) {
            return null;
        }

        $totalNilai = collect($latestRecord['nilai'])->sum('nilai_akademik');
        $jumlahMapel = count($latestRecord['nilai']);

        return $jumlahMapel > 0 ? round($totalNilai / $jumlahMapel, 2) : 0;
    }

    /**
     * Mendapatkan jadwal hari ini
     */
    public function getJadwalHariIni()
    {
        $hariIni = strtolower(now()->locale('id')->dayName);
        
        if (empty($this->schedule)) {
            return [];
        }

        $jadwalHariIni = [];
        
        foreach ($this->schedule as $s) {
            foreach ($s['jadwal'] ?? [] as $j) {
                if (strtolower($j['hari']) === $hariIni) {
                    $jadwalHariIni = $j['mata_pelajaran'] ?? [];
                    break 2;
                }
            }
        }

        return $jadwalHariIni;
    }

    /**
     * Reset password
     */
    public function simpanTokenResetPassword(string $token): void
    {
        $this->push('password_resets', [
            'token' => $token,
            'expired_at' => now()->addHours(24)->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}