<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('staff_id');
            $table->time('checked_in_on');
            $table->time('checked_out_on');
            $table->string('work_type');
            $table->string('absent_type');
            $table->boolean('is_excluded');
            $table->string('description');
            $table->string('pic');
            $table->string('verified_by');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staffs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_transactions');
    }
}
