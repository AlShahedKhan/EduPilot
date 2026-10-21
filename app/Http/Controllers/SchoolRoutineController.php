<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRoutineRequest;
use App\Http\Requests\UpdateSchoolRoutineRequest;
use App\Models\Classes;
use App\Models\ClassRoom;
use App\Models\Routine;
use App\Models\Section;
use App\Models\Session;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Spatie\Browsershot\Browsershot;

class SchoolRoutineController extends Controller
{
    private const DAYS = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Routine::class);
        $schoolId = (int) $request->user()->school_id;
        $filters = $this->filters($request);
        $baseQuery = Routine::query()->where('school_id', $schoolId);
        $summary = (object) [
            'total' => (clone $baseQuery)->count(),
            'teachers' => (clone $baseQuery)->distinct()->count('teacher_id'),
            'rooms' => (clone $baseQuery)->whereNotNull('room_id')->distinct()->count('room_id'),
            'days' => (clone $baseQuery)->distinct()->count('day'),
        ];

        $routines = $this->filteredQuery($schoolId, $filters)
            ->orderByRaw("CASE day WHEN 'saturday' THEN 1 WHEN 'sunday' THEN 2 WHEN 'monday' THEN 3 WHEN 'tuesday' THEN 4 WHEN 'wednesday' THEN 5 WHEN 'thursday' THEN 6 WHEN 'friday' THEN 7 ELSE 8 END")
            ->orderBy('starting_hour')
            ->orderBy('starting_minute')
            ->paginate(20)
            ->withQueryString();

        return view('admin.routine.routine', [
            ...$filters,
            'routines' => $routines,
            'summary' => $summary,
            'days' => self::DAYS,
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->where('school_id', $schoolId)->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))->orderBy('name')->get(['id', 'name']),
            'teachers' => User::query()->where('school_id', $schoolId)->where('role_id', 3)->orderBy('name')->get(['id', 'name']),
            'rooms' => ClassRoom::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->latest('id')->get(['id', 'session_title']),
            'canManageRoutines' => Gate::allows('create', Routine::class),
        ]);
    }

    public function store(StoreSchoolRoutineRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->assertNoConflict($data);
        Routine::query()->create([...$data, 'school_id' => (int) $request->user()->school_id]);

        return redirect()->route('admin.routine')->with('message', 'Routine created successfully.');
    }

    public function update(UpdateSchoolRoutineRequest $request, Routine $routine): RedirectResponse
    {
        $data = $request->validated();
        $this->assertNoConflict($data, $routine->id);
        $routine->update($data);

        return redirect()->route('admin.routine')->with('message', 'Routine updated successfully.');
    }

    public function destroy(Routine $routine): RedirectResponse
    {
        Gate::authorize('delete', $routine);
        $routine->delete();

        return back()->with('message', 'Routine deleted successfully.');
    }

    public function exportPdf(Request $request): Response
    {
        Gate::authorize('viewAny', Routine::class);
        $routines = $this->filteredQuery((int) $request->user()->school_id, $this->filters($request))
            ->orderBy('day')->orderBy('starting_hour')->orderBy('starting_minute')->get();
        $html = view('admin.routine.routine_pdf', ['routines' => $routines])->render();
        $pdf = Browsershot::html($html)->format('A4')->landscape()->showBackground()->noSandbox()->pdf();

        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="routines.pdf"']);
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    private function filters(Request $request): array
    {
        $day = $request->string('day')->lower()->toString();

        return [
            'classId' => $request->filled('class_id') ? (int) $request->input('class_id') : null,
            'sectionId' => $request->filled('section_id') ? (int) $request->input('section_id') : null,
            'teacherId' => $request->filled('teacher_id') ? (int) $request->input('teacher_id') : null,
            'roomId' => $request->filled('room_id') ? (int) $request->input('room_id') : null,
            'sessionId' => $request->filled('session_id') ? (int) $request->input('session_id') : null,
            'day' => in_array($day, self::DAYS, true) ? $day : '',
        ];
    }

    private function filteredQuery(int $schoolId, array $filters)
    {
        return Routine::query()->where('school_id', $schoolId)
            ->with(['schoolClass:id,name', 'section:id,name', 'subject:id,name', 'teacher:id,name', 'room:id,name', 'session:id,session_title'])
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))
            ->when($filters['teacherId'], fn ($query, $id) => $query->where('teacher_id', $id))
            ->when($filters['roomId'], fn ($query, $id) => $query->where('room_id', $id))
            ->when($filters['sessionId'], fn ($query, $id) => $query->where('session_id', $id))
            ->when($filters['day'] !== '', fn ($query) => $query->where('day', $filters['day']));
    }

    private function assertNoConflict(array $data, ?int $ignoreId = null): void
    {
        $start = ((int) $data['starting_hour'] * 60) + (int) $data['starting_minute'];
        $end = ((int) $data['ending_hour'] * 60) + (int) $data['ending_minute'];
        abort_if($end <= $start, 422, 'Ending time must be later than starting time.');

        $conflict = Routine::query()
            ->where('school_id', auth()->user()->school_id)
            ->where('session_id', $data['session_id'])
            ->where('day', $data['day'])
            ->when($ignoreId, fn ($query, $id) => $query->where('id', '!=', $id))
            ->where(function ($query) use ($data): void {
                $query->where('section_id', $data['section_id'])
                    ->orWhere('teacher_id', $data['teacher_id'])
                    ->when($data['room_id'] ?? null, fn ($query, $roomId) => $query->orWhere('room_id', $roomId));
            })
            ->get(['starting_hour', 'starting_minute', 'ending_hour', 'ending_minute'])
            ->contains(fn (Routine $routine): bool => (((int) $routine->starting_hour * 60) + (int) $routine->starting_minute) < $end
                && (((int) $routine->ending_hour * 60) + (int) $routine->ending_minute) > $start);

        abort_if($conflict, 422, 'The routine overlaps an existing section, teacher, or room slot.');
    }
}
