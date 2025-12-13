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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('usia')->nullable()->after('password');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('usia');
            $table->float('tb')->nullable()->after('jenis_kelamin');
            $table->float('bb')->nullable()->after('tb');
            $table->string('aktivitas')->nullable()->after('bb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['usia', 'jenis_kelamin', 'tb', 'bb', 'aktivitas']);
        });
    }
};
