<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('course_id')->nullable();
            $table->string('name')->nullable();
            $table->string('course_type')->nullable();
            $table->string('day_type')->nullable();
            $table->string('coach_type')->nullable();
            $table->string('description')->nullable();
            $table->decimal('price', 14, 2)->default(0);
            $table->integer('number_of_students_from')->nullable();
            $table->integer('number_of_students_to')->nullable();
            $table->integer('number_of_lessons')->nullable();
            $table->unsignedBigInteger('level_group_id')->index();
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('level_group_id')->references('id')->on('level_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
