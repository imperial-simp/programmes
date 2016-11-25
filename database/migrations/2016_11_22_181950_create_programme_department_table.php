<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgrammeDepartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programme_department', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('department_id')->unsigned()->nullable();
            $table->integer('programme_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments');
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
        Schema::dropIfExists('programme_department');
    }
}
