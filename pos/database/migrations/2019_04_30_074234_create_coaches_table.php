<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoachesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('coaches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('coach_id')->unique();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->string('level')->nullable();    // specific to skating academy coach
            $table->string('type')->nullable();     // specific to skating academy coach
            $table->string('category')->nullable(); // specific to skating academy coach
            $table->string('language')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('remark')->nullable();
            $table->decimal('private_rate', 6, 2)->nullable()->default(0);
            $table->decimal('semi_private_rate', 6, 2)->nullable()->default(0);
            $table->decimal('group_rate', 6, 2)->nullable()->default(0);
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
        Schema::dropIfExists('coach');
    }
}
