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
        Schema::create('menu', function (Blueprint $table) {
            // $table->id();
            $table->integer('id_menu', false, true)->length(10)->autoIncrement();
            $table->string('nama_menu', 30);
            $table->integer('jumlah', false, true)->length(10);
            $table->integer('jumlah_kalori', false, true)->length(10);
            $table->integer('id_satuan', false, true)->length(10);
            $table->timestamps();

            // Index untuk id_satuan karena kemungkinan ada foreign key
            $table->index('id_satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
