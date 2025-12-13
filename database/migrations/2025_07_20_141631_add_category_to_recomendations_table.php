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
        Schema::table('recomendations', function (Blueprint $table) {
            // Tambahkan kolom category setelah kolom image_path
            $table->string('category', 100)->after('image_path')->nullable();

            // Optional: Tambahkan kolom untuk multiple categories (JSON)
            // $table->json('categories')->after('image_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('recomendations', function (Blueprint $table) {
            $table->dropColumn('category');
            // $table->dropColumn('categories');
        });
    }
    /**
     * Reverse the migrations.
     */
};
