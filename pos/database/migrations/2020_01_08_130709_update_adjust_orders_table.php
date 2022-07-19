<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAdjustOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjust_orders', function (Blueprint $table) {
            $table->renameColumn('adjust_status','status');
            $table->string('remark');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjust_orders', function (Blueprint $table) {
            $table->renameColumn('status','adjust_status');
            $table->dropColumn('remark');
        });
    }
}
