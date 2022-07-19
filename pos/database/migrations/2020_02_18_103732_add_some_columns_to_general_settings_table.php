<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->string('logo')->nullable();
            $table->string('tax_payer')->nullable();
            $table->string('tax_number')->nullable();
            $table->boolean('tax_toggle')->nullable();
            $table->datetime('affirmation_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('logo');
            $table->dropColumn('tax_payer');
            $table->dropColumn('tax_number');
            $table->dropColumn('tax_toggle');
            $table->dropColumn('affirmation_date');
        });
    }
}
