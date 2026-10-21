<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmitCardsTable extends Migration
{
    public function up()
    {
        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('template');
            $table->string('heading', 255)->nullable();
            $table->string('title', 255)->nullable();
            $table->foreignId('school_id')->nullable();
            $table->string('exam_center', 255)->nullable();
            $table->string('footer_text', 255)->nullable();
            $table->string('left_logo', 255)->nullable();
            $table->string('right_logo', 255)->nullable();
            $table->string('sign', 255)->nullable();
            $table->string('background_image', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admit_cards');
    }
}
