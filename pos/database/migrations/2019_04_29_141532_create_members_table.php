<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('member_id')->unique();
            $table->string('email')->nullable();
            $table->string('name');
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
