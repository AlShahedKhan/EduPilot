<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolParentDocumentRequest;
use App\Http\Requests\StoreSchoolParentRequest;
use App\Http\Requests\UpdateSchoolParentPasswordRequest;
use App\Http\Requests\UpdateSchoolParentRequest;
use App\Http\Requests\UpdateSchoolParentStatusRequest;
use App\Mail\NewUserEmail;
use App\Models\Classes;
use App\Models\School;
use App\Models\Section;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolParentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAnyParent', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        if (!in_array($status, ['active', 'disabled', 'without_children'], true)) {
            $status = '';
        }

        $baseQuery = User::query()
            ->where('role_id', 6)
            ->where('school_id', auth()->user()->school_id);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->first();
        $summary->without_children = (clone $baseQuery)->whereDoesntHave('children')->count();

        $parents = $baseQuery
            ->with(['children' => fn ($query) => $query
                ->where('role_id', 7)
                ->where('school_id', auth()->user()->school_id)
                ->orderBy('name'), 'children.enrollment.class', 'children.enrollment.section'])
            ->withCount(['children' => fn ($query) => $query
                ->where('role_id', 7)
                ->where('school_id', auth()->user()->school_id)])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('children', fn ($query) => $query
                            ->where('role_id', 7)
                            ->where('school_id', auth()->user()->school_id)
                            ->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status === 'active', fn ($query) => $query->where(fn ($query) => $query
                ->whereNull('account_status')
                ->orWhere('account_status', '!=', 'disable')))
            ->when($status === 'disabled', fn ($query) => $query->where('account_status', 'disable'))
            ->when($status === 'without_children', fn ($query) => $query->whereDoesntHave('children'))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter(['search' => $search, 'status' => $status]))
            ->through(fn (User $parent): User => $this->normalizeParent($parent));

        return view('admin.parent.parent_list', [
            'parents' => $parents,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'canManageParents' => Gate::allows('createParent', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('createParent', User::class);

        return view('admin.parent.add_parent', [
            'classes' => $this->schoolClasses(),
            'selectedChildren' => $this->selectedChildren(session()->getOldInput('child_ids', [])),
        ]);
    }

    public function store(StoreSchoolParentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);
        $parent = DB::transaction(function () use ($validated, $photo): User {
            $children = $this->lockAssignableChildren($validated['child_ids'] ?? []);
            $parent = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => 6,
                'school_id' => auth()->user()->school_id,
                'user_information' => $this->profileInformation($validated, $photo),
                'status' => 1,
                'account_status' => 'enable',
            ]);

            if ($children->isNotEmpty()) {
                User::query()->whereKey($children->modelKeys())->update(['parent_id' => $parent->id]);
            }

            return $parent;
        });

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($parent->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.parent')->with('message', 'Parent created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The parent was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $parent): View
    {
        Gate::authorize('updateParent', $parent);

        $children = $parent->children()->where('role_id', 7)->where('school_id', auth()->user()->school_id)->orderBy('name')->get();

        return view('admin.parent.edit_parent', [
            'user' => $parent,
            'profile' => $this->decodeProfile($parent),
            'classes' => $this->schoolClasses(),
            'selectedChildren' => $this->selectedChildren(session()->getOldInput('child_ids', $children->modelKeys())),
        ]);
    }

    public function update(UpdateSchoolParentRequest $request, User $parent): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($parent);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        DB::transaction(function () use ($parent, $validated, $photo): void {
            $children = $this->lockAssignableChildren($validated['child_ids'] ?? [], $parent);
            $parent->children()->where('school_id', auth()->user()->school_id)->lockForUpdate()->get();
            $parent->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'user_information' => $this->profileInformation($validated, $photo),
            ]);
            $parent->children()->where('school_id', auth()->user()->school_id)->whereNotIn('id', $children->modelKeys())->update(['parent_id' => null]);

            if ($children->isNotEmpty()) {
                User::query()->whereKey($children->modelKeys())->update(['parent_id' => $parent->id]);
            }
        });

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.parent')->with('message', 'Parent updated successfully.');
    }

    public function destroy(User $parent): RedirectResponse
    {
        Gate::authorize('deleteParent', $parent);

        if ($parent->children()->exists() || $this->hasFinancialOrMessageHistory($parent)) {
            return redirect()->route('admin.parent')->with(
                'error',
                'This parent has linked children, financial, feedback, or message history and cannot be deleted. Disable the account instead.',
            );
        }

        $photo = (string) ($this->decodeProfile($parent)['photo'] ?? '');
        $documents = $this->decodeDocuments($parent);
        $parent->delete();
        $this->deletePhoto($photo);

        foreach ($documents as $fileName) {
            $this->deleteDocumentFile($parent, $fileName);
        }

        return redirect()->route('admin.parent')->with('message', 'Parent deleted successfully.');
    }

    public function updateStatus(UpdateSchoolParentStatusRequest $request, User $parent): RedirectResponse
    {
        $parent->update(['account_status' => $request->validated('status')]);

        return redirect()->route('admin.parent')->with('message', 'Parent status updated successfully.');
    }

    public function profile(User $parent): View
    {
        Gate::authorize('viewParent', $parent);

        $parent->load(['children' => fn ($query) => $query->where('role_id', 7)->orderBy('name'), 'children.enrollment.class', 'children.enrollment.section']);

        return view('admin.parent.parent_profile', [
            'parent' => $this->normalizeParent($parent),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
            'canManageParent' => Gate::allows('updateParentPassword', $parent),
        ]);
    }

    public function updatePassword(UpdateSchoolParentPasswordRequest $request, User $parent): RedirectResponse
    {
        $parent->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.parent')->with('message', 'Parent password updated successfully.');
    }

    public function documents(User $parent): View
    {
        Gate::authorize('manageParentDocuments', $parent);

        return view('admin.parent.documents', [
            'parent' => $parent,
            'documents' => $this->decodeDocuments($parent),
        ]);
    }

    public function storeDocument(StoreSchoolParentDocumentRequest $request, User $parent): RedirectResponse
    {
        $validated = $request->validated();
        $documents = $this->decodeDocuments($parent);
        $key = Str::slug($validated['file_name']);
        $key = $key !== '' ? $key : 'document';
        $key .= '-'.Str::lower(Str::random(6));
        $file = $validated['file'];
        $fileName = Str::uuid().'.'.$file->extension();
        $file->move(public_path('assets/uploads/user-docs/'.$parent->id), $fileName);
        $documents[$key] = $fileName;
        $parent->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);

        return redirect()->route('admin.parent.documents', $parent)->with('message', 'Document uploaded successfully.');
    }

    public function destroyDocument(User $parent, string $document): RedirectResponse
    {
        Gate::authorize('manageParentDocuments', $parent);
        $documents = $this->decodeDocuments($parent);

        if (!array_key_exists($document, $documents)) {
            abort(404);
        }

        $fileName = $documents[$document];
        unset($documents[$document]);
        $parent->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);
        $this->deleteDocumentFile($parent, $fileName);

        return redirect()->route('admin.parent.documents', $parent)->with('message', 'Document removed successfully.');
    }

    public function sections(Classes $class): JsonResponse
    {
        Gate::authorize('createParent', User::class);
        $this->ensureClassBelongsToSchool($class);

        return response()->json($class->sections()->where('school_id', auth()->user()->school_id)->orderBy('name')->get(['id', 'name']));
    }

    public function students(Request $request, Classes $class, Section $section): JsonResponse
    {
        Gate::authorize('createParent', User::class);
        $this->ensureClassBelongsToSchool($class);

        if ((int) $section->class_id !== (int) $class->id || (int) $section->school_id !== (int) auth()->user()->school_id) {
            abort(404);
        }

        $parentId = $request->integer('parent_id');
        $parent = $parentId > 0 ? User::query()->find($parentId) : null;

        if ($parent !== null) {
            Gate::authorize('updateParent', $parent);
        }

        $students = User::query()
            ->where('users.role_id', 7)
            ->where('users.school_id', auth()->user()->school_id)
            ->where(function ($query) use ($parent): void {
                $query->whereNull('users.parent_id');

                if ($parent !== null) {
                    $query->orWhere('users.parent_id', $parent->id);
                }
            })
            ->whereHas('enrollment', fn ($query) => $query
                ->where('school_id', auth()->user()->school_id)
                ->where('class_id', $class->id)
                ->where('section_id', $section->id))
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'users.email']);

        return response()->json($students);
    }

    /** @return Collection<int, Classes> */
    private function schoolClasses(): Collection
    {
        return Classes::query()
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get(['id', 'name', 'school_id']);
    }

    private function ensureClassBelongsToSchool(Classes $class): void
    {
        if ((int) $class->school_id !== (int) auth()->user()->school_id) {
            abort(404);
        }
    }

    /** @param array<int, int|string> $childIds */
    private function selectedChildren(array $childIds): Collection
    {
        return User::query()
            ->whereKey($childIds)
            ->where('role_id', 7)
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * @param array<int, int|string> $childIds
     * @return Collection<int, User>
     */
    private function lockAssignableChildren(array $childIds, ?User $parent = null): Collection
    {
        $ids = collect($childIds)->map(fn ($id): int => (int) $id)->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return new Collection();
        }

        $children = User::query()
            ->whereKey($ids)
            ->where('role_id', 7)
            ->where('school_id', auth()->user()->school_id)
            ->lockForUpdate()
            ->get();

        $hasUnavailableChild = $children->count() !== $ids->count()
            || $children->contains(fn (User $child): bool => $child->parent_id !== null
                && ($parent === null || (int) $child->parent_id !== (int) $parent->id));

        if ($hasUnavailableChild) {
            throw ValidationException::withMessages([
                'child_ids' => 'One or more selected students are unavailable or already assigned to another parent.',
            ]);
        }

        return $children;
    }

    private function normalizeParent(User $parent): User
    {
        $profile = $this->decodeProfile($parent);
        $photo = basename((string) ($profile['photo'] ?? ''));
        $parent->setAttribute('profile_information', $profile);
        $parent->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));

        return $parent;
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
    private function decodeProfile(User $parent): array
    {
        $profile = json_decode($parent->user_information ?? '{}', true);

        return is_array($profile) ? $profile : [];
    }

    /** @return array<string, string> */
    private function decodeDocuments(User $parent): array
    {
        $documents = json_decode($parent->documents ?? '{}', true);

        return is_array($documents) ? array_filter($documents, 'is_string') : [];
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

    private function hasFinancialOrMessageHistory(User $parent): bool
    {
        return DB::table('payments')->where('user_id', $parent->id)->exists()
            || DB::table('student_fee_managers')->where('parent_id', $parent->id)->exists()
            || DB::table('feedback')->where('parent_id', $parent->id)->exists()
            || DB::table('chats')->where('sender_id', $parent->id)->orWhere('reciver_id', $parent->id)->exists()
            || DB::table('message_thrades')->where('sender_id', $parent->id)->orWhere('reciver_id', $parent->id)->exists();
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

    private function deleteDocumentFile(User $parent, string $fileName): void
    {
        $safeName = basename($fileName);

        if ($safeName === '' || $safeName !== $fileName) {
            return;
        }

        $path = public_path('assets/uploads/user-docs/'.$parent->id.'/'.$safeName);

        if (is_file($path)) {
            unlink($path);
        }
    }

    private function mailIsConfigured(): bool
    {
        return filled(get_settings('smtp_user'))
            && filled(get_settings('smtp_pass'))
            && filled(get_settings('smtp_host'))
            && filled(get_settings('smtp_port'));
    }
}
