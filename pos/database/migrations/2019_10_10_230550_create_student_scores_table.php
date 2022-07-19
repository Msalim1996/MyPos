<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_scores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('score');
            $table->string('remark')->nullable();
            $table->unsignedBigInteger('level_id')->index();
            $table->unsignedBigInteger('member_id')->index();
            $table->unsignedBigInteger('certificate_id')->index();
            $table->timestamps();
            
            $table->foreign('level_id')->references('id')->on('levels');
            $table->foreign('member_id')->references('id')->on('members');
            $table->foreign('certificate_id')->references('id')->on('certificates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_scores');
    }
}
