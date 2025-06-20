<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->integer('calories')->nullable();
            $table->float('sugar')->default(0);
            $table->float('fat')->default(0);
            $table->float('protein')->default(0);
            $table->float('fiber')->default(0);
            $table->float('carbohydrates')->default(0);
            $table->float('sodium')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingredients');
    }
};