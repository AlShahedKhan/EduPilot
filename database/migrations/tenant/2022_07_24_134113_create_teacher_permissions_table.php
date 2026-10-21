<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable();
            $table->foreignId('section_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('teacher_id')->nullable();
            $table->integer('marks')->nullable();
            $table->integer('attendance')->nullable();
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
        Schema::dropIfExists('teacher_permissions');
    }
};
