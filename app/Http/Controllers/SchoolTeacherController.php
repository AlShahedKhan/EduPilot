<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolTeacherRequest;
use App\Http\Requests\UpdateSchoolTeacherPasswordRequest;
use App\Http\Requests\UpdateSchoolTeacherRequest;
use App\Http\Requests\UpdateSchoolTeacherStatusRequest;
use App\Mail\NewUserEmail;
use App\Models\Appraisal;
use App\Models\Appraisal_submit;
use App\Models\Department;
use App\Models\Routine;
use App\Models\School;
use App\Models\TeacherPermission;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolTeacherController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAnyTeacher', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();
        $departmentId = $request->integer('department');
        $schoolId = (int) auth()->user()->school_id;

        if (!in_array($status, ['active', 'disabled', 'unassigned'], true)) {
            $status = '';
        }

        $departments = Department::query()
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($departmentId > 0 && !$departments->contains('id', $departmentId)) {
            $departmentId = 0;
        }

        $baseQuery = User::query()
            ->where('role_id', 3)
            ->where('school_id', $schoolId);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->selectRaw('SUM(CASE WHEN department_id IS NULL THEN 1 ELSE 0 END) as unassigned')
            ->first();

        $teachers = $baseQuery
            ->with('department:id,name,school_id')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where(fn ($query) => $query
                ->whereNull('account_status')
                ->orWhere('account_status', '!=', 'disable')))
            ->when($status === 'disabled', fn ($query) => $query->where('account_status', 'disable'))
            ->when($status === 'unassigned', fn ($query) => $query->whereNull('department_id'))
            ->when($departmentId > 0, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter([
                'search' => $search,
                'status' => $status,
                'department' => $departmentId ?: null,
            ]))
            ->through(fn (User $teacher): User => $this->normalizeTeacher($teacher));

        return view('admin.teacher.teacher_list', [
            'teachers' => $teachers,
            'departments' => $departments,
            'search' => $search,
            'status' => $status,
            'departmentId' => $departmentId,
            'summary' => $summary,
            'canManageTeachers' => Gate::allows('createTeacher', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('createTeacher', User::class);

        return view('admin.teacher.add_teacher', [
            'departments' => $this->schoolDepartments(),
        ]);
    }

    public function store(StoreSchoolTeacherRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);

        $teacher = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 3,
            'school_id' => auth()->user()->school_id,
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'user_information' => $this->profileInformation($validated, $photo),
            'status' => 1,
            'account_status' => 'enable',
        ]);

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($teacher->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.teacher')->with('message', 'Teacher created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The teacher was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $teacher): View
    {
        Gate::authorize('updateTeacher', $teacher);

        return view('admin.teacher.edit_teacher', [
            'user' => $teacher,
            'profile' => $this->decodeProfile($teacher),
            'departments' => $this->schoolDepartments(),
        ]);
    }

    public function update(UpdateSchoolTeacherRequest $request, User $teacher): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($teacher);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department_id' => $validated['department_id'],
            'designation' => $validated['designation'],
            'user_information' => $this->profileInformation($validated, $photo),
        ]);

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.teacher')->with('message', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher): RedirectResponse
    {
        Gate::authorize('deleteTeacher', $teacher);

        if ($this->hasAcademicRecords($teacher)) {
            return redirect()->route('admin.teacher')->with(
                'error',
                'This teacher has linked academic records and cannot be deleted. Disable the account instead.',
            );
        }

        $photo = (string) ($this->decodeProfile($teacher)['photo'] ?? '');
        $teacher->delete();
        $this->deletePhoto($photo);

        return redirect()->route('admin.teacher')->with('message', 'Teacher deleted successfully.');
    }

    public function updateStatus(UpdateSchoolTeacherStatusRequest $request, User $teacher): RedirectResponse
    {
        $teacher->update(['account_status' => $request->validated('status')]);

        return redirect()->route('admin.teacher')->with('message', 'Teacher status updated successfully.');
    }

    public function profile(User $teacher): View
    {
        Gate::authorize('viewTeacher', $teacher);
        $teacher->load('department:id,name,school_id');

        return view('admin.teacher.teacher_profile', [
            'teacher' => $this->normalizeTeacher($teacher),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
            'canManageTeacher' => Gate::allows('updateTeacherPassword', $teacher),
        ]);
    }

    public function updatePassword(UpdateSchoolTeacherPasswordRequest $request, User $teacher): RedirectResponse
    {
        $teacher->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.teacher')->with('message', 'Teacher password updated successfully.');
    }

    public function documents(User $teacher): View
    {
        Gate::authorize('manageTeacherDocuments', $teacher);

        return view('admin.teacher.documents', ['user_details' => $teacher]);
    }

    /** @return Collection<int, Department> */
    private function schoolDepartments(): Collection
    {
        return Department::query()
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    private function normalizeTeacher(User $teacher): User
    {
        $profile = $this->decodeProfile($teacher);
        $photo = basename((string) ($profile['photo'] ?? ''));

        $teacher->setAttribute('profile_information', $profile);
        $teacher->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));
        $departmentName = (int) $teacher->department?->school_id === (int) $teacher->school_id
            ? $teacher->department?->name
            : null;

        $teacher->setAttribute('department_name', $departmentName);

        return $teacher;
    }

    /** @param array<string, mixed> $validated */
    private function profileInformation(array $validated, string $photo): string
    {
        return json_encode([
            'gender' => $validated['gender'],
            'blood_group' => $validated['blood_group'] ?? '',
            'birthday' => CarbonImmutable::createFromFormat('m/d/Y', $validated['birthday'])->startOfDay()->timestamp,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'photo' => $photo,
        ], JSON_THROW_ON_ERROR);
    }

    /** @return array<string, mixed> */
    private function decodeProfile(User $teacher): array
    {
        $profile = json_decode($teacher->user_information ?? '{}', true);

        return is_array($profile) ? $profile : [];
    }

    private function resolvePhoto(Request $request, string $fallback = ''): string
    {
        $croppedPhoto = $request->string('cropped_photo')->toString();

        if ($croppedPhoto !== '') {
            $safeName = basename($croppedPhoto);
            $isSafeName = $safeName === $croppedPhoto
                && preg_match('/\A[a-zA-Z0-9_-]+\.(?:jpe?g|png|webp)\z/i', $safeName) === 1;

            if (!$isSafeName || !is_file(public_path('assets/uploads/user-images/'.$safeName))) {
                throw ValidationException::withMessages(['photo' => 'The cropped photo is invalid.']);
            }

            return $safeName;
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName = Str::uuid().'.'.$photo->extension();
            $photo->move(public_path('assets/uploads/user-images'), $fileName);

            return $fileName;
        }

        return basename($fallback);
    }

    private function deletePhoto(string $photo): void
    {
        $safeName = basename($photo);

        if ($safeName === '' || $safeName === 'thumbnail.png' || $safeName !== $photo) {
            return;
        }

        $path = public_path('assets/uploads/user-images/'.$safeName);

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function hasAcademicRecords(User $teacher): bool
    {
        return Routine::query()->where('teacher_id', $teacher->id)->exists()
            || TeacherPermission::query()->where('teacher_id', $teacher->id)->exists()
            || Appraisal::query()->where('teacher_id', $teacher->id)->exists()
            || Appraisal_submit::query()->where('teacher_id', $teacher->id)->exists();
    }

    private function mailIsConfigured(): bool
    {
        return filled(get_settings('smtp_user'))
            && filled(get_settings('smtp_pass'))
            && filled(get_settings('smtp_host'))
            && filled(get_settings('smtp_port'));
    }
}
