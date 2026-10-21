<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table): void {
            $table->unique(['school_id', 'name'], 'classes_school_name_unique');
        });

        Schema::table('sections', function (Blueprint $table): void {
            $table->unique(['class_id', 'name'], 'sections_class_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table): void {
            $table->dropUnique('sections_class_name_unique');
        });

        Schema::table('classes', function (Blueprint $table): void {
            $table->dropUnique('classes_school_name_unique');
        });
    }
};
