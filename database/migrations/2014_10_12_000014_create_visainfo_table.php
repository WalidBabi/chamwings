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
            $table->unsignedBigInteger('airport_id');
            $table->string('visa_and_residence');
            $table->string('origin');
            $table->string('destination');

            $table->foreign('airport_id')->references('airport_id')->on('airports')->onDelete('cascade');
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