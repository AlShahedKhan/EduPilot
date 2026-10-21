<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyllabusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabuses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('class_id');
            $table->foreignId('section_id');
            $table->foreignId('subject_id');
            $table->string('file');
            $table->foreignId('school_id');
            $table->foreignId('session_id');
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
        Schema::dropIfExists('syllabuses');
    }
}
