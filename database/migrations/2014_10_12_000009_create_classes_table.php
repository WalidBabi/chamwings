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
        Schema::create('classes', function (Blueprint $table) {
            $table->id('class_id');
            $table->unsignedBigInteger('airplane_id');
            $table->enum('class_name', ['e.g', 'Economy', 'Business']);
            $table->integer('price_rate');
            $table->string('weight_allowed');
            $table->integer('number_of_meals');
            $table->integer('number_of_seats');

            $table->foreign('airplane_id')->references('airplane_id')->on('airplanes')->onDelete('cascade');
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
        Schema::dropIfExists('classes');
    }
};