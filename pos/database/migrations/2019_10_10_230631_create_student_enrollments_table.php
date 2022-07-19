<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('enrollment_status')->nullable();
            $table->unsignedBigInteger('member_id')->index();
            $table->unsignedBigInteger('student_class_id')->index();
            $table->timestamps();
            
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('student_class_id')->references('id')->on('student_classes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_enrollments');
    }
}
