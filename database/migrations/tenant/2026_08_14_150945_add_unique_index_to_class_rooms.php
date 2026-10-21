<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_rooms', function (Blueprint $table): void {
            $table->unique(['school_id', 'name'], 'class_rooms_school_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table): void {
            $table->dropUnique('class_rooms_school_name_unique');
        });
    }
};
