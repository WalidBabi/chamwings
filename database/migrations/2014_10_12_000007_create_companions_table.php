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
            $table->unsignedBigInteger('user_profile_id');
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('passenger_info_id');
            $table->boolean('infant');

            $table->foreign('user_profile_id')->references('user_profile_id')->on('users_profiles')->onDelete('cascade');
            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('passenger_info_id')->references('passenger_info_id')->on('passengers_info')->onDelete('cascade');
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
        Schema::dropIfExists('companions');
    }
};
