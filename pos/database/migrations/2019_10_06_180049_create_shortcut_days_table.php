<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortcutDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shortcut_days', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('on_date');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('shortcut_day_type_id')->nullable();
            $table->timestamps();
            
            $table->foreign('shortcut_day_type_id')->references('id')->on('shortcut_day_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shortcut_days');
    }
}
