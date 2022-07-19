<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPaymentDatePurchaseFulfillmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_fulfillments', function (Blueprint $table){
            $table->datetime('fulfilled_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_fulfillments', function (Blueprint $table){
            $table->date('fulfilled_date')->change();
        });
    }
}
