<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelRoomAllocationsTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_room_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('student_id')->nullable();
            $table->foreignId('room_id')->nullable();
            $table->string('allocated_on', 255)->nullable();
            $table->string('vacated_on', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_room_allocations');
    }
}
