<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skate_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode_id');
            $table->dateTime('rent_start')->nullable();
            $table->dateTime('rent_end')->nullable();
            $table->string('skate_size')->nullable();
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
        Schema::dropIfExists('skate_transactions');
    }
}
