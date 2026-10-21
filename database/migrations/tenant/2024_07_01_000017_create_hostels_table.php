<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelsTable extends Migration
{
    public function up()
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->foreignId('warden_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostels');
    }
}
