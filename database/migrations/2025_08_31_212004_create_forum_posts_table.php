<?php
// database/migrations/create_forum_posts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('category')->default('General');
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('answers_count')->default(0);
            $table->timestamps();

            $table->index(['created_at', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('forum_posts');
    }
};
