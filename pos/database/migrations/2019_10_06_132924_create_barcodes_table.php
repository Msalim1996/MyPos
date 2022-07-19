<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode_id', 50)->unique();
            $table->timestamp('active_on')->nullable();
            $table->unsignedBigInteger('sales_order_id')->index();
            $table->string('session_name')->nullable();
            $table->string('session_day')->nullable();
            $table->time('session_start_time')->nullable();
            $table->time('session_end_time')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barcodes');
    }
}
