<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovedRelationshipsInSkatingAidTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table) {
            $table->dropForeign(['skating_aid_id']);
            $table->dropForeign(['sales_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table) {
            $table->foreign('skating_aid_id')->references('id')->on('skating_aids');
            $table->foreign('sales_order_id')->references('id')->on('sales_orders');
        });
    }
}
