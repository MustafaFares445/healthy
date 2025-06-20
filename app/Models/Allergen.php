<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'allergen_meal');
    }
}