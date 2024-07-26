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
        Schema::create('flights', function (Blueprint $table) {
            $table->id('flight_id');
            $table->unsignedBigInteger('airplane_id');
            $table->unsignedBigInteger('departure_airport');
            $table->unsignedBigInteger('arrival_airport');
            $table->integer('flight_number');
            $table->integer('number_of_reserved_seats');
            $table->integer('price');
            $table->date('departure_terminal');
            $table->date('arrival_terminal');

            $table->foreign('departure_airport')->references('airport_id')->on('airports')->onDelete('cascade');
            $table->foreign('arrival_airport')->references('airport_id')->on('airports')->onDelete('cascade');
            $table->foreign('airplane_id')->references('airplane_id')->on('airplanes')->onDelete('cascade');
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
        Schema::dropIfExists('flights');
    }
};