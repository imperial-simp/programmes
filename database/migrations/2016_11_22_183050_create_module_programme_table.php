<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleProgrammeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_programme', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('programme_id')->unsigned()->nullable();
            $table->integer('module_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('programme_id')->references('id')->on('programmes');
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
        Schema::dropIfExists('module_programme');
    }
}
