<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('total_cents');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled'])->default('pending');
            $table->string('delivery_address', 255);
            $table->string('delivery_time_slot', 50)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};