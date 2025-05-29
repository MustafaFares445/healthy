<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    protected $table = 'order_items';

    public $incrementing = true;

    protected $fillable = [
        'order_id', 'meal_id', 'quantity', 'unit_price_cents'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}