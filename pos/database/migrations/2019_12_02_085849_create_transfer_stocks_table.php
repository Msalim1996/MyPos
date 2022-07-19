<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransferStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('current_location_id')->nullable();
            $table->unsignedBigInteger('destination_location_id')->nullable();
            $table->string('description')->nullable();
            $table->string('transfer_ref_no')->nullable();
            $table->string('transfer_status');
            $table->decimal('qty',14,2);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('current_location_id')->references('id')->on('locations');
            $table->foreign('destination_location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_stocks');
    }
}
