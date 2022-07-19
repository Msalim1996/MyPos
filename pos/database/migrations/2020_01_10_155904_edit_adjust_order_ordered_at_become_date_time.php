<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAdjustOrderOrderedAtBecomeDateTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjust_orders', function (Blueprint $table) {
            $table->datetime('ordered_at')->nullable()->change();
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
            $table->date('ordered_at')->nullable()->change();
        });
    }
}
