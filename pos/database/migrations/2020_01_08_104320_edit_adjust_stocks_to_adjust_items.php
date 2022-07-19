<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAdjustStocksToAdjustItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('adjust_stocks', 'adjust_items');

        Schema::table('adjust_items', function (Blueprint $table) {
            $table->dropColumn('adjust_ref_no');
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
            $table->string('adjust_ref_no');
        });

        Schema::rename('adjust_items', 'adjust_stocks');
    }
}
