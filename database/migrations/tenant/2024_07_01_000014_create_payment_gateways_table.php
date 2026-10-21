<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status')->nullable();
            $table->longText('configuration');
            $table->integer('created_at');
            $table->integer('updated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
}
