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
        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // For authenticated users
            $table->string('session_id')->nullable(); // For guest users
            $table->morphs('likeable'); // likeable_id and likeable_type
            $table->timestamps();

            // Indexes for better performance
            $table->index(['likeable_id', 'likeable_type']);
            $table->index('session_id');
            $table->index('user_id');

            // Prevent duplicate likes from same session/user
            $table->unique(['session_id', 'likeable_id', 'likeable_type'], 'unique_session_like');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_likes');
    }
};
