<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeNames('classes', ['school_id'], 'Class');
        $this->normalizeNames('sections', ['class_id'], 'Section');
    }

    public function down(): void
    {
        // Normalized and suffixed names cannot be restored safely.
    }

    /**
     * @param  list<string>  $scopeColumns
     */
    private function normalizeNames(string $table, array $scopeColumns, string $fallback): void
    {
        $seen = [];
        $query = DB::table($table);

        foreach ($scopeColumns as $column) {
            $query->orderBy($column);
        }

        foreach ($query->orderBy('id')->get() as $record) {
            $scope = implode(':', array_map(fn (string $column): string => (string) $record->{$column}, $scopeColumns));
            $baseName = Str::squish((string) $record->name);
            $baseName = $baseName !== '' ? $baseName : $fallback.' '.$record->id;
            $candidate = Str::limit($baseName, 255, '');
            $suffixNumber = 2;

            while (isset($seen[$scope][Str::lower($candidate)])) {
                $suffix = ' ('.$suffixNumber.')';
                $candidate = Str::limit($baseName, 255 - Str::length($suffix), '').$suffix;
                $suffixNumber++;
            }

            $seen[$scope][Str::lower($candidate)] = true;

            if ($candidate !== $record->name) {
                DB::table($table)->where('id', $record->id)->update(['name' => $candidate]);
            }
        }
    }
};
