<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_ref_no')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->string('payment_method')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_payments');
    }
}
