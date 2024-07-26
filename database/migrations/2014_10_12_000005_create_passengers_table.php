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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id('passenger_id');
<<<<<<< HEAD
            $table->unsignedBigInteger('user_profile_id');
            $table->unsignedBigInteger('passenger_info_id');
            $table->boolean('is_traveling')->default(0);

            $table->foreign('user_profile_id')->references('user_profile_id')->on('users_profiles')->onDelete('cascade');
            $table->foreign('passenger_info_id')->references('passenger_info_id')->on('passengers_info')->onDelete('cascade');
=======
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('travel_requirement_id');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('travel_requirement_id')->references('travel_requirement_id')->on('travel_requirements')->onDelete('cascade');
>>>>>>> Database-and-Models
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
        Schema::dropIfExists('passengers');
    }
};