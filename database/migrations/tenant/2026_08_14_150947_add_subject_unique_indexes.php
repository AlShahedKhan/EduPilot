<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table): void {
            $table->unique(['school_id', 'class_id', 'session_id', 'name'], 'subjects_school_class_session_name_unique');
            $table->index(['school_id', 'session_id', 'class_id'], 'subjects_school_session_class_index');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table): void {
            $table->dropIndex('subjects_school_session_class_index');
            $table->dropUnique('subjects_school_class_session_name_unique');
        });
    }
};
