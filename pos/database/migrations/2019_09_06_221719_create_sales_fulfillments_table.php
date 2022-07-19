<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_fulfillments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description')->nullable();
            $table->decimal('qty', 14, 6)->default(0);
            $table->datetime('fulfilled_date');
            $table->unsignedBigInteger('location_id')->index();
            $table->unsignedBigInteger('sales_order_id')->index();
            $table->unsignedBigInteger('sales_item_id')->index();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
            $table->foreign('sales_item_id')->references('id')->on('sales_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_fulfillments');
    }
}
