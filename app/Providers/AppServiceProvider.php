<?php

namespace App\Providers;

use App\Models\OrderItem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Relation::morphMap([
            'order_item' => OrderItem::class,
        ]);
    }
}
