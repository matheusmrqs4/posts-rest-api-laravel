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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('senders_id');
            $table->unsignedBigInteger('posts_id');
            $table->unsignedBigInteger('comments_id');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('senders_id')->references('id')->on('users');
            $table->foreign('posts_id')->references('id')->on('posts');
            $table->foreign('comments_id')->references('id')->on('comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['users_id']);
            $table->dropForeign(['senders_id']);
            $table->dropForeign(['posts_id']);
            $table->dropForeign(['comments_id']);
        });

        Schema::dropIfExists('notifications');
    }
};
