<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolAdminRequest;
use App\Http\Requests\UpdateSchoolAdminPasswordRequest;
use App\Http\Requests\UpdateSchoolAdminPermissionsRequest;
use App\Http\Requests\UpdateSchoolAdminRequest;
use App\Http\Requests\UpdateSchoolAdminStatusRequest;
use App\Mail\NewUserEmail;
use App\Models\School;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        if (!in_array($status, ['active', 'disabled', 'primary'], true)) {
            $status = '';
        }

        $baseQuery = User::query()
            ->where('role_id', 2)
            ->where('school_id', auth()->user()->school_id);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->selectRaw('SUM(CASE WHEN school_role = 1 THEN 1 ELSE 0 END) as primary_admins')
            ->first();

        $admins = $baseQuery
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where(fn ($query) => $query
                ->whereNull('account_status')
                ->orWhere('account_status', '!=', 'disable')))
            ->when($status === 'disabled', fn ($query) => $query->where('account_status', 'disable'))
            ->when($status === 'primary', fn ($query) => $query->where('school_role', 1))
            ->orderByDesc('school_role')
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query())
            ->through(function (User $admin): User {
                $information = json_decode($admin->user_information ?? '{}', true);
                $information = is_array($information) ? $information : [];
                $photo = basename((string) ($information['photo'] ?? ''));

                $admin->setAttribute('profile_information', $information);
                $admin->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));

                return $admin;
            });

        return view('admin.admin.admin_list', [
            'admins' => $admins,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'canManageAdmins' => Gate::allows('create', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('admin.admin.add_admin');
    }

    public function store(StoreSchoolAdminRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);

        $admin = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 2,
            'school_id' => auth()->user()->school_id,
            'user_information' => $this->profileInformation($validated, $photo),
            'status' => 1,
            'school_role' => 0,
            'account_status' => 'enable',
        ]);

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($admin->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.admin')
            ->with('message', 'Admin created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The admin was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $admin): View
    {
        Gate::authorize('update', $admin);

        return view('admin.admin.edit_admin', [
            'user' => $admin,
            'profile' => $this->decodeProfile($admin),
        ]);
    }

    public function update(UpdateSchoolAdminRequest $request, User $admin): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($admin);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_information' => $this->profileInformation($validated, $photo),
        ]);

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.admin')->with('message', 'Admin updated successfully.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        Gate::authorize('delete', $admin);

        $photo = (string) ($this->decodeProfile($admin)['photo'] ?? '');
        $admin->delete();
        $this->deletePhoto($photo);

        return redirect()->route('admin.admin')->with('message', 'Admin deleted successfully.');
    }

    public function updateStatus(UpdateSchoolAdminStatusRequest $request, User $admin): RedirectResponse
    {
        $admin->update(['account_status' => $request->validated('status')]);

        return redirect()->route('admin.admin')->with('message', 'Admin status updated successfully.');
    }

    public function profile(User $admin): View
    {
        Gate::authorize('view', $admin);

        return view('admin.admin.admin_profile', [
            'user_details' => (new CommonController())->getAdminDetails($admin->id),
            'admin' => $admin,
            'canManageAdmin' => Gate::allows('updatePassword', $admin),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
        ]);
    }

    public function updatePassword(UpdateSchoolAdminPasswordRequest $request, User $admin): RedirectResponse
    {
        $admin->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.admin')->with('message', 'Admin password updated successfully.');
    }

    public function permissions(User $admin): View
    {
        Gate::authorize('managePermissions', $admin);

        return view('admin.admin.menu_permission', [
            'user' => $admin,
            'permissionOptions' => config('school_admin_permissions', []),
        ]);
    }

    public function updatePermissions(UpdateSchoolAdminPermissionsRequest $request, User $admin): RedirectResponse
    {
        $admin->update([
            'menu_permission' => json_encode($request->validated('permissions', [])),
        ]);

        return redirect()->route('admin.admin')->with('message', 'Admin permissions updated successfully.');
    }

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
    private function decodeProfile(User $admin): array
    {
        $profile = json_decode($admin->user_information ?? '{}', true);

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

    private function mailIsConfigured(): bool
    {
        return filled(get_settings('smtp_user'))
            && filled(get_settings('smtp_pass'))
            && filled(get_settings('smtp_host'))
            && filled(get_settings('smtp_port'));
    }
}
