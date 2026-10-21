<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterSchoolTeacherPermissionRequest;
use App\Http\Requests\UpdateSchoolTeacherPermissionRequest;
use App\Models\Classes;
use App\Models\TeacherPermission;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SchoolTeacherPermissionController extends Controller
{
    public function index(FilterSchoolTeacherPermissionRequest $request): View
    {
        $validated = $request->validated();
        $schoolId = (int) $request->user()->school_id;
        $classId = (int) ($validated['class_id'] ?? 0);
        $sectionId = (int) ($validated['section_id'] ?? 0);
        $search = (string) ($validated['search'] ?? '');
        $status = (string) ($validated['status'] ?? '');
        $permissionFilter = (string) ($validated['permission'] ?? '');
        $classes = Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name', 'school_id']);
        $sections = $classId > 0
            ? \App\Models\Section::query()->where('school_id', $schoolId)->where('class_id', $classId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $permissionTotals = TeacherPermission::query()->where('school_id', $schoolId)
            ->when($classId > 0, fn ($query) => $query->where('class_id', $classId))
            ->when($sectionId > 0, fn ($query) => $query->where('section_id', $sectionId));
        $summary = (object) [
            'teachers' => User::query()->where('role_id', 3)->where('school_id', $schoolId)->count(),
            'class_access' => (clone $permissionTotals)->distinct()->count('teacher_id'),
            'marks' => (clone $permissionTotals)->where('marks', true)->distinct()->count('teacher_id'),
            'attendance' => (clone $permissionTotals)->where('attendance', true)->distinct()->count('teacher_id'),
        ];

        $teachers = User::query()
            ->where('role_id', 3)
            ->where('school_id', $schoolId)
            ->with(['teacherPermissions' => fn ($query) => $query
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)])
            ->when($classId === 0 || $sectionId === 0, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when($status === 'active', fn ($query) => $query->where(fn ($query) => $query->whereNull('account_status')->orWhere('account_status', '!=', 'disable')))
            ->when($status === 'disabled', fn ($query) => $query->where('account_status', 'disable'))
            ->when($permissionFilter === 'class_access', fn ($query) => $this->whereHasPermission($query, $schoolId, $classId, $sectionId))
            ->when($permissionFilter === 'marks', fn ($query) => $this->whereHasPermission($query, $schoolId, $classId, $sectionId, 'marks'))
            ->when($permissionFilter === 'attendance', fn ($query) => $this->whereHasPermission($query, $schoolId, $classId, $sectionId, 'attendance'))
            ->when($permissionFilter === 'none', fn ($query) => $query->whereDoesntHave('teacherPermissions', fn ($query) => $query
                ->where('school_id', $schoolId)->where('class_id', $classId)->where('section_id', $sectionId)))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter($validated));

        return view('admin.permission.index', [
            'classes' => $classes,
            'sections' => $sections,
            'teachers' => $teachers,
            'summary' => $summary,
            'classId' => $classId,
            'sectionId' => $sectionId,
            'search' => $search,
            'status' => $status,
            'permissionFilter' => $permissionFilter,
            'canManagePermissions' => Gate::allows('manageTeacherPermissions', User::class),
        ]);
    }

    public function sections(Classes $class): JsonResponse
    {
        Gate::authorize('viewTeacherPermissions', User::class);

        if ((int) $class->school_id !== (int) auth()->user()->school_id) {
            abort(404);
        }

        return response()->json($class->sections()->where('school_id', auth()->user()->school_id)->orderBy('name')->get(['id', 'name']));
    }

    public function update(UpdateSchoolTeacherPermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $schoolId = (int) $request->user()->school_id;
        $classId = (int) $validated['class_id'];
        $sectionId = (int) $validated['section_id'];

        DB::transaction(function () use ($validated, $schoolId, $classId, $sectionId): void {
            $upserts = [];
            $revokedTeacherIds = [];

            foreach ($validated['permissions'] as $teacherId => $permission) {
                if (!(bool) $permission['class_access']) {
                    $revokedTeacherIds[] = (int) $teacherId;
                    continue;
                }

                $upserts[] = [
                    'school_id' => $schoolId,
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'teacher_id' => (int) $teacherId,
                    'marks' => (bool) $permission['marks'],
                    'attendance' => (bool) $permission['attendance'],
                ];
            }

            if ($upserts !== []) {
                TeacherPermission::query()->upsert($upserts, ['school_id', 'class_id', 'section_id', 'teacher_id'], ['marks', 'attendance']);
            }

            if ($revokedTeacherIds !== []) {
                TeacherPermission::query()
                    ->where('school_id', $schoolId)
                    ->where('class_id', $classId)
                    ->where('section_id', $sectionId)
                    ->whereIn('teacher_id', $revokedTeacherIds)
                    ->delete();
            }
        });

        return redirect()->route('admin.teacher.permission', ['class_id' => $classId, 'section_id' => $sectionId])
            ->with('message', 'Teacher permissions updated successfully.');
    }

    private function whereHasPermission(Builder $query, int $schoolId, int $classId, int $sectionId, ?string $column = null): Builder
    {
        return $query->whereHas('teacherPermissions', function ($query) use ($schoolId, $classId, $sectionId, $column): void {
            $query->where('school_id', $schoolId)->where('class_id', $classId)->where('section_id', $sectionId);

            if ($column !== null) {
                $query->where($column, true);
            }
        });
    }
}
