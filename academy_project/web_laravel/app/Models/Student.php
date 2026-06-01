<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'students';
    
    protected $fillable = [
        'name', 'gender', 'race_ethnicity', 
        'math_score', 'reading_score', 'writing_score', 
        'cluster'
    ];
}