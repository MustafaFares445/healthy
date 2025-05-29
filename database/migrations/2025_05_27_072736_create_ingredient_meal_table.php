<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ingredient_meal', function (Blueprint $table) {
            $table->double('quantity')->nullable();
            $table->enum('unit', ['tbsp', 'g', 'piece', 'l'])->nullable();

            $table->foreignId('meal_id')->references('id')->on('meals')->onDelete('cascade');
            $table->foreignId('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');

            $table->primary(['meal_id', 'ingredient_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_ingredients');
    }
};