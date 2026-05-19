<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

class Ruang extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ruang';

    protected $fillable = [
        'nama_ruang',
        'luas',
        'lokasi',
        'peminjaman',
    ];

    protected $casts = [
        'peminjaman' => 'array',
    ];

    public $timestamps = true;
}