<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->longText('expense_type');
            $table->integer('expense_id');
            $table->foreignId('user_id');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->longText('transaction_keys');
            $table->string('created_at', 255)->nullable();
            $table->string('updated_at', 255)->nullable();
            $table->foreignId('school_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
