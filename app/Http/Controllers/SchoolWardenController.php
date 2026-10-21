<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolWardenDocumentRequest;
use App\Http\Requests\StoreSchoolWardenRequest;
use App\Http\Requests\UpdateSchoolWardenPasswordRequest;
use App\Http\Requests\UpdateSchoolWardenRequest;
use App\Http\Requests\UpdateSchoolWardenStatusRequest;
use App\Mail\NewUserEmail;
use App\Models\School;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SchoolWardenController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAnyWarden', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        if (!in_array($status, ['active', 'disabled', 'unassigned'], true)) {
            $status = '';
        }

        $baseQuery = User::query()
            ->where('role_id', 10)
            ->where('school_id', auth()->user()->school_id);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->first();
        $summary->unassigned = (clone $baseQuery)->whereDoesntHave('hostels')->count();

        $wardens = $baseQuery
            ->with(['hostels' => fn ($query) => $query->where('school_id', auth()->user()->school_id)->orderBy('name')])
            ->withCount(['hostels' => fn ($query) => $query->where('school_id', auth()->user()->school_id)])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('hostels', fn ($query) => $query
                            ->where('school_id', auth()->user()->school_id)
                            ->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status === 'active', fn ($query) => $query->where(fn ($query) => $query
                ->whereNull('account_status')
                ->orWhere('account_status', '!=', 'disable')))
            ->when($status === 'disabled', fn ($query) => $query->where('account_status', 'disable'))
            ->when($status === 'unassigned', fn ($query) => $query->whereDoesntHave('hostels'))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter(['search' => $search, 'status' => $status]))
            ->through(fn (User $warden): User => $this->normalizeWarden($warden));

        return view('admin.warden.warden_list', [
            'wardens' => $wardens,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'canManageWardens' => Gate::allows('createWarden', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('createWarden', User::class);

        return view('admin.warden.add_warden');
    }

    public function store(StoreSchoolWardenRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);

        $warden = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 10,
            'school_id' => auth()->user()->school_id,
            'user_information' => $this->profileInformation($validated, $photo),
            'status' => 1,
            'account_status' => 'enable',
        ]);

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($warden->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.warden')->with('message', 'Warden created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The warden was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $warden): View
    {
        Gate::authorize('updateWarden', $warden);

        return view('admin.warden.edit_warden', [
            'user' => $warden,
            'profile' => $this->decodeProfile($warden),
        ]);
    }

    public function update(UpdateSchoolWardenRequest $request, User $warden): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($warden);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        $warden->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_information' => $this->profileInformation($validated, $photo),
        ]);

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.warden')->with('message', 'Warden updated successfully.');
    }

    public function destroy(User $warden): RedirectResponse
    {
        Gate::authorize('deleteWarden', $warden);

        if ($warden->hostels()->exists() || $this->hasFinancialOrMessageHistory($warden)) {
            return redirect()->route('admin.warden')->with(
                'error',
                'This warden has assigned hostels or linked history and cannot be deleted. Reassign hostels or disable the account instead.',
            );
        }

        $photo = (string) ($this->decodeProfile($warden)['photo'] ?? '');
        $documents = $this->decodeDocuments($warden);
        $warden->delete();
        $this->deletePhoto($photo);

        foreach ($documents as $fileName) {
            $this->deleteDocumentFile($warden, $fileName);
        }

        return redirect()->route('admin.warden')->with('message', 'Warden deleted successfully.');
    }

    public function updateStatus(UpdateSchoolWardenStatusRequest $request, User $warden): RedirectResponse
    {
        $warden->update(['account_status' => $request->validated('status')]);

        $response = redirect()->route('admin.warden')->with('message', 'Warden status updated successfully.');

        if ($request->validated('status') === 'disable' && $warden->hostels()->exists()) {
            $response->with('warning', 'This disabled warden still has assigned hostels that require reassignment.');
        }

        return $response;
    }

    public function profile(User $warden): View
    {
        Gate::authorize('viewWarden', $warden);
        $warden->load(['hostels' => fn ($query) => $query->where('school_id', auth()->user()->school_id)->orderBy('name')]);

        return view('admin.warden.warden_profile', [
            'warden' => $this->normalizeWarden($warden),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
            'canManageWarden' => Gate::allows('updateWardenPassword', $warden),
        ]);
    }

    public function updatePassword(UpdateSchoolWardenPasswordRequest $request, User $warden): RedirectResponse
    {
        $warden->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.warden')->with('message', 'Warden password updated successfully.');
    }

    public function documents(User $warden): View
    {
        Gate::authorize('manageWardenDocuments', $warden);

        return view('admin.warden.documents', [
            'warden' => $warden,
            'documents' => $this->decodeDocuments($warden),
        ]);
    }

    public function storeDocument(StoreSchoolWardenDocumentRequest $request, User $warden): RedirectResponse
    {
        $validated = $request->validated();
        $documents = $this->decodeDocuments($warden);
        $key = Str::slug($validated['file_name']);
        $key = $key !== '' ? $key : 'document';
        $key .= '-'.Str::lower(Str::random(6));
        $file = $validated['file'];
        $fileName = Str::uuid().'.'.$file->extension();
        $file->move(public_path('assets/uploads/user-docs/'.$warden->id), $fileName);
        $documents[$key] = $fileName;
        $warden->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);

        return redirect()->route('admin.warden.documents', $warden)->with('message', 'Document uploaded successfully.');
    }

    public function destroyDocument(User $warden, string $document): RedirectResponse
    {
        Gate::authorize('manageWardenDocuments', $warden);
        $documents = $this->decodeDocuments($warden);

        if (!array_key_exists($document, $documents)) {
            abort(404);
        }

        $fileName = $documents[$document];
        unset($documents[$document]);
        $warden->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);
        $this->deleteDocumentFile($warden, $fileName);

        return redirect()->route('admin.warden.documents', $warden)->with('message', 'Document removed successfully.');
    }

    private function normalizeWarden(User $warden): User
    {
        $profile = $this->decodeProfile($warden);
        $photo = basename((string) ($profile['photo'] ?? ''));
        $warden->setAttribute('profile_information', $profile);
        $warden->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));

        return $warden;
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
    private function decodeProfile(User $warden): array
    {
        $profile = json_decode($warden->user_information ?? '{}', true);

        return is_array($profile) ? $profile : [];
    }

    /** @return array<string, string> */
    private function decodeDocuments(User $warden): array
    {
        $documents = json_decode($warden->documents ?? '{}', true);

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

    private function hasFinancialOrMessageHistory(User $warden): bool
    {
        return DB::table('payments')->where('user_id', $warden->id)->exists()
            || DB::table('chats')->where('sender_id', $warden->id)->orWhere('reciver_id', $warden->id)->exists()
            || DB::table('message_thrades')->where('sender_id', $warden->id)->orWhere('reciver_id', $warden->id)->exists();
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

    private function deleteDocumentFile(User $warden, string $fileName): void
    {
        $safeName = basename($fileName);

        if ($safeName === '' || $safeName !== $fileName) {
            return;
        }

        $path = public_path('assets/uploads/user-docs/'.$warden->id.'/'.$safeName);

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
