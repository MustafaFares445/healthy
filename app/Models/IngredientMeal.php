<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name', 'calories', 'sugar', 'fat', 'protein',
        'fiber', 'carbohydrates', 'sodium'
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            // Delete associated media files
            $model->meals()->detach();
        });
    }

    public $timestamps = false;

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'meal_ingredients')
                    ->withPivot('quantity', 'unit');
    }
}
