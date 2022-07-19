<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('remark')->nullable();

            $table->dropColumn('description');
            $table->renameColumn('phone_number','phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('website');
            $table->dropColumn('fax');
            $table->dropColumn('email');
            $table->dropColumn('remark');
            $table->renameColumn('phone','phone_number');
            $table->string('description')->nullable();
        });
    }
}
