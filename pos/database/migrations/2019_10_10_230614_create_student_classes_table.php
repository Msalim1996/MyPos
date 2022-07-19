<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_id')->nullable();
            $table->string('age_range')->nullable();
            $table->datetime('date_start')->nullable();
            $table->datetime('date_expired')->nullable();
            $table->string('remark')->nullable();
            $table->unsignedBigInteger('level_id')->index()->nullable();
            $table->unsignedBigInteger('coach_id')->index();
            $table->unsignedBigInteger('course_id')->index();
            $table->datetime('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('coach_id')->references('id')->on('coaches');
            $table->foreign('course_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
