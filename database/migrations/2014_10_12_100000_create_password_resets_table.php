<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->enum('title', ['Mr', 'Ms', 'Mrs'])->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('password')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
<<<<<<< HEAD
            $table->string('phone')->nullable();
=======
<<<<<<< HEAD
            $table->string('mobile')->nullable();
=======
            $table->string('phone')->nullable();
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_of_residence')->nullable();
            $table->integer('code');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};