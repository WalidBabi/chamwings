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
            $table->unsignedBigInteger('round_flight_id')->nullable();
            $table->unsignedBigInteger('schedule_time_id');
            $table->unsignedBigInteger('round_schedule_time_id')->nullable();
            $table->boolean('round_trip')->default(0);
            $table->enum('status', ['Confirmed', 'Pending', 'Cancelled', 'Ended']);
            $table->boolean('is_traveling')->default(0);
            $table->string('have_companions')->nullable();
            $table->string('infants')->nullable();

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('flight_id')->references('flight_id')->on('flights')->onDelete('cascade');
            $table->foreign('round_flight_id')->references('flight_id')->on('flights')->onDelete('cascade');
            $table->foreign('schedule_time_id')->references('schedule_time_id')->on('schedule_times')->onDelete('cascade');
            $table->foreign('round_schedule_time_id')->references('schedule_time_id')->on('schedule_times')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
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
