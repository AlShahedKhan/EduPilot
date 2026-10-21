<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('canteen_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id');
            $table->foreignId('order_id');
            $table->foreignId('item_id');
            $table->string('item_name', 150);
            $table->decimal('item_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_order_items');
    }
}
