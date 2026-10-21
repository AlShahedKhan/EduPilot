<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolSubjectRequest;
use App\Http\Requests\UpdateSchoolSubjectRequest;
use App\Models\Classes;
use App\Models\Gradebook;
use App\Models\School;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Browsershot\Browsershot;

class SchoolSubjectController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Subject::class);

        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $classId = filled($request->input('class_id')) ? (int) $request->input('class_id') : null;
        $sessionId = filled($request->input('session_id')) ? (int) $request->input('session_id') : null;
        $usage = in_array($request->input('usage'), ['scheduled', 'unused'], true) ? $request->input('usage') : '';

        $baseQuery = Subject::query()->where('school_id', $schoolId);
        $summary = (object) [
            'total' => (clone $baseQuery)->count(),
            'classes_covered' => (clone $baseQuery)->distinct('class_id')->count('class_id'),
            'scheduled' => (clone $baseQuery)->whereHas('routines', fn ($query) => $query->where('school_id', $schoolId))->count(),
            'unused' => (clone $baseQuery)->whereDoesntHave('routines', fn ($query) => $query->where('school_id', $schoolId))->count(),
        ];

        $subjects = $baseQuery
            ->with([
                'schoolClass:id,name,school_id',
                'session:id,session_title,school_id',
            ])
            ->withCount([
                'routines as routine_count' => fn ($query) => $query->where('school_id', $schoolId),
                'exams as exam_count' => fn ($query) => $query->where('school_id', $schoolId),
                'syllabuses as syllabus_count' => fn ($query) => $query->where('school_id', $schoolId),
            ])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($classId !== null, fn ($query) => $query->where('class_id', $classId))
            ->when($sessionId !== null, fn ($query) => $query->where('session_id', $sessionId))
            ->when($usage === 'scheduled', fn ($query) => $query->whereHas('routines', fn ($query) => $query->where('school_id', $schoolId)))
            ->when($usage === 'unused', fn ($query) => $query->whereDoesntHave('routines', fn ($query) => $query->where('school_id', $schoolId)))
            ->orderBy('name')
            ->orderByDesc('id')
            ->paginate(10)
            ->appends(array_filter([
                'search' => $search,
                'class_id' => $classId,
                'session_id' => $sessionId,
                'usage' => $usage,
            ], fn ($value): bool => $value !== null && $value !== ''));

        $gradebookSubjectIds = $this->gradebookSubjectIds(
            $schoolId,
            $subjects->getCollection()->pluck('class_id')->unique()->values()->all(),
        );

        $subjects->getCollection()->each(function (Subject $subject) use ($gradebookSubjectIds): void {
            $subject->dependency_label = match (true) {
                (int) $subject->exam_count > 0 => 'exams',
                (int) $subject->routine_count > 0 => 'routines',
                (int) $subject->syllabus_count > 0 => 'syllabuses',
                in_array((int) $subject->id, $gradebookSubjectIds, true) => 'gradebook marks',
                default => null,
            };
        });

        return view('admin.subject.subject_list', [
            'subjects' => $subjects,
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->orderByDesc('id')->get(['id', 'session_title']),
            'search' => $search,
            'classId' => $classId,
            'sessionId' => $sessionId,
            'usage' => $usage,
            'summary' => $summary,
            'canManageSubjects' => Gate::allows('create', Subject::class),
        ]);
    }

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        Gate::authorize('viewAny', Subject::class);
        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $classId = filled($request->input('class_id')) ? (int) $request->input('class_id') : null;
        $sessionId = filled($request->input('session_id')) ? (int) $request->input('session_id') : null;
        $usage = in_array($request->input('usage'), ['scheduled', 'unused'], true) ? $request->input('usage') : '';
        $subjects = Subject::query()->where('school_id', $schoolId)->with(['schoolClass:id,name', 'session:id,session_title'])
            ->withCount(['routines as routine_count' => fn ($q) => $q->where('school_id', $schoolId), 'exams as exam_count' => fn ($q) => $q->where('school_id', $schoolId), 'syllabuses as syllabus_count' => fn ($q) => $q->where('school_id', $schoolId)])
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))->when($classId !== null, fn ($q) => $q->where('class_id', $classId))->when($sessionId !== null, fn ($q) => $q->where('session_id', $sessionId))
            ->when($usage === 'scheduled', fn ($q) => $q->whereHas('routines', fn ($q) => $q->where('school_id', $schoolId)))->when($usage === 'unused', fn ($q) => $q->whereDoesntHave('routines', fn ($q) => $q->where('school_id', $schoolId)))->orderBy('name')->get();
        $html = view('admin.subject.subject_pdf', compact('subjects', 'search', 'classId', 'sessionId', 'usage'))->render();
        $pdf = Browsershot::html($html)->format('A4')->showBackground()->noSandbox()->pdf();
        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="subjects.pdf"']);
    }

    public function create(): View
    {
        Gate::authorize('create', Subject::class);
        $schoolId = (int) auth()->user()->school_id;

        return view('admin.subject.add_subject', [
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->orderByDesc('id')->get(['id', 'session_title']),
            'runningSessionId' => $this->runningSessionId($schoolId),
        ]);
    }

    public function store(StoreSchoolSubjectRequest $request): RedirectResponse
    {
        Subject::query()->create([
            ...$request->validated(),
            'school_id' => (int) auth()->user()->school_id,
        ]);

        return redirect()->route('admin.subject_list')->with('message', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        Gate::authorize('update', $subject);
        $schoolId = (int) auth()->user()->school_id;

        return view('admin.subject.edit_subject', [
            'subject' => $subject,
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->orderByDesc('id')->get(['id', 'session_title']),
        ]);
    }

    public function update(UpdateSchoolSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validated();
        $schoolId = (int) auth()->user()->school_id;

        DB::transaction(function () use ($validated, $subject, $schoolId): void {
            $lockedSubject = Subject::query()
                ->whereKey($subject->id)
                ->where('school_id', $schoolId)
                ->lockForUpdate()
                ->firstOrFail();

            $classOrSessionChanged = (int) $lockedSubject->class_id !== (int) $validated['class_id']
                || (int) $lockedSubject->session_id !== (int) $validated['session_id'];

            if ($classOrSessionChanged && ($dependency = $this->subjectDependency($lockedSubject)) !== null) {
                throw ValidationException::withMessages([
                    'class_id' => "This subject has linked {$dependency} and cannot be moved to another class or session.",
                ]);
            }

            $lockedSubject->update([
                'name' => $validated['name'],
                'class_id' => $validated['class_id'],
                'session_id' => $validated['session_id'],
            ]);
        }, 3);

        return redirect()->route('admin.subject_list')->with('message', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        Gate::authorize('delete', $subject);

        $error = DB::transaction(function () use ($subject): ?string {
            $lockedSubject = Subject::query()->whereKey($subject->id)->lockForUpdate()->firstOrFail();

            if ($dependency = $this->subjectDependency($lockedSubject)) {
                return "This subject has linked {$dependency} and cannot be deleted.";
            }

            $lockedSubject->delete();

            return null;
        }, 3);

        return redirect()->route('admin.subject_list')->with(
            $error === null ? 'message' : 'error',
            $error ?? 'Subject deleted successfully.',
        );
    }

    public function sections(Classes $class): JsonResponse
    {
        Gate::authorize('viewAny', Subject::class);
        abort_unless((int) $class->school_id === (int) auth()->user()->school_id, 404);

        return response()->json($class->sections()
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get(['id', 'name']));
    }

    public function subjects(Request $request, Classes $class): JsonResponse
    {
        Gate::authorize('viewAny', Subject::class);
        $schoolId = (int) auth()->user()->school_id;
        abort_unless((int) $class->school_id === $schoolId, 404);

        $sessionId = filled($request->input('session_id')) ? (int) $request->input('session_id') : null;
        $subjects = Subject::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $class->id)
            ->when($sessionId !== null, fn ($query) => $query->where('session_id', $sessionId))
            ->orderBy('name')
            ->get(['id', 'name', 'session_id']);

        return response()->json($subjects);
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    private function runningSessionId(int $schoolId): int
    {
        return (int) School::query()->whereKey($schoolId)->value('running_session');
    }

    private function subjectDependency(Subject $subject): ?string
    {
        foreach ([
            'exams' => 'exams',
            'routines' => 'routines',
            'syllabuses' => 'syllabuses',
        ] as $relation => $label) {
            if ($subject->{$relation}()->exists()) {
                return $label;
            }
        }

        if ($this->gradebookSubjectIds((int) $subject->school_id, [(int) $subject->class_id], (int) $subject->id) !== []) {
            return 'gradebook marks';
        }

        return null;
    }

    /**
     * @param  list<int>  $classIds
     * @return list<int>
     */
    private function gradebookSubjectIds(int $schoolId, array $classIds, ?int $onlySubjectId = null): array
    {
        if ($classIds === []) {
            return [];
        }

        $subjectIds = [];
        $gradebooks = Gradebook::query()
            ->where('school_id', $schoolId)
            ->whereIn('class_id', $classIds)
            ->whereNotNull('marks')
            ->get(['marks']);

        foreach ($gradebooks as $gradebook) {
            $marks = json_decode((string) $gradebook->marks, true);
            if (! is_array($marks)) {
                continue;
            }

            foreach (array_keys($marks) as $subjectId) {
                if (is_numeric($subjectId) && ($onlySubjectId === null || (int) $subjectId === $onlySubjectId)) {
                    $subjectIds[] = (int) $subjectId;
                }
            }
        }

        return array_values(array_unique($subjectIds));
    }
}
