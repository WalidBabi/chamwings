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
        Schema::create('companions', function (Blueprint $table) {
            $table->id('companion_id');
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('travel_requirement_id');
            $table->boolean('infant');

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('travel_requirement_id')->references('travel_requirement_id')->on('travel_requirements')->onDelete('cascade');
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
        Schema::dropIfExists('companions');
    }
};