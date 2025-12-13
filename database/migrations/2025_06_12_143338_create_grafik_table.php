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
        Schema::create('grafik', function (Blueprint $table) {
            // $table->id();
            $table->date('tanggal');
            $table->integer('kalori_harian', false, true)->length(10);
            $table->integer('id_grafik', false, true)->length(10)->autoIncrement();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grafik');
    }
};
