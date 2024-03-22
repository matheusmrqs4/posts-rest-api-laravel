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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('comment');
            $table->unsignedBigInteger('posts_id');
            $table->unsignedBigInteger('users_id');
            $table->timestamps();

            $table->foreign('posts_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->foreign('users_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['posts_id']);
            $table->dropForeign(['users_id']);
        });

        Schema::dropIfExists('comments');
    }
};
