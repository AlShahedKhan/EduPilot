<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGradebookResultRequest;
use App\Models\Classes;
use App\Models\Enrollment;
use App\Models\ExamCategory;
use App\Models\Gradebook;
use App\Models\School;
use App\Models\Section;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class SchoolGradebookController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Gradebook::class);
        $schoolId = (int) $request->user()->school_id;
        $filters = $this->filters($request, $schoolId);
        $subjects = Subject::query()->where('school_id', $schoolId)
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))
            ->when($filters['sessionId'], fn ($query, $id) => $query->where('session_id', $id))
            ->orderBy('name')->get(['id', 'name']);

        $gradebooksQuery = Gradebook::query()->where('school_id', $schoolId)
            ->where('session_id', $filters['sessionId'])
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['examCategoryId'], fn ($query, $id) => $query->where('exam_category_id', $id))
            ->with(['student:id,name,email', 'schoolClass:id,name', 'section:id,name', 'examCategory:id,name'])
            ->when($filters['search'] !== '', fn ($query) => $query->whereHas('student', fn ($query) => $query->where('name', 'like', "%{$filters['search']}%")))
            ->when($filters['status'] === 'recorded', fn ($query) => $query->whereNotNull('marks'))
            ->when($filters['status'] === 'missing', fn ($query) => $query->whereNull('marks'));

        $summaryGradebooksQuery = clone $gradebooksQuery;
        $gradebooks = $gradebooksQuery->latest('id')->paginate(10)->withQueryString();
        $gradebooks->getCollection()->each(function (Gradebook $gradebook) use ($filters): void {
            $marks = $this->marksArray($gradebook->marks);
            $visibleMarks = $filters['subjectId'] ? array_intersect_key($marks, [(string) $filters['subjectId'] => true]) : $marks;
            $gradebook->visible_marks = $visibleMarks;
            $gradebook->total_mark = array_sum(array_map('floatval', $visibleMarks));
            $gradebook->average_mark = $visibleMarks === [] ? null : round($gradebook->total_mark / count($visibleMarks), 1);
        });

        $allGradebooks = $summaryGradebooksQuery->get(['marks']);
        $allMarks = $allGradebooks->flatMap(fn (Gradebook $gradebook) => array_values($this->marksArray($gradebook->marks)))->map(fn ($mark): float => (float) $mark);
        $studentCount = Enrollment::query()->where('school_id', $schoolId)->where('session_id', $filters['sessionId'])
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))->count();
        $summary = (object) [
            'students' => $studentCount,
            'recorded' => $allGradebooks->filter(fn (Gradebook $gradebook) => $this->marksArray($gradebook->marks) !== [])->count(),
            'missing' => max(0, $studentCount - $allGradebooks->count()),
            'average' => $allMarks->isEmpty() ? 0 : round($allMarks->avg(), 1),
        ];

        return view('admin.gradebook.gradebook', [
            ...$filters,
            'gradebooks' => $gradebooks,
            'summary' => $summary,
            'subjects' => $subjects,
            'subjectNames' => $subjects->pluck('name', 'id'),
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->where('school_id', $schoolId)->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))->orderBy('name')->get(['id', 'name']),
            'examCategories' => ExamCategory::query()->where('school_id', $schoolId)->latest('id')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->latest('id')->get(['id', 'session_title']),
            'canManageGradebook' => Gate::allows('create', Gradebook::class),
        ]);
    }

    public function store(StoreGradebookResultRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request): void {
            $scope = [
                'school_id' => (int) $request->user()->school_id,
                'session_id' => $data['session_id'],
                'class_id' => $data['class_id'],
                'section_id' => $data['section_id'],
                'student_id' => $data['student_id'],
                'exam_category_id' => $data['exam_category_id'],
            ];
            $gradebook = Gradebook::query()->where($scope)->lockForUpdate()->first();

            if ($gradebook === null) {
                Gradebook::query()->create([
                    ...$scope,
                    'marks' => json_encode([(string) $data['subject_id'] => $data['mark']]),
                    'comment' => $data['comment'] ?? '',
                    'timestamp' => time(),
                ]);
                return;
            }

            $marks = $this->marksArray($gradebook->marks);
            $marks[(string) $data['subject_id']] = $data['mark'];
            $gradebook->update([
                'marks' => json_encode($marks),
                'comment' => $data['comment'] ?? $gradebook->comment,
                'timestamp' => time(),
            ]);
        }, 3);

        return back()->with('message', 'Result saved successfully.');
    }

    public function exportPdf(Request $request): Response
    {
        Gate::authorize('viewAny', Gradebook::class);
        $view = $this->index($request);
        $html = view('admin.gradebook.gradebook_pdf', $view->getData())->render();
        $pdf = Browsershot::html($html)->format('A4')->landscape()->showBackground()->noSandbox()->pdf();

        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="gradebook.pdf"']);
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    private function filters(Request $request, int $schoolId): array
    {
        $runningSessionId = (int) School::query()->whereKey($schoolId)->value('running_session');
        $status = $request->string('status')->toString();

        return [
            'classId' => $request->filled('class_id') ? (int) $request->input('class_id') : null,
            'sectionId' => $request->filled('section_id') ? (int) $request->input('section_id') : null,
            'examCategoryId' => $request->filled('exam_category_id') ? (int) $request->input('exam_category_id') : null,
            'subjectId' => $request->filled('subject_id') ? (int) $request->input('subject_id') : null,
            'sessionId' => $request->filled('session_id') ? (int) $request->input('session_id') : $runningSessionId,
            'search' => Str::limit($request->string('search')->trim()->toString(), 255, ''),
            'status' => in_array($status, ['recorded', 'missing'], true) ? $status : '',
        ];
    }

    private function marksArray(mixed $marks): array
    {
        if (is_array($marks)) {
            return $marks;
        }

        $decoded = json_decode((string) $marks, true);

        return is_array($decoded) ? $decoded : [];
    }
}
