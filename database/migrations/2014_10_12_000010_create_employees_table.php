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
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
<<<<<<< HEAD
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('job_title');
            $table->string('department');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
=======
<<<<<<< HEAD
            $table->unsignedBigInteger('user_profile_id');
            $table->string('job_title');
            $table->string('department');

            $table->foreign('user_profile_id')->references('user_profile_id')->on('users_profiles')->onDelete('cascade');
=======
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('job_title');
            $table->string('department');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('employees');
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
