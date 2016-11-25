<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_specification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('specification_id')->unsigned()->nullable();
            $table->integer('module_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('specification_id')->references('id')->on('specifications');
            $table->foreign('module_id')->references('id')->on('modules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_specification');
    }
}
