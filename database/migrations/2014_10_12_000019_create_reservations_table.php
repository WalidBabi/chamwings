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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('flight_id');
<<<<<<< HEAD
            $table->boolean('round_trip');
=======
<<<<<<< HEAD
            $table->integer('number_of_passengers');
            $table->date('reservation_date');
            $table->boolean('round_trip');
            $table->enum('status', ['Confirmed', 'Pending', 'Canceled']);
=======
            $table->boolean('round_trip');
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
            $table->enum('status', ['Confirmed', 'Pending', 'Cancelled']);
            $table->boolean('is_traveling');
            $table->boolean('have_companions');
            $table->date('reservation_date');
<<<<<<< HEAD
=======
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('flight_id')->references('flight_id')->on('flights')->onDelete('cascade');
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
        Schema::dropIfExists('reservations');
    }
};