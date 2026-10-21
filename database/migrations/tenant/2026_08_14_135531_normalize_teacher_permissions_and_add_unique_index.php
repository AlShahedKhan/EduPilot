<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('teacher_permissions')->whereNull('marks')->update(['marks' => 0]);
        DB::table('teacher_permissions')->whereNull('attendance')->update(['attendance' => 0]);

        $duplicates = DB::table('teacher_permissions')
            ->select(['school_id', 'class_id', 'section_id', 'teacher_id'])
            ->selectRaw('MIN(id) as keeper_id, MAX(marks) as marks, MAX(attendance) as attendance')
            ->groupBy(['school_id', 'class_id', 'section_id', 'teacher_id'])
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('teacher_permissions')->where('id', $duplicate->keeper_id)->update([
                'marks' => $duplicate->marks,
                'attendance' => $duplicate->attendance,
            ]);
            DB::table('teacher_permissions')
                ->where('school_id', $duplicate->school_id)
                ->where('class_id', $duplicate->class_id)
                ->where('section_id', $duplicate->section_id)
                ->where('teacher_id', $duplicate->teacher_id)
                ->where('id', '!=', $duplicate->keeper_id)
                ->delete();
        }

        Schema::table('teacher_permissions', function (Blueprint $table): void {
            $table->unique(
                ['school_id', 'class_id', 'section_id', 'teacher_id'],
                'teacher_permissions_scope_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('teacher_permissions', function (Blueprint $table): void {
            $table->dropUnique('teacher_permissions_scope_unique');
        });
    }
};
