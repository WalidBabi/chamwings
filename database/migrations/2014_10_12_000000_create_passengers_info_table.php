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
        Schema::create('passengers_info', function (Blueprint $table) {
            $table->id('passenger_info_id');
            $table->string('passport')->nullable();
            $table->string('passport_issued_country')->nullable();
            $table->string('passport_expiry_date')->nullable();
            $table->string('mobile_during_travel')->nullable();
            $table->string('passport_image')->nullable();
            $table->integer('id_number')->nullable();
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
        Schema::dropIfExists('passengers_info');
    }
};
