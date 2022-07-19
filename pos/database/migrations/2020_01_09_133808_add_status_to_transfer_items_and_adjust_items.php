<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToTransferItemsAndAdjustItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('tranfer_items', 'transfer_items');
        Schema::table('transfer_items', function (Blueprint $table) {
            $table->string('status')->nullable();
        });

        Schema::table('adjust_items', function (Blueprint $table) {
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('transfer_items', 'tranfer_items');
        Schema::table('tranfer_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('adjust_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
