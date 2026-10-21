<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCanteenItemsTable extends Migration
{
    public function up()
    {
        Schema::create('canteen_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id');
            $table->foreignId('category_id');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('canteen_items');
    }
}
