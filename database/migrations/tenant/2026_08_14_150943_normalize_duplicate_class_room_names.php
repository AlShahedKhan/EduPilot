<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $seen = [];

        foreach (DB::table('class_rooms')->orderBy('school_id')->orderBy('id')->get() as $classRoom) {
            $schoolId = (int) $classRoom->school_id;
            $baseName = Str::squish((string) $classRoom->name);
            $baseName = $baseName !== '' ? $baseName : 'Room '.$classRoom->id;
            $candidate = Str::limit($baseName, 255, '');
            $suffixNumber = 2;

            while (isset($seen[$schoolId][Str::lower($candidate)])) {
                $suffix = ' ('.$suffixNumber.')';
                $candidate = Str::limit($baseName, 255 - Str::length($suffix), '').$suffix;
                $suffixNumber++;
            }

            $seen[$schoolId][Str::lower($candidate)] = true;

            if ($candidate !== $classRoom->name) {
                DB::table('class_rooms')->where('id', $classRoom->id)->update(['name' => $candidate]);
            }
        }
    }

    public function down(): void
    {
        // Normalized and suffixed names cannot be restored safely.
    }
};
