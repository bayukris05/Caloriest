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
        Schema::create('post', function (Blueprint $table) {
            // $table->id();
            $table->integer('id_post', false, true)->length(10)->autoIncrement();
            $table->date('tanggal_post');
            $table->text('content_post')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('id_user', false, true)->length(10);
            $table->timestamps();

            // Index untuk id_user karena kemungkinan ada foreign key
            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};
