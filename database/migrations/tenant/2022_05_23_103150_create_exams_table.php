<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('exam_type');
            $table->string('starting_time');
            $table->string('ending_time');
            $table->float('total_marks');
            $table->string('status');
            $table->foreignId('class_id');
            $table->foreignId('subject_id');
            $table->foreignId('school_id');
            $table->foreignId('session_id');
            $table->integer('exam_category_id')->nullable();
            $table->string('room_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
