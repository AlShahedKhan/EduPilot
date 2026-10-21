<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageThradesTable extends Migration
{
    public function up()
    {
        Schema::create('message_thrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reciver_id')->nullable();
            $table->foreignId('sender_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('message_thrades');
    }
}
