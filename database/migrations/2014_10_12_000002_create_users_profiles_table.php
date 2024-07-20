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
        Schema::create('users_profiles', function (Blueprint $table) {
            $table->id('user_profile_id');
            $table->enum('title', ['Mr', 'Ms', 'Mrs']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('date_of_birth');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('mobile');
            $table->integer('age');
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_of_residence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_profiles');
    }
};