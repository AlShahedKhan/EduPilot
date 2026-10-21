<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id');
            $table->foreignId('section_id');
            $table->foreignId('subject_id');
            $table->integer('starting_hour');
            $table->integer('ending_hour');
            $table->integer('starting_minute');
            $table->integer('ending_minute');
            $table->string('day');
            $table->foreignId('teacher_id');
            $table->foreignId('room_id');
            $table->foreignId('session_id');
            $table->foreignId('school_id');
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
        Schema::dropIfExists('routines');
    }
}
