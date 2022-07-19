<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePayPerDayNullableOmAttendanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->decimal('pay_per_day',14,2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->decimal('pay_per_day',14,2)->nullable(false)->change();
        });
    }
}
