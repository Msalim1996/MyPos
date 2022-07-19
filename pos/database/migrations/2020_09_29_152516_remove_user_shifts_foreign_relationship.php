<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserShiftsForeignRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_shifts', function (Blueprint $table) {
            $table->dropForeign(['user_attendance_id']);
            $table->dropForeign(['shift_id']);

            $table->dropColumn('user_attendance_id');
            $table->dropColumn('shift_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_attendance_id');
            $table->unsignedBigInteger('shift_id');
            
            $table->dropForeign('user_attendance_id')->references('id')->on('user_attendances');
            $table->dropForeign('shift_id')->references('id')->on('shifts');
        });
    }
}
