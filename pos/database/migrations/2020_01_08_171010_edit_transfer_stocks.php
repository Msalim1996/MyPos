<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditTransferStocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('transfer_stocks', 'tranfer_items');

        Schema::table('tranfer_items', function (Blueprint $table) {
            $table->dropColumn('transfer_ref_no');
            $table->dropColumn('transfer_status');
            $table->unsignedBigInteger('transfer_order_id')->nullable();
            $table->foreign('transfer_order_id')->references('id')->on('transfer_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->dropForeign(['transfer_order_id']);
            $table->dropColumn('transfer_order_id');
            $table->string('transfer_ref_no');
            $table->string('transfer_status');
        });

        Schema::rename('transfer_items', 'tranfer_stocks');
    }
}
