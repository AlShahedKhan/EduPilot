<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('student_id')->nullable();
            $table->foreignId('hostel_id')->nullable();
            $table->foreignId('room_id')->nullable();
            $table->string('status', 255)->nullable();
            $table->longText('note')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_applications');
    }
}
