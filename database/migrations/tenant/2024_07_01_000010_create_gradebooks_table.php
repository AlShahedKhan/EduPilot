<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradebooksTable extends Migration
{
    public function up()
    {
        Schema::create('gradebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id');
            $table->foreignId('section_id');
            $table->foreignId('student_id');
            $table->foreignId('exam_category_id');
            $table->string('marks', 255)->nullable();
            $table->string('comment');
            $table->foreignId('school_id');
            $table->foreignId('session_id');
            $table->integer('timestamp');
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gradebooks');
    }
}
