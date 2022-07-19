<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAdjustItemsRenameColumnAndAddDifferenceColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjust_items', function (Blueprint $table) {
            $table->renameColumn('qty','old_qty');
            $table->integer('difference')->default(0);
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
            $table->renameColumn('old_qty','qty');
            $table->dropColumn('difference');
        });
    }
}
