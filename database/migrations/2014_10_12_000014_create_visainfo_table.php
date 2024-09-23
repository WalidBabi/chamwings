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
        Schema::create('visainfo', function (Blueprint $table) {
            $table->id('visainfo_id');
            $table->unsignedBigInteger('departure_airport');
            $table->unsignedBigInteger('arrival_airport');
            $table->string('visa_and_residence');

            $table->foreign('departure_airport')->references('airport_id')->on('airports')->onDelete('cascade');
            $table->foreign('arrival_airport')->references('airport_id')->on('airports')->onDelete('cascade');
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
        Schema::dropIfExists('visainfo');
    }
};