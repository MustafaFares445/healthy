<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_cents', 'status',
        'delivery_address', 'delivery_time_slot'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'order_items')
                    ->withPivot('quantity', 'unit_price_cents');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}