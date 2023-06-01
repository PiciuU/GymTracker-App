<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_exercise_history', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('user_id');
            $table->float('weight');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_exercise_history');
    }
};
