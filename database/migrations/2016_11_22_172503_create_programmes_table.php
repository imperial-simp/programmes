<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('oss_code')->unique();
            $table->string('banner_code')->nullable();

            $table->string('long_title')->nullable();
            $table->string('oss_title')->nullable();
            $table->string('banner_title')->nullable();

            $table->string('level', 4)->default('UG');

            $table->integer('award_id')->unsigned()->nullable();
            $table->integer('joint_award_id')->unsigned()->nullable();
            $table->integer('calendar_id')->unsigned()->nullable();

            $table->json('contents')->nullable();

            $table->timestamps();

            $table->foreign('award_id')->references('id')->on('awards');
            $table->foreign('joint_award_id')->references('id')->on('awards');
            $table->foreign('calendar_id')->references('id')->on('calendars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmes');
    }
}
