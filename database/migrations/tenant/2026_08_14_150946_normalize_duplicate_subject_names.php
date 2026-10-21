<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $seen = [];

        foreach (DB::table('subjects')->orderBy('school_id')->orderBy('class_id')->orderBy('session_id')->orderBy('id')->get() as $subject) {
            $scope = implode(':', [(int) $subject->school_id, (int) $subject->class_id, (int) $subject->session_id]);
            $baseName = Str::squish((string) $subject->name);
            $baseName = $baseName !== '' ? $baseName : 'Subject '.$subject->id;
            $candidate = Str::limit($baseName, 255, '');
            $suffixNumber = 2;

            while (isset($seen[$scope][Str::lower($candidate)])) {
                $suffix = ' ('.$suffixNumber.')';
                $candidate = Str::limit($baseName, 255 - Str::length($suffix), '').$suffix;
                $suffixNumber++;
            }

            $seen[$scope][Str::lower($candidate)] = true;

            if ($candidate !== $subject->name) {
                DB::table('subjects')->where('id', $subject->id)->update(['name' => $candidate]);
            }
        }
    }

    public function down(): void
    {
        // Normalized and suffixed names cannot be restored safely.
    }
};
