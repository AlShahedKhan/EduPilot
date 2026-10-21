<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('canteen_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id');
            $table->foreignId('student_id');
            $table->string('order_number', 20)->unique();
            $table->decimal('total_amount', 10, 2);
            $table->tinyInteger('status')->default(0); // 0=Pending, 1=Completed, 2=Cancelled
            $table->text('note')->nullable();
            $table->foreignId('ordered_by')->nullable();
            $table->integer('timestamp');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_orders');
    }
}
