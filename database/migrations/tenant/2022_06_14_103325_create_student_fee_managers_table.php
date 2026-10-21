<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentFeeManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_fee_managers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('total_amount');
            $table->integer('amount')->nullable();
            $table->integer('discounted_price')->nullable();
            $table->foreignId('class_id');
            $table->foreignId('student_id');
            $table->foreignId('parent_id')->nullable();
            $table->string('payment_method');
            $table->string('document_image')->nullable();
            $table->integer('paid_amount');
            $table->string('status');
            $table->foreignId('school_id');
            $table->foreignId('session_id');
            $table->integer('timestamp');
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
        Schema::dropIfExists('student_fee_managers');
    }
}
