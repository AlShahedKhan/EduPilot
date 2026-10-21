<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\Classes;
use App\Models\Section;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Browsershot\Browsershot;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Classes::class);

        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $usage = $request->string('usage')->toString();

        if (!in_array($usage, ['with_students', 'empty'], true)) {
            $usage = '';
        }

        $baseQuery = Classes::query()->where('school_id', $schoolId);
        $summary = (object) [
            'total' => (clone $baseQuery)->count(),
            'sections' => Section::query()->where('school_id', $schoolId)->count(),
            'with_students' => (clone $baseQuery)->whereHas('enrollments', fn ($query) => $query->where('school_id', $schoolId))->count(),
            'empty' => (clone $baseQuery)->whereDoesntHave('enrollments', fn ($query) => $query->where('school_id', $schoolId))->count(),
        ];

        $schoolClasses = $baseQuery
            ->with(['sections' => fn ($query) => $query
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->select(['id', 'name', 'class_id', 'school_id'])])
            ->withCount([
                'sections' => fn ($query) => $query->where('school_id', $schoolId),
                'enrollments as students_count' => fn ($query) => $query->where('school_id', $schoolId),
                'subjects' => fn ($query) => $query->where('school_id', $schoolId),
            ])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhereHas('sections', fn ($query) => $query
                    ->where('school_id', $schoolId)
                    ->where('name', 'like', "%{$search}%"))))
            ->when($usage === 'with_students', fn ($query) => $query->whereHas('enrollments', fn ($query) => $query->where('school_id', $schoolId)))
            ->when($usage === 'empty', fn ($query) => $query->whereDoesntHave('enrollments', fn ($query) => $query->where('school_id', $schoolId)))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter(['search' => $search, 'usage' => $usage]));

        return view('admin.class.class_list', [
            'classLists' => $schoolClasses,
            'search' => $search,
            'usage' => $usage,
            'summary' => $summary,
            'canManageClasses' => Gate::allows('create', Classes::class),
        ]);
    }

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        Gate::authorize('viewAny', Classes::class);
        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $usage = in_array($request->input('usage'), ['with_students', 'empty'], true) ? $request->input('usage') : '';
        $classes = Classes::query()->where('school_id', $schoolId)
            ->withCount(['sections' => fn ($q) => $q->where('school_id', $schoolId), 'enrollments as students_count' => fn ($q) => $q->where('school_id', $schoolId), 'subjects' => fn ($q) => $q->where('school_id', $schoolId)])
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhereHas('sections', fn ($q) => $q->where('school_id', $schoolId)->where('name', 'like', "%{$search}%"))))
            ->when($usage === 'with_students', fn ($q) => $q->whereHas('enrollments', fn ($q) => $q->where('school_id', $schoolId)))
            ->when($usage === 'empty', fn ($q) => $q->whereDoesntHave('enrollments', fn ($q) => $q->where('school_id', $schoolId)))
            ->with(['sections' => fn ($q) => $q->where('school_id', $schoolId)->orderBy('name')])->orderBy('name')->get();
        $html = view('admin.class.class_pdf', compact('classes', 'search', 'usage'))->render();
        $pdf = Browsershot::html($html)->format('A4')->showBackground()->noSandbox()->pdf();
        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="classes.pdf"']);
    }

    public function create(): View
    {
        Gate::authorize('create', Classes::class);

        return view('admin.class.add_class');
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $schoolId = (int) $request->user()->school_id;

        DB::transaction(function () use ($validated, $schoolId): void {
            $schoolClass = Classes::query()->create([
                'name' => $validated['name'],
                'school_id' => $schoolId,
            ]);

            $schoolClass->sections()->createMany(collect($validated['sections'])
                ->map(fn (array $section): array => [
                    'name' => $section['name'],
                    'school_id' => $schoolId,
                ])->all());
        }, 3);

        return redirect()->route('admin.class_list')->with('message', 'Class created successfully.');
    }

    public function edit(Classes $class): View
    {
        Gate::authorize('update', $class);
        $class->load(['sections' => fn ($query) => $query
            ->where('school_id', $class->school_id)
            ->orderBy('name')]);

        return view('admin.class.edit_class', ['schoolClass' => $class]);
    }

    public function update(UpdateSchoolClassRequest $request, Classes $class): RedirectResponse
    {
        $validated = $request->validated();
        $schoolId = (int) $request->user()->school_id;

        DB::transaction(function () use ($validated, $class, $schoolId): void {
            $lockedClass = Classes::query()
                ->whereKey($class->id)
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->firstOrFail();
            $existingSections = Section::query()
                ->where('class_id', $lockedClass->id)
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $removedIds = collect($validated['removed_section_ids'] ?? [])->map(fn ($id): int => (int) $id);

            $this->assertSafeSectionRemoval($existingSections->only($removedIds->all()));
            $this->assertUniqueFinalSections($existingSections, $validated['sections'], $removedIds);

            $lockedClass->update(['name' => $validated['name']]);

            $submittedExistingIds = collect($validated['sections'])
                ->pluck('id')
                ->filter()
                ->map(fn ($id): int => (int) $id);

            Section::query()
                ->where('class_id', $lockedClass->id)
                ->where('school_id', $schoolId)
                ->whereIn('id', $submittedExistingIds)
                ->get()
                ->each(fn (Section $section) => $section->update([
                    'name' => 'pending-'.$section->id.'-'.Str::uuid(),
                ]));

            if ($removedIds->isNotEmpty()) {
                Section::query()
                    ->where('class_id', $lockedClass->id)
                    ->where('school_id', $schoolId)
                    ->whereIn('id', $removedIds)
                    ->delete();
            }

            foreach ($validated['sections'] as $sectionData) {
                if (filled($sectionData['id'] ?? null)) {
                    Section::query()
                        ->whereKey((int) $sectionData['id'])
                        ->where('class_id', $lockedClass->id)
                        ->where('school_id', $schoolId)
                        ->update(['name' => $sectionData['name']]);
                    continue;
                }

                $lockedClass->sections()->create([
                    'name' => $sectionData['name'],
                    'school_id' => $schoolId,
                ]);
            }

        }, 3);

        return redirect()->route('admin.class_list')->with('message', 'Class and sections updated successfully.');
    }

    public function destroy(Classes $class): RedirectResponse
    {
        Gate::authorize('delete', $class);

        $error = DB::transaction(function () use ($class): ?string {
            $lockedClass = Classes::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();

            if ($dependency = $this->classDependency($lockedClass)) {
                return "This class has linked {$dependency} and cannot be deleted.";
            }

            if ($lockedClass->sections()->where('school_id', '!=', $lockedClass->school_id)->exists()) {
                return 'This class has sections owned by another school and cannot be deleted.';
            }

            $lockedClass->load(['sections' => fn ($query) => $query->where('school_id', $lockedClass->school_id)]);

            foreach ($lockedClass->sections as $section) {
                if ($dependency = $this->sectionDependency($section)) {
                    return "Section {$section->name} has linked {$dependency}; the class cannot be deleted.";
                }
            }

            $lockedClass->delete();

            return null;
        }, 3);

        if ($error !== null) {
            return redirect()->route('admin.class_list')->with('error', $error);
        }

        return redirect()->route('admin.class_list')->with('message', 'Class deleted successfully.');
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    /**
     * @param  Collection<int, Section>  $sections
     */
    private function assertSafeSectionRemoval(Collection $sections): void
    {
        foreach ($sections as $section) {
            if ($dependency = $this->sectionDependency($section)) {
                throw ValidationException::withMessages([
                    'removed_section_ids' => "Section {$section->name} has linked {$dependency} and cannot be removed.",
                ]);
            }
        }

    }

    /**
     * @param  Collection<int, Section>  $existingSections
     * @param  list<array{id?: int|null, name: string}>  $submittedSections
     * @param  Collection<int, int>  $removedIds
     */
    private function assertUniqueFinalSections(Collection $existingSections, array $submittedSections, Collection $removedIds): void
    {
        $finalNames = $existingSections
            ->reject(fn (Section $section): bool => $removedIds->contains($section->id))
            ->mapWithKeys(fn (Section $section): array => [$section->id => $section->name]);

        foreach ($submittedSections as $index => $sectionData) {
            $key = filled($sectionData['id'] ?? null) ? (int) $sectionData['id'] : 'new-'.$index;
            $finalNames->put($key, $sectionData['name']);
        }

        $normalizedNames = $finalNames->map(fn (string $name): string => Str::lower(Str::squish($name)));

        if ($normalizedNames->isEmpty()) {
            throw ValidationException::withMessages(['sections' => 'Every class must retain at least one section.']);
        }

        if ($normalizedNames->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['sections' => 'Section names must be unique within the class.']);
        }
    }

    private function sectionDependency(Section $section): ?string
    {
        foreach ([
            'enrollments' => 'enrollments',
            'dailyAttendances' => 'attendance records',
            'routines' => 'routines',
            'syllabuses' => 'syllabuses',
            'teacherPermissions' => 'teacher permissions',
            'gradebooks' => 'gradebooks',
            'feedback' => 'feedback',
        ] as $relation => $label) {
            if ($section->{$relation}()->exists()) {
                return $label;
            }
        }

        return null;
    }

    private function classDependency(Classes $schoolClass): ?string
    {
        foreach ([
            'subjects' => 'subjects',
            'exams' => 'exams',
            'enrollments' => 'enrollments',
            'dailyAttendances' => 'attendance records',
            'routines' => 'routines',
            'syllabuses' => 'syllabuses',
            'studentFeeManagers' => 'fee records',
            'bookIssues' => 'book issues',
            'teacherPermissions' => 'teacher permissions',
            'gradebooks' => 'gradebooks',
            'appraisals' => 'appraisals',
            'feedback' => 'feedback',
        ] as $relation => $label) {
            if ($schoolClass->{$relation}()->exists()) {
                return $label;
            }
        }

        return null;
    }
}
