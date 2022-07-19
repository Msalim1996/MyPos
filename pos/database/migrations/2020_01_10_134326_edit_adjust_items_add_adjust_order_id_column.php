<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAdjustItemsAddAdjustOrderIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjust_items', function (Blueprint $table) {
            $table->unsignedBigInteger('adjust_order_id')->nullable();
            $table->foreign('adjust_order_id')->references('id')->on('adjust_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjust_items', function (Blueprint $table) {
            $table->dropForeign(['adjust_order_id']);
            $table->dropColumn('adjust_order_id');
        });
    }
}
