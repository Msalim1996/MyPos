<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropExtendedShiftDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('extended_shift_days');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('extended_shift_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('on_date');
            $table->string('name');
            $table->timestamps();
        });
    }
}
