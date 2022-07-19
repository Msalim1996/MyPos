<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShortcutProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shortcut_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shortcut_key')->nullable();
            $table->string('category')->nullable();
            $table->integer('position_index');
            $table->unsignedBigInteger('item_id')->index();
            $table->unsignedBigInteger('shortcut_day_type_id')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('shortcut_day_type_id')->references('id')->on('shortcut_day_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shortcut_products');
    }
}
