<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    protected static function booted()
    {
        static::deleting(function ($model) {
            // Delete associated media files
            $model->meals()->detach();
        });
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'allergen_meal');
    }
}
