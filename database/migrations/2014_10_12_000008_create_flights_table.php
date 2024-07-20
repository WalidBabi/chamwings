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
            $table->unsignedBigInteger('departure_airport');
            $table->unsignedBigInteger('arrival_airport');
            $table->unsignedBigInteger('airplane_id');
            $table->integer('flight_number');
            $table->date('departure_date');
            $table->date('arrival_date');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->string('duration');
            $table->integer('number_of_reserved_seats');
            $table->integer('price');
            $table->string('terminal');

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