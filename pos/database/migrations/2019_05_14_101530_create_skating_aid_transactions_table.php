<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkatingAidTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skating_aid_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode_skating_aid_id');
            $table->string('barcode_skater_id');
            $table->string('barcode_skater_name');
            $table->dateTime('rent_start')->nullable();
            $table->dateTime('rent_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skating_aid_transactions');
    }
}
