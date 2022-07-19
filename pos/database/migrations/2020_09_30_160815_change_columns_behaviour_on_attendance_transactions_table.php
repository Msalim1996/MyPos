<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsBehaviourOnAttendanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->boolean('is_excluded')->default(0)->change();
            $table->string('description')->nullable()->change();
            $table->string('pic')->nullable()->change();
            $table->string('verified_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->string('description')->nullable(0)->change();
            $table->string('pic')->nullable(0)->change();
            $table->string('verified_by')->nullable(0)->change();
        });
    }
}
