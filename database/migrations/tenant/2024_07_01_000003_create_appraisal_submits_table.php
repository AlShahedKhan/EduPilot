<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalSubmitsTable extends Migration
{
    public function up()
    {
        Schema::create('appraisal_submits', function (Blueprint $table) {
            $table->id();
            $table->text('answers')->nullable();
            $table->foreignId('teacher_id')->nullable();
            $table->foreignId('apprasial_id')->nullable();
            $table->foreignId('student_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appraisal_submits');
    }
}
