<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_shifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('day');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('user_attendance_id');

            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('user_attendance_id')->references('id')->on('user_attendances');
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
        Schema::dropIfExists('user_shifts');
    }
}
