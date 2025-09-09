<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'meal_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'meal_id' => 'integer',
        'rating' => 'integer',
        'comment' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
