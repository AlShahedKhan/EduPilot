<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('payment_keys');
            $table->string('image', 255)->nullable();
            $table->integer('status')->default(0);
            $table->string('mode')->default('test');
            $table->string('created_at', 255)->nullable();
            $table->string('updated_at', 255)->nullable();
            $table->foreignId('school_id')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
}
