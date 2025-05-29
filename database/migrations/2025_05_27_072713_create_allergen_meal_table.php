<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('allergen_meal', function (Blueprint $table) {
            $table->foreignId('meal_id')->references('id')->on('meals')->onDelete('cascade');
            $table->foreignId('allergen_id')->references('id')->on('allergens')->onDelete('cascade');

            $table->primary(['meal_id', 'allergen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('meal_allergens');
    }
};