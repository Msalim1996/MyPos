<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAttendanceTransactionsForeignRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_transactions', function (Blueprint $table) {
            $table->dropForeign(['user_attendance_id']);

            $table->dropColumn('user_attendance_id');
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
            $table->unsignedBigInteger('user_attendance_id');
            
            $table->dropForeign('user_attendance_id')->references('id')->on('user_attendances');
        });
    }
}
