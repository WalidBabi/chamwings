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
        Schema::create('passenger_memberships', function (Blueprint $table) {
            $table->id('passenge_membership_id');
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('program_id');
            $table->string('tier_level');
            $table->string('miles');
            $table->date('join_date');

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('program_id')->references('loyalty_program_id')->on('loyalty_programs')->onDelete('cascade');
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
        Schema::dropIfExists('passenger_memberships');
    }
};