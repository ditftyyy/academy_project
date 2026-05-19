<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class MoodleUser extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'moodle_users';

    protected $fillable = [
        'username',
        'email',
        'moodle_user_id',
        'moodle_data',
    ];

    protected $casts = [
        'moodle_data' => 'array',
    ];

    public $timestamps = true;
}