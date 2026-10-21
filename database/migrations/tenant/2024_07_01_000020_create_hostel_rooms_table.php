<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelRoomsTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('hostel_id')->nullable();
            $table->string('room_no', 255)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('occupied')->nullable();
            $table->integer('seat_fee')->nullable();
            $table->longText('description')->nullable();
            $table->string('status', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_rooms');
    }
}
