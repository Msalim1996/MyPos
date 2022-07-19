<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('position_index')->default(0);
            $table->string('description')->nullable();
            $table->decimal('qty', 14, 6)->default(0);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->unsignedBigInteger('sales_order_id')->index();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->timestamps();

            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('promotion_id')->references('id')->on('promotions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_items');
    }
}
