<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Meal extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'owner_id', 'title', 'description', 'price_cents', 'is_available',
        'available_from', 'available_to', 'diet_type', 'rate'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'available_from' => 'datetime:H:i:s',
        'available_to' => 'datetime:H:i:s',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($meal) {
            // Delete associated media files
            $meal->clearMediaCollection();

            // Detach allergens (many-to-many relationship)
            $meal->allergens()->detach();

            // Detach ingredients (many-to-many relationship)
            $meal->ingredients()->detach();

            // Delete associated reviews (one-to-many relationship)
            $meal->reviews()->delete();

            // Note: For orders, you might want to handle this differently
            // depending on your business logic. You could:
            // 1. Detach from orders (remove meal from order_items)
            // 2. Or handle this through order cancellation logic
            $meal->orders()->detach();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'allergen_meal');
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_meal')
            ->withPivot('quantity', 'unit');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity', 'unit_price_cents');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
