<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIntegerToDecimalInTaxToItemsAndSalesItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('tax',14,2)->change();
        });
        Schema::table('sales_items', function (Blueprint $table) {
            $table->decimal('tax',14,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('tax')->change();
        });
        Schema::table('sales_items', function (Blueprint $table) {
            $table->integer('tax')->change();
        });
    }
}
