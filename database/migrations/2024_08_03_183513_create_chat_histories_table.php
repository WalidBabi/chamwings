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
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('input_text');
            $table->text('response_text')->nullable();
            $table->string('thread_id');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->json('chat_history')->nullable(); // Adding JSON column for chat history
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};
