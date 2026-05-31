<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Eloquent implements AuthenticatableContract
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

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'deleted' => 'boolean',
        'is_online' => 'boolean',
        'last_online' => 'datetime',
    ];

    // Accessors
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

    private function ensureArray($data)
    {
        if (is_null($data)) return [];
        if (is_array($data)) return $data;
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    public function hasRole(...$roles): bool
    {
        $current = $this->current_role ?? $this->role;
        return in_array($current, $roles);
    }
}