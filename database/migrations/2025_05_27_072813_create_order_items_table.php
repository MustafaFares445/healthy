<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price_cents');

            $table->foreignId('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreignId('meal_id')->references('id')->on('meals')->onDelete('cascade');

            $table->primary(['order_id', 'meal_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};