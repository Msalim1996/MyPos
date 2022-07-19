<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOnDateTypeFromDateTimeToDateOnExtendedShiftDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extended_shift_days', function (Blueprint $table) {
            $table->date('on_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extended_shift_days', function (Blueprint $table) {
            $table->datetime('on_date')->change();
        });
    }
}
