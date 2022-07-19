<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUserExtendedShiftsForeignRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_extended_shifts', function (Blueprint $table) {
            $table->dropForeign('user_extended_shift_user_attendance_id_foreign');
            $table->dropForeign('user_extended_shift_shift_id_foreign');
            $table->dropForeign('user_extended_shift_extended_shift_day_id_foreign');

            $table->dropColumn('user_attendance_id');
            $table->dropColumn('shift_id');
            $table->dropColumn('extended_shift_day_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_extended_shifts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_attendance_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('extended_shift_day_id');

            $table->foreign('user_attendance_id')->references('id')->on('user_attendances');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('extended_shift_day_id')->references('id')->on('extended_shift_days');
        });
    }
}
