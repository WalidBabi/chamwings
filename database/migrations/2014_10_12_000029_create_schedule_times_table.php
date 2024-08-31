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
        Schema::create('schedule_times', function (Blueprint $table) {
            $table->id('schedule_time_id');
            $table->unsignedBigInteger('schedule_day_id');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->date('duration');

            $table->foreign('schedule_day_id')->references('schedule_day_id')->on('schedule_days')->onDelete('cascade');
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
        Schema::dropIfExists('schedule_time');
    }
};