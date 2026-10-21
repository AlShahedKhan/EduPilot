<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_thrade')->nullable();
            $table->foreignId('reciver_id')->nullable();
            $table->foreignId('sender_id')->nullable();
            $table->longText('message')->nullable();
            $table->foreignId('reply_id')->nullable();
            $table->foreignId('school_id')->nullable();
            $table->integer('read_status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}
