<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserShiftExtendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extended_shift', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_attendance_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('extended_shift_day_id');

            $table->foreign('user_attendance_id')->references('id')->on('user_attendances');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('extended_shift_day_id')->references('id')->on('extended_shift_days');
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
        Schema::dropIfExists('user_extended_shift');
    }
}
