<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentDateAndReceivedDateOnTransferOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_orders', function (Blueprint $table) {
            $table->datetime('sent_at')->nullable();
            $table->datetime('received_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_orders', function (Blueprint $table) {
            $table->dropColumn('sent_at');
            $table->dropColumn('received_at');
        });
    }
}
