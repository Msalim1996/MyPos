<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_fulfillments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->unsignedBigInteger('purchase_item_id')->index();
            $table->decimal('qty', 14, 2)->default(0);
            $table->string('description')->nullable();
            $table->date('fulfilled_date')->nullable();
            $table->unsignedBigInteger('location_id')->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('purchase_item_id')->references('id')->on('purchase_items');
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_fulfillments');
    }
}
