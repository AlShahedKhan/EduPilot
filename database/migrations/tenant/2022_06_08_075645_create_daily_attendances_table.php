<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id');
            $table->foreignId('section_id');
            $table->foreignId('student_id');
            $table->integer('status');
            $table->foreignId('session_id');
            $table->foreignId('school_id');
            $table->integer('timestamp');
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
        Schema::dropIfExists('daily_attendances');
    }
}
