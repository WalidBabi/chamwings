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
        Schema::create('offers', function (Blueprint $table) {
            $table->id('offer_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('image');
            $table->string('title');

            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
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
        Schema::dropIfExists('offers');
    }
<<<<<<< HEAD
};
=======
};
>>>>>>> Database-and-Models
