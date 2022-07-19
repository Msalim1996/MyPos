<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pre_qty')->nullable();
            $table->unsignedBigInteger('pre_item_id')->index()->nullable();
            $table->string('pre_type')->nullable();
            $table->integer('benefit_qty')->nullable();
            $table->unsignedBigInteger('benefit_item_id')->index()->nullable();
            $table->decimal('benefit_discount_amount', 14, 2)->nullable();
            $table->string('benefit_discount_type')->nullable();
            $table->string('benefit_type')->nullable();
            $table->boolean('apply_multiply');
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('pre_item_id')->references('id')->on('items');
            $table->foreign('benefit_item_id')->references('id')->on('items');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}
