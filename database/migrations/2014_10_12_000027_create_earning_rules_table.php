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
        Schema::create('earning_rules', function (Blueprint $table) {
            $table->id('earning_rule_id');
            $table->unsignedBigInteger('progrma_id');
            $table->enum('earning_type', ['e.g', 'Per Mile Flown', 'Based On Fare Class']);
            $table->enum('earning_rate', ['e.g', '1 Mile kilometer', '2x Miles for Business Class']);
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
        Schema::dropIfExists('earning_rules');
    }
};