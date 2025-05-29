<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->unsignedInteger('price_cents');
            $table->boolean('is_available')->default(true);
            $table->time('available_from')->default('00:00:00');
            $table->time('available_to')->default('23:59:59');
            $table->enum('diet_type', ['keto', 'low_carb', 'vegetarian', 'vegan', 'paleo', 'balanced'])->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('meals');
    }
};