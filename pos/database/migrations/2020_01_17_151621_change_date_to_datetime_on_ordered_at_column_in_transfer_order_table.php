<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateToDatetimeOnOrderedAtColumnInTransferOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_orders', function (Blueprint $table) {
            $table->datetime('ordered_at')->change();
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
            $table->date('ordered_at')->change();
        });
    }
}
