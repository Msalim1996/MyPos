<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPb1BooleanDppAndTaxInSalesItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->boolean('is_pb1')->nullable();
            $table->decimal('pb1_dpp',14,2)->nullable();
            $table->decimal('pb1_tax',14,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->dropColumn('is_pb1');
            $table->dropColumn('pb1_dpp',14,2);
            $table->dropColumn('pb1_tax',14,2);
        });
    }
}
