<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTable extends Migration
{
    public function up()
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faq');
    }
}
