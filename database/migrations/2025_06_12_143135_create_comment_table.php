<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->integer('id_comment', false, true)->length(10)->autoIncrement()->primary();
            $table->text('isi_comment');
            $table->date('tanggal_comment');
            $table->integer('id_post', false, true)->length(10);
            $table->integer('id_user', false, true)->length(10);
            $table->timestamps();

            // Index untuk foreign keys
            $table->index('id_post');
            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment');
    }
}
