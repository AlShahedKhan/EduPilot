<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolAttendanceRequest;
use App\Models\Classes;
use App\Models\DailyAttendances;
use App\Models\Enrollment;
use App\Models\School;
use App\Models\Section;
use App\Models\Session;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class SchoolAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', DailyAttendances::class);
        $schoolId = (int) $request->user()->school_id;
        $filters = $this->filters($request, $schoolId);
        $period = CarbonImmutable::create($filters['year'], $filters['month'], 1);
        $firstTimestamp = $period->startOfMonth()->timestamp;
        $lastTimestamp = $period->endOfMonth()->timestamp;

        $enrollmentsQuery = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('session_id', $filters['sessionId'])
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))
            ->with(['student:id,name,email', 'class:id,name', 'section:id,name'])
            ->when($filters['search'] !== '', fn ($query) => $query->whereHas('student', fn ($query) => $query->where('name', 'like', "%{$filters['search']}%")))
            ->when($filters['status'] !== '', function ($query) use ($filters, $firstTimestamp, $lastTimestamp): void {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->whereHas('dailyAttendances', fn ($attendanceQuery) => $attendanceQuery
                    ->where('school_id', $filters['schoolId'])
                    ->where('session_id', $filters['sessionId'])
                    ->whereBetween('timestamp', [$firstTimestamp, $lastTimestamp])
                    ->where('status', $filters['status'] === 'present' ? 1 : 0)));
            });

        $summaryEnrollmentsQuery = clone $enrollmentsQuery;
        $enrollments = $enrollmentsQuery->orderBy('user_id')->paginate(10)->withQueryString();
        $attendanceRows = DailyAttendances::query()
            ->where('school_id', $schoolId)
            ->where('session_id', $filters['sessionId'])
            ->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))
            ->when($filters['sectionId'], fn ($query, $id) => $query->where('section_id', $id))
            ->whereBetween('timestamp', [$firstTimestamp, $lastTimestamp])
            ->get(['student_id', 'status', 'timestamp']);

        $attendanceByStudent = $attendanceRows->groupBy('student_id');
        $enrollments->getCollection()->each(function (Enrollment $enrollment) use ($attendanceByStudent): void {
            $records = $attendanceByStudent->get($enrollment->user_id, collect());
            $enrollment->attendance_by_day = $records->mapWithKeys(fn (DailyAttendances $record): array => [(int) date('j', $record->timestamp) => (bool) $record->status]);
            $enrollment->present_count = $records->where('status', true)->count();
            $enrollment->absent_count = $records->where('status', false)->count();
        });

        $present = $attendanceRows->where('status', true)->count();
        $absent = $attendanceRows->where('status', false)->count();
        $recorded = $present + $absent;
        $summary = (object) [
            'students' => $summaryEnrollmentsQuery->count(),
            'present' => $present,
            'absent' => $absent,
            'rate' => $recorded === 0 ? 0 : round(($present / $recorded) * 100, 1),
        ];

        return view('admin.attendance.daily_attendance', [
            ...$filters,
            'daysInMonth' => $period->daysInMonth,
            'monthLabel' => $period->format('F Y'),
            'enrollments' => $enrollments,
            'summary' => $summary,
            'classes' => Classes::query()->where('school_id', $schoolId)->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->where('school_id', $schoolId)->when($filters['classId'], fn ($query, $id) => $query->where('class_id', $id))->orderBy('name')->get(['id', 'name']),
            'sessions' => Session::query()->where('school_id', $schoolId)->latest('id')->get(['id', 'session_title']),
            'canManageAttendance' => Gate::allows('create', DailyAttendances::class),
        ]);
    }

    public function update(UpdateSchoolAttendanceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $timestamp = CarbonImmutable::parse($data['date'])->startOfDay()->timestamp;
        $rows = collect($data['attendance'])->map(fn ($status, $studentId): array => [
            'school_id' => (int) $request->user()->school_id,
            'session_id' => $data['session_id'],
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'student_id' => (int) $studentId,
            'status' => (int) $status,
            'timestamp' => $timestamp,
        ])->values()->all();

        DB::transaction(fn () => DailyAttendances::query()->upsert(
            $rows,
            ['school_id', 'session_id', 'class_id', 'section_id', 'student_id', 'timestamp'],
            ['status'],
        ));

        return back()->with('message', 'Attendance saved successfully.');
    }

    public function exportPdf(Request $request): Response
    {
        Gate::authorize('viewAny', DailyAttendances::class);
        $view = $this->index($request);
        $html = view('admin.attendance.attendance_pdf', $view->getData())->render();
        $pdf = Browsershot::html($html)->format('A4')->landscape()->showBackground()->noSandbox()->pdf();

        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="attendance.pdf"']);
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }

    private function filters(Request $request, int $schoolId): array
    {
        $runningSessionId = (int) School::query()->whereKey($schoolId)->value('running_session');
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $status = $request->string('status')->toString();

        return [
            'schoolId' => $schoolId,
            'month' => max(1, min(12, $month)),
            'year' => max(2015, min(now()->year, $year)),
            'classId' => $request->filled('class_id') ? (int) $request->input('class_id') : null,
            'sectionId' => $request->filled('section_id') ? (int) $request->input('section_id') : null,
            'sessionId' => $request->filled('session_id') ? (int) $request->input('session_id') : $runningSessionId,
            'search' => Str::limit($request->string('search')->trim()->toString(), 255, ''),
            'status' => in_array($status, ['present', 'absent'], true) ? $status : '',
        ];
    }
}
