<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->longText('school_info')->nullable();
            $table->integer('status');
            $table->integer('running_session')->nullable();
            $table->string('school_currency')->nullable();
            $table->string('currency_position')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('email_title')->nullable();
            $table->string('email_details')->nullable();
            $table->string('warning_text')->nullable();
            $table->string('socialLink1')->nullable();
            $table->string('socialLink2')->nullable();
            $table->string('socialLink3')->nullable();
            $table->string('email_logo')->nullable();
            $table->string('socialLogo1')->nullable();
            $table->string('socialLogo2')->nullable();
            $table->string('socialLogo3')->nullable();
            $table->string('off_pay_ins_text')->nullable();
            $table->string('off_pay_ins_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
}
