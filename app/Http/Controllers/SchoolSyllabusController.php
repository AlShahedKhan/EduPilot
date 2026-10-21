<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolSyllabusRequest;
use App\Http\Requests\UpdateSchoolSyllabusRequest;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Syllabus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class SchoolSyllabusController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Syllabus::class);

        $schoolId = (int) $request->user()->school_id;
        $filters = $this->filters($request);
        $baseQuery = Syllabus::query()->where('school_id', $schoolId);
        $summary = (object) [
            'total' => (clone $baseQuery)->count(),
            'classes_covered' => (clone $baseQuery)->distinct()->count('class_id'),
            'subjects_covered' => (clone $baseQuery)->distinct()->count('subject_id'),
            'recent' => (clone $baseQuery)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $syllabuses = $this->filteredQuery($schoolId, $filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.syllabus.syllabus', [
            ...$this->filterOptions($schoolId, $filters),
            ...$filters,
            'syllabuses' => $syllabuses,
            'summary' => $summary,
            'canManageSyllabuses' => Gate::allows('create', Syllabus::class),
        ]);
    }

    public function store(StoreSchoolSyllabusRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('syllabus_file');
        $file = $request->file('syllabus_file');
        $filename = Str::uuid().'.'.$file->extension();
        $file->move(public_path('assets/uploads/syllabus'), $filename);

        Syllabus::query()->create([
            ...$data,
            'file' => $filename,
            'school_id' => (int) $request->user()->school_id,
        ]);

        return redirect()->route('admin.syllabus')->with('message', 'Syllabus uploaded successfully.');
    }

    public function update(UpdateSchoolSyllabusRequest $request, Syllabus $syllabus): RedirectResponse
    {
        $data = $request->safe()->except('syllabus_file');
        $oldFile = null;

        if ($request->hasFile('syllabus_file')) {
            $oldFile = public_path('assets/uploads/syllabus/'.$syllabus->file);
            $file = $request->file('syllabus_file');
            $filename = Str::uuid().'.'.$file->extension();
            $file->move(public_path('assets/uploads/syllabus'), $filename);
            $data['file'] = $filename;
        }

        $syllabus->update($data);

        if ($oldFile !== null && is_file($oldFile)) {
            unlink($oldFile);
        }

        return redirect()->route('admin.syllabus')->with('message', 'Syllabus updated successfully.');
    }

    public function destroy(Syllabus $syllabus): RedirectResponse
    {
        Gate::authorize('delete', $syllabus);
        $file = public_path('assets/uploads/syllabus/'.$syllabus->file);
        $syllabus->delete();

        if (is_file($file)) {
            unlink($file);
        }

        return back()->with('message', 'Syllabus deleted successfully.');
    }

    public function exportPdf(Request $request): Response
    {
        Gate::authorize('viewAny', Syllabus::class);
        $schoolId = (int) $request->user()->school_id;
        $syllabuses = $this->filteredQuery($schoolId, $this->filters($request))->latest()->get();
        $html = view('admin.syllabus.syllabus_pdf', ['syllabuses' => $syllabuses])->render();
        $pdf = Browsershot::html($html)->format('A4')->showBackground()->noSandbox()->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="syllabuses.pdf"',
        ]);
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    /** @return array{search: string, classId: ?int, sectionId: ?int, subjectId: ?int, sessionId: ?int} */
    private function filters(Request $request): array
    {
        return [
            'search' => Str::limit($request->string('search')->trim()->toString(), 255, ''),
            'classId' => $request->filled('class_id') ? (int) $request->input('class_id') : null,
            'sectionId' => $request->filled('section_id') ? (int) $request->input('section_id') : null,
            'subjectId' => $request->filled('subject_id') ? (int) $request->input('subject_id') : null,
            'sessionId' => $request->filled('session_id') ? (int) $request->input('session_id') : null,
        ];
    }

    /** @param array{search: string, classId: ?int, sectionId: ?int, subjectId: ?int, sessionId: ?int} $filters */
    private function filteredQuery(int $schoolId, array $filters)
    {
        return Syllabus::query()
            ->where('school_id', $schoolId)
            ->with(['schoolClass:id,name', 'section:id,name', 'subject:id,name', 'session:id,session_title'])
            ->when($filters['search'] !== '', fn ($query) => $query->where('title', 'like', "%{$filters['search']}%"))
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['subjectId'], fn ($query, $id) => $query->where('subject_id', $id))
            ->when($filters['sessionId'], fn ($query, $id) => $query->where('session_id', $id));
    }

    /** @param array{search: string, classId: ?int, sectionId: ?int, subjectId: ?int, sessionId: ?int} $filters */
    private function filterOptions(int $schoolId, array $filters): array
    {
        return [
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->where('school_id', $schoolId)->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::query()->where('school_id', $schoolId)->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->latest('id')->get(['id', 'session_title']),
        ];
    }
}
