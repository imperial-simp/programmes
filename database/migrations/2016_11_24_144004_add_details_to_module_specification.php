<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToModuleSpecification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('module_specification', function (Blueprint $table) {
            $table->decimal('ects', 5, 2)->nullable();
            $table->decimal('fheq', 5, 2)->nullable();
            $table->decimal('learning_hours', 5, 2)->nullable();
            $table->decimal('study_hours', 5, 2)->nullable();
            $table->decimal('placement_hours', 5, 2)->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->decimal('exam_weight', 3, 2)->nullable();
            $table->decimal('coursework_weight', 3, 2)->nullable();
            $table->decimal('practical_weight', 3, 2)->nullable();
        });

        Schema::table('module_programme', function (Blueprint $table) {
            $table->json('years')->nullable();
            $table->boolean('core')->default(false);
            $table->json('elective_group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_specification', function (Blueprint $table) {
            $table->dropColumn('ects');
            $table->dropColumn('fheq');
            $table->dropColumn('learning_hours');
            $table->dropColumn('study_hours');
            $table->dropColumn('placement_hours');
            $table->dropColumn('total_hours');
            $table->dropColumn('exam_weight');
            $table->dropColumn('coursework_weight');
            $table->dropColumn('practical_weight');
        });

        Schema::table('module_programme', function (Blueprint $table) {
            $table->dropColumn('years');
            $table->dropColumn('core');
            $table->dropColumn('elective_group');
        });
    }
}
