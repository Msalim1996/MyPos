<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_ref_no')->nullable();
            $table->datetime('order_date');
            $table->string('remark')->nullable();
            $table->string('fulfillment_remark')->nullable();
            $table->string('return_remark')->nullable();
            $table->string('restock_remark')->nullable();
            $table->string('payment_remark')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->unsignedBigInteger('location_id')->index();
            $table->unsignedBigInteger('customer_id')->index()->nullable();
            $table->unsignedBigInteger('customer_address_id')->index()->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('customer_address_id')->references('id')->on('customer_addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_orders');
    }
}
