<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubNoticesTable extends Migration
{
    public function up()
    {
        Schema::create('club_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id');
            $table->foreignId('admin_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->date('notice_date');
            $table->string('image', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreignId('advisor_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_notices');
    }
}
