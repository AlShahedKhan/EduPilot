<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolClassRoomRequest;
use App\Http\Requests\UpdateSchoolClassRoomRequest;
use App\Models\ClassRoom;
use App\Models\Routine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class SchoolClassRoomController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ClassRoom::class);

        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $usage = $request->string('usage')->toString();

        if (!in_array($usage, ['in_use', 'available'], true)) {
            $usage = '';
        }

        $baseQuery = ClassRoom::query()->where('school_id', $schoolId);
        $summary = (object) [
            'total' => (clone $baseQuery)->count(),
            'in_use' => (clone $baseQuery)->whereHas('routines', fn ($query) => $query->where('school_id', $schoolId))->count(),
            'available' => (clone $baseQuery)->whereDoesntHave('routines', fn ($query) => $query->where('school_id', $schoolId))->count(),
            'routine_slots' => Routine::query()->where('school_id', $schoolId)->count(),
        ];

        $classRooms = $baseQuery
            ->withCount(['routines' => fn ($query) => $query->where('school_id', $schoolId)])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($usage === 'in_use', fn ($query) => $query->whereHas('routines', fn ($query) => $query->where('school_id', $schoolId)))
            ->when($usage === 'available', fn ($query) => $query->whereDoesntHave('routines', fn ($query) => $query->where('school_id', $schoolId)))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter(['search' => $search, 'usage' => $usage]));

        return view('admin.class_room.class_room_list', [
            'classRooms' => $classRooms,
            'search' => $search,
            'usage' => $usage,
            'summary' => $summary,
            'canManageClassRooms' => Gate::allows('create', ClassRoom::class),
        ]);
    }

    public function exportPdf(Request $request): \Illuminate\Http\Response
    {
        Gate::authorize('viewAny', ClassRoom::class);
        $schoolId = (int) auth()->user()->school_id;
        $search = Str::limit($request->string('search')->trim()->toString(), 255, '');
        $usage = in_array($request->input('usage'), ['in_use', 'available'], true) ? $request->input('usage') : '';
        $classRooms = ClassRoom::query()->where('school_id', $schoolId)->withCount(['routines' => fn ($q) => $q->where('school_id', $schoolId)])
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($usage === 'in_use', fn ($q) => $q->whereHas('routines', fn ($q) => $q->where('school_id', $schoolId)))
            ->when($usage === 'available', fn ($q) => $q->whereDoesntHave('routines', fn ($q) => $q->where('school_id', $schoolId)))
            ->orderBy('name')->get();
        $html = view('admin.class_room.class_room_pdf', compact('classRooms', 'search', 'usage'))->render();
        $pdf = Browsershot::html($html)->format('A4')->showBackground()->noSandbox()->pdf();
        return response($pdf, 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="class-rooms.pdf"']);
    }

    public function create(): View
    {
        Gate::authorize('create', ClassRoom::class);

        return view('admin.class_room.add_class_room');
    }

    public function store(StoreSchoolClassRoomRequest $request): RedirectResponse
    {
        ClassRoom::query()->create([
            'name' => $request->validated('name'),
            'school_id' => $request->user()->school_id,
        ]);

        return redirect()->route('admin.class_room_list')->with('message', 'Class room created successfully.');
    }

    public function edit(ClassRoom $classRoom): View
    {
        Gate::authorize('update', $classRoom);

        return view('admin.class_room.edit_class_room', ['classRoom' => $classRoom]);
    }

    public function update(UpdateSchoolClassRoomRequest $request, ClassRoom $classRoom): RedirectResponse
    {
        $classRoom->update(['name' => $request->validated('name')]);

        return redirect()->route('admin.class_room_list')->with('message', 'Class room updated successfully.');
    }

    public function destroy(ClassRoom $classRoom): RedirectResponse
    {
        Gate::authorize('delete', $classRoom);

        if ($classRoom->routines()->exists()) {
            return redirect()->route('admin.class_room_list')->with('error', 'This class room is used by routines and cannot be deleted.');
        }

        $classRoom->delete();

        return redirect()->route('admin.class_room_list')->with('message', 'Class room deleted successfully.');
    }

    public function legacyEndpointGone(): never
    {
        abort(410);
    }
}
