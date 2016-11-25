<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgrammeSpecificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programme_specification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('specification_id')->unsigned()->nullable();
            $table->integer('programme_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('specification_id')->references('id')->on('specifications');
            $table->foreign('programme_id')->references('id')->on('programmes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programme_specification');
    }
}
