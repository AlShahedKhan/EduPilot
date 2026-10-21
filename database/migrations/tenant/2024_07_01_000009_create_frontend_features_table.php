<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontendFeaturesTable extends Migration
{
    public function up()
    {
        Schema::create('frontend_features', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('icon', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('frontend_features');
    }
}
