<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppraisalsTable extends Migration
{
    public function up()
    {
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('question')->nullable();
            $table->string('ans_type', 255)->nullable();
            $table->foreignId('teacher_id')->nullable();
            $table->foreignId('class_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('appraisals');
    }
}
