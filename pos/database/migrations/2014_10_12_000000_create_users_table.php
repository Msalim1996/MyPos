<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username')->unique();
            $table->string('position')->nullable();
            $table->date('date_join');
            $table->date('date_left')->nullable();
            $table->string('starting_position')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender');
            $table->string('religion')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('remark')->nullable();
            $table->string('name');
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
