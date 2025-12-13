<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('daily_calories');
        Schema::create('daily_calories', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('menu_id');
        $table->foreign('menu_id')->references('id_menu')->on('menu')->onDelete('cascade');
            $table->integer('calories');
            $table->date('date');
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['menu_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_calories');
    }
};