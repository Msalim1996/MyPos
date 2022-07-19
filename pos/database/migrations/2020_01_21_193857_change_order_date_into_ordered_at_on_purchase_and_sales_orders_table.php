<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderDateIntoOrderedAtOnPurchaseAndSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->renameColumn('order_date','ordered_at');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->renameColumn('order_date','ordered_at');
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
            $table->renameColumn('ordered_at','order_date');
        });
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->renameColumn('ordered_at','order_date');
        });
    }
}
