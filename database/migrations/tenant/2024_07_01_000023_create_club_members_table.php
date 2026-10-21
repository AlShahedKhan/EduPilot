<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubMembersTable extends Migration
{
    public function up()
    {
        Schema::create('club_members', function (Blueprint $table) {
            $table->unsignedInteger('id', true);
            $table->foreignId('club_id');
            $table->foreignId('student_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('club_members');
    }
}
