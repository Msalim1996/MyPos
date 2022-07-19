<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDateTimeToDateForContractsOnStaffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->date('contract_started_on')->nullable()->change();
            $table->date('contract_ended_on')->nullable()->change();
            $table->date('contract_changed_on')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dateTime('contract_started_on')->change();
            $table->dateTime('contract_ended_on')->change();
            $table->dateTime('contract_changed_on')->change();
        });
    }
}
