<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeCalculatedColumnNullableOmAttendanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->decimal('late_deduction',14,2)->nullable()->change();
            $table->integer('late_duration')->nullable()->change();
            $table->decimal('overtime_pay',14,2)->nullable()->change();
            $table->integer('overtime_duration')->nullable()->change();
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
            $table->decimal('late_deduction',14,2)->nullable(false)->change();
            $table->integer('late_duration')->nullable(false)->change();
            $table->decimal('overtime_pay',14,2)->nullable(false)->change();
            $table->integer('overtime_duration')->nullable(false)->change();
        });
    }
}
