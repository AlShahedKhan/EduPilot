<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguageTable extends Migration
{
    public function up()
    {
        Schema::create('language', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('phrase', 300)->nullable();
            $table->string('translated', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('language');
    }
}
