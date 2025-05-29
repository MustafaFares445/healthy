<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_health_info', function (Blueprint $table) {
            $table->id();
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->enum('activity_level', ['sedentary', 'active', 'very_active'])->nullable();
            $table->string('dietary_restrictions', 255)->nullable();
            $table->enum('goal', ['weight_loss', 'maintenance', 'muscle_gain'])->nullable();
            $table->text('health_notes')->nullable();

            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_health_info');
    }
};