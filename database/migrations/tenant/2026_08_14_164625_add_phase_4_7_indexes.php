<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('syllabuses', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'class_id', 'section_id', 'subject_id'], 'syllabuses_scope_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'section_id', 'day'], 'routines_section_day_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'teacher_id', 'day'], 'routines_teacher_day_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'room_id', 'day'], 'routines_room_day_index'));
        Schema::table('daily_attendances', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'class_id', 'section_id', 'timestamp'], 'attendance_report_index'));
        Schema::table('gradebooks', fn (Blueprint $table) => $table->index(['school_id', 'session_id', 'class_id', 'section_id', 'exam_category_id'], 'gradebooks_report_index'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('syllabuses', fn (Blueprint $table) => $table->dropIndex('syllabuses_scope_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->dropIndex('routines_section_day_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->dropIndex('routines_teacher_day_index'));
        Schema::table('routines', fn (Blueprint $table) => $table->dropIndex('routines_room_day_index'));
        Schema::table('daily_attendances', fn (Blueprint $table) => $table->dropIndex('attendance_report_index'));
        Schema::table('gradebooks', fn (Blueprint $table) => $table->dropIndex('gradebooks_report_index'));
    }
};
