<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModeToProgrammes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->integer('programme_type_id')->unsigned()->nullable();
            $table->integer('duration')->unsigned()->nullable();
            $table->string('measure')->default('Y');
            $table->string('mode')->nullable();
            $table->integer('joint_duration')->unsigned()->nullable();
            $table->string('joint_measure')->nullable();
            $table->string('joint_mode')->nullable();
            $table->string('entry')->nullable();
            $table->json('flags')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('programme_type_id');
            $table->dropColumn('duration');
            $table->dropColumn('joint_duration');
            $table->dropColumn('measure');
            $table->dropColumn('joint_measure');
            $table->dropColumn('mode');
            $table->dropColumn('entry');
            $table->dropColumn('flags');
        });
    }
}
