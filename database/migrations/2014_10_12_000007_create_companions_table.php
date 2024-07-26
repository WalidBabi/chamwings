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
<<<<<<< HEAD
=======
<<<<<<< HEAD
            $table->unsignedBigInteger('user_profile_id');
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('travel_requirement_id');
            $table->boolean('infant');

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
<<<<<<< HEAD
            $table->foreign('travel_requirement_id')->references('travel_requirement_id')->on('travel_requirements')->onDelete('cascade');
=======
            $table->foreign('passenger_info_id')->references('passenger_info_id')->on('passengers_info')->onDelete('cascade');
=======
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('travel_requirement_id');
            $table->boolean('infant');

            $table->foreign('passenger_id')->references('passenger_id')->on('passengers')->onDelete('cascade');
            $table->foreign('travel_requirement_id')->references('travel_requirement_id')->on('travel_requirements')->onDelete('cascade');
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
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
<<<<<<< HEAD
};
=======
<<<<<<< HEAD
};
=======
};
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
