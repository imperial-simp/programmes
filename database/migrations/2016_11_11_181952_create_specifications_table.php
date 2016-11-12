<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('specifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('file')->nullable()->unique();
            $table->string('mime')->nullable();
            $table->string('url')->nullable();
            $table->string('hash')->unique();
            $table->string('etag')->nullable();
            $table->string('parser')->nullable();
            $table->text('details')->nullable();
            $table->text('contents')->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->timestamp('parsed_at')->nullable();
            $table->unsignedInteger('source_id')->nullable();
            $table->unsignedInteger('institution_id')->nullable();
            $table->unsignedInteger('faculty_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->unsignedInteger('award_id')->nullable();
            $table->unsignedInteger('calendar_id')->nullable();
            $table->unsignedInteger('joint_award_id')->nullable();
            $table->timestamps();

            $table->foreign('source_id')->references('id')->on('sources');
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('faculty_id')->references('id')->on('faculties');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('award_id')->references('id')->on('awards');
            $table->foreign('calendar_id')->references('id')->on('calendars');
            $table->foreign('joint_award_id')->references('id')->on('award');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specifications');
    }
}
