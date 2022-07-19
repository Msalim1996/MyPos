<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTransactionsTable extends Migration
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
            $table->unsignedBigInteger('user_attendance_id');
            $table->string('shift_name');
            $table->time('shift_started_on');
            $table->time('shift_ended_on');
            $table->time('checked_in_on');
            $table->time('checked_out_on');
            $table->decimal('pay_per_day',14,2);
            $table->boolean('is_on_leave');
            $table->boolean('is_absent');
            $table->string('reason');
            $table->decimal('late_deduction',14,2);
            $table->time('late_duration');
            $table->string('overtime_pay');
            $table->time('overtime_duration');
            $table->decimal('calculated_pay',14,2);
            $table->decimal('payment_adjustment',14,2);
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
        Schema::dropIfExists('attendance_transactions');
    }
}
