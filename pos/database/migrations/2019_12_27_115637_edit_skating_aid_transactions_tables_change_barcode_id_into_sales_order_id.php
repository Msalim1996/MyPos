<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSkatingAidTransactionsTablesChangeBarcodeIdIntoSalesOrderId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table){
            $table->dropColumn('barcode_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table){
            $table->string('barcode_id');
        });
    }
}
