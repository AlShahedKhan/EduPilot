<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('feedback_text')->nullable();
            $table->foreignId('student_id')->nullable();
            $table->foreignId('admin_id')->nullable();
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('class_id')->nullable();
            $table->foreignId('section_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('session_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
