<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAttendanceTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('attendance_transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('attendance_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
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
            $table->time('overtime_duration');
            $table->decimal('payment_difference',14,2);
            $table->timestamps();
            $table->date('date_of_transaction');
            $table->decimal('overtime_pay',14,2);
            $table->boolean('is_overtime');
        });
    }
}
