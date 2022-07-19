<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypeInSkatingAidTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_order_id');
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
            $table->dropColumn('sales_order_id');
        });
    }
}
