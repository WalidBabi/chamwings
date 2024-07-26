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
<<<<<<< HEAD
            $table->unsignedBigInteger('airport_id');
=======
<<<<<<< HEAD
            $table->unsignedBigInteger('destination_airport');
            $table->unsignedBigInteger('employee_id');
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38
            $table->string('visa_and_residence');
            $table->string('origin');
            $table->string('destination');

<<<<<<< HEAD
            $table->foreign('airport_id')->references('airport_id')->on('airports')->onDelete('cascade');
=======
            $table->foreign('destination_airport')->references('airport_id')->on('airports')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
=======
            $table->unsignedBigInteger('airport_id');
            $table->string('visa_and_residence');
            $table->string('origin');
            $table->string('destination');

            $table->foreign('airport_id')->references('airport_id')->on('airports')->onDelete('cascade');
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
        Schema::dropIfExists('visainfo');
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
