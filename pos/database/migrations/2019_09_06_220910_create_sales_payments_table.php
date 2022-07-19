<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_ref_no')->nullable();
            $table->string('description')->nullable();
            $table->datetime('payment_date');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('type')->nullable();
            $table->unsignedBigInteger('sales_order_id')->index();
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
        Schema::dropIfExists('sales_payments');
    }
}
