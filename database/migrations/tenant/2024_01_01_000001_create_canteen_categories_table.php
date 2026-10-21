<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('canteen_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id');
            $table->string('name', 100);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_categories');
    }
}
