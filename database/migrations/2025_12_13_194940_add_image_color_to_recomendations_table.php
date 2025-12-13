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
        Schema::table('recomendations', function (Blueprint $table) {
            $table->string('image_color')->nullable()->after('calorie_range');
            // Remove image_path if it's no longer needed, or keep it. 
            // The seeder doesn't use image_path, so making image_path nullable might be needed if it's not null currently.
            // But for now, let's just add image_color.
            $table->string('image_path')->nullable()->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recomendations', function (Blueprint $table) {
            $table->dropColumn('image_color');
            // Revert image_path to not null if needed, but safe to leave as is for now.
        });
    }
};
