<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('fulfillment_remark')->nullable();
            $table->string('payment_remark')->nullable();
            $table->string('remark')->nullable();
            
            $table->renameColumn('deadline','order_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('fulfillment_remark');
            $table->dropColumn('payment_remark');
            $table->dropColumn('remark');
            
            $table->renameColumn('order_date','deadline');
        });
    }
}
