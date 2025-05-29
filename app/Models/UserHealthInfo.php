<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHealthInfo extends Model
{
    use HasFactory;

    protected $table = 'user_health_info';

    protected $fillable = [
        'weight',
        'height',
        'activity_level',
        'dietary_restrictions',
        'goal',
        'health_notes',
        'user_id',
    ];
}