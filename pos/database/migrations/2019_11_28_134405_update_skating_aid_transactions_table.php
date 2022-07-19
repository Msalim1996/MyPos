<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSkatingAidTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table) {
            $table->dropColumn('barcode_skating_aid_id');
            $table->dropColumn('barcode_skater_id');
            $table->dropColumn('barcode_skater_name');

            $table->unsignedBigInteger('barcode_id')->nullable();
            $table->unsignedBigInteger('skating_aid_id')->nullable();

            $table->foreign('skating_aid_id')->references('id')->on('skating_aids');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skating_aid_transactions', function (Blueprint $table) {
            $table->string('barcode_skating_aid_id')->nullable();
            $table->string('barcode_skater_id')->nullable();
            $table->string('barcode_skater_name')->nullable();

            $table->dropForeign(['skating_aid_id']);

            $table->dropColumn('barcode_id')->nullable();
            $table->dropColumn('skating_aid_id')->nullable();
        });
    }
}
