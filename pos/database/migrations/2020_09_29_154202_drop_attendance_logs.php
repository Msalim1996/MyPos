<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAttendanceLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('attendance_logs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('verify_mode');
            $table->unsignedBigInteger('in_out_mode');
            $table->dateTime('date');
            $table->unsignedBigInteger('work_code');
            $table->unsignedBigInteger('reserved');
            $table->timestamps();;
            $table->unsignedBigInteger('enroll_number');
        });
    }
}
