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
        Schema::create('seats', function (Blueprint $table) {
            $table->id('seat_id');
            $table->unsignedBigInteger('class_id');
<<<<<<< HEAD
            $table->enum('seat_number', ['A', 'B', 'C', 'D']);
            $table->enum('row_number', [1, 2, 3, 4]);
=======
<<<<<<< HEAD
            $table->integer('seat_number');
            $table->enum('status', ['Occupied', 'Available', 'Selected']);
            $table->integer('row_number');
=======
            $table->enum('seat_number', ['A', 'B', 'C', 'D']);
            $table->enum('row_number', [1, 2, 3, 4]);
>>>>>>> Database-and-Models
>>>>>>> 4d5a7aa1bf14c0f1d78928c68052db2367164b38

            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
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
        Schema::dropIfExists('seats');
    }
};