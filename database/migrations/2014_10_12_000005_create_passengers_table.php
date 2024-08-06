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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('travel_requirement_id');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('travel_requirement_id')->references('travel_requirement_id')->on('travel_requirements')->onDelete('cascade');
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