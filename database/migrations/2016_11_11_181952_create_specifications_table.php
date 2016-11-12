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
            $table->string('mime')->default('text/plain');
            $table->string('url')->nullable();
            $table->string('hash')->unique();
            $table->string('etag')->unique()->nullable();
            $table->string('parser')->nullable();
            $table->text('details')->nullable();
            $table->text('contents')->nullable();
            $table->timestamp('retrieved_at')->nullable();
            $table->unsignedInteger('institution_id')->nullable();
            $table->unsignedInteger('faculty_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->unsignedInteger('award_id')->nullable();
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('faculty_id')->references('id')->on('faculties');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('award_id')->references('id')->on('awards');
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
