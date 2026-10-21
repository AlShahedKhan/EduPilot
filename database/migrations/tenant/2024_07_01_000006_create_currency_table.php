<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyTable extends Migration
{
    public function up()
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('code', 255)->nullable();
            $table->string('symbol', 255)->nullable();
            $table->integer('paypal_supported')->nullable();
            $table->integer('stripe_supported')->nullable();
            $table->integer('flutterwave_supported');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('currency');
    }
}
