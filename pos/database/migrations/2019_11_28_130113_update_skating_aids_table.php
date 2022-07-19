<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSkatingAidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skating_aids', function (Blueprint $table) {
            $table->string('skating_aid_code')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('rent')->default(0);

            $table->dropColumn('barcode_id');
        });      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('skating_aids', function (Blueprint $table) {
            $table->dropColumn('skating_aid_code');
            $table->dropColumn('stock');
            $table->dropColumn('rent');

            $table->string('barcode_id', 50)->unique();
        });
    }
}
