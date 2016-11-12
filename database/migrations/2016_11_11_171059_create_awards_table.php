<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('short_name')->nullable();
            $table->string('abbrev')->nullable();
            $table->string('oss_code')->nullable();
            $table->string('banner_code')->nullable();
            $table->unsignedInteger('award_type_id')->nullable();
            $table->timestamps();

            $table->foreign('award_type_id')->references('id')->on('award_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awards');
    }
}
