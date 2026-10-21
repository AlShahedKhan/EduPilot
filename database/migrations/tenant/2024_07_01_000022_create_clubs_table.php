<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('club_name');
            $table->longText('description')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();
            $table->foreignId('advisor_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clubs');
    }
}
