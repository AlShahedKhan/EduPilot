<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostelFeesTable extends Migration
{
    public function up()
    {
        Schema::create('hostel_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable();
            $table->foreignId('hostel_id')->nullable();
            $table->foreignId('student_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('document_image', 255)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->date('fee_payment_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hostel_fees');
    }
}
