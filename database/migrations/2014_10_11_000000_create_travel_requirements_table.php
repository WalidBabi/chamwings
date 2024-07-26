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
        Schema::create('travel_requirements', function (Blueprint $table) {
            $table->id('travel_requirement_id');
            $table->enum('title', ['Mr', 'Ms', 'Mrs']);
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->integer('id_number')->nullable();
            $table->string('mobile_during_travel')->nullable();
            $table->integer('age');
            $table->string('gender')->nullable();
            $table->string('nationality')->nullable();
            $table->string('country_of_residence');
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
        Schema::dropIfExists('travel_requirements');
    }
};