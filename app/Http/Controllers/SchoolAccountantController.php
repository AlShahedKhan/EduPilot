<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolAccountantDocumentRequest;
use App\Http\Requests\StoreSchoolAccountantRequest;
use App\Http\Requests\UpdateSchoolAccountantPasswordRequest;
use App\Http\Requests\UpdateSchoolAccountantRequest;
use App\Http\Requests\UpdateSchoolAccountantStatusRequest;
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

class SchoolAccountantController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAnyAccountant', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        if (!in_array($status, ['active', 'disabled', 'recent'], true)) {
            $status = '';
        }

        $baseQuery = User::query()
            ->where('role_id', 4)
            ->where('school_id', auth()->user()->school_id);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [now()->subDays(30)->startOfDay()])
            ->first();

        $accountants = $baseQuery
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
            ->when($status === 'recent', fn ($query) => $query->where('created_at', '>=', now()->subDays(30)->startOfDay()))
            ->orderBy('name')
            ->paginate(10)
            ->appends(array_filter(['search' => $search, 'status' => $status]))
            ->through(fn (User $accountant): User => $this->normalizeAccountant($accountant));

        return view('admin.accountant.accountant_list', [
            'accountants' => $accountants,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'canManageAccountants' => Gate::allows('createAccountant', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('createAccountant', User::class);

        return view('admin.accountant.add_accountant');
    }

    public function store(StoreSchoolAccountantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);

        $accountant = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 4,
            'school_id' => auth()->user()->school_id,
            'user_information' => $this->profileInformation($validated, $photo),
            'status' => 1,
            'account_status' => 'enable',
        ]);

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($accountant->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.accountant')->with('message', 'Accountant created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The accountant was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $accountant): View
    {
        Gate::authorize('updateAccountant', $accountant);

        return view('admin.accountant.edit_accountant', [
            'user' => $accountant,
            'profile' => $this->decodeProfile($accountant),
        ]);
    }

    public function update(UpdateSchoolAccountantRequest $request, User $accountant): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($accountant);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        $accountant->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_information' => $this->profileInformation($validated, $photo),
        ]);

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.accountant')->with('message', 'Accountant updated successfully.');
    }

    public function destroy(User $accountant): RedirectResponse
    {
        Gate::authorize('deleteAccountant', $accountant);

        if ($this->hasFinancialOrMessageHistory($accountant)) {
            return redirect()->route('admin.accountant')->with(
                'error',
                'This accountant has linked financial or message history and cannot be deleted. Disable the account instead.',
            );
        }

        $photo = (string) ($this->decodeProfile($accountant)['photo'] ?? '');
        $documents = $this->decodeDocuments($accountant);
        $accountant->delete();
        $this->deletePhoto($photo);

        foreach ($documents as $fileName) {
            $this->deleteDocumentFile($accountant, $fileName);
        }

        return redirect()->route('admin.accountant')->with('message', 'Accountant deleted successfully.');
    }

    public function updateStatus(UpdateSchoolAccountantStatusRequest $request, User $accountant): RedirectResponse
    {
        $accountant->update(['account_status' => $request->validated('status')]);

        return redirect()->route('admin.accountant')->with('message', 'Accountant status updated successfully.');
    }

    public function profile(User $accountant): View
    {
        Gate::authorize('viewAccountant', $accountant);

        return view('admin.accountant.accountant_profile', [
            'accountant' => $this->normalizeAccountant($accountant),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
            'canManageAccountant' => Gate::allows('updateAccountantPassword', $accountant),
        ]);
    }

    public function updatePassword(UpdateSchoolAccountantPasswordRequest $request, User $accountant): RedirectResponse
    {
        $accountant->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.accountant')->with('message', 'Accountant password updated successfully.');
    }

    public function documents(User $accountant): View
    {
        Gate::authorize('manageAccountantDocuments', $accountant);

        return view('admin.accountant.documents', [
            'accountant' => $accountant,
            'documents' => $this->decodeDocuments($accountant),
        ]);
    }

    public function storeDocument(StoreSchoolAccountantDocumentRequest $request, User $accountant): RedirectResponse
    {
        $validated = $request->validated();
        $documents = $this->decodeDocuments($accountant);
        $key = Str::slug($validated['file_name']);
        $key = $key !== '' ? $key : 'document';
        $key .= '-'.Str::lower(Str::random(6));
        $file = $validated['file'];
        $fileName = Str::uuid().'.'.$file->extension();
        $file->move(public_path('assets/uploads/user-docs/'.$accountant->id), $fileName);
        $documents[$key] = $fileName;
        $accountant->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);

        return redirect()->route('admin.accountant.documents', $accountant)->with('message', 'Document uploaded successfully.');
    }

    public function destroyDocument(User $accountant, string $document): RedirectResponse
    {
        Gate::authorize('manageAccountantDocuments', $accountant);
        $documents = $this->decodeDocuments($accountant);

        if (!array_key_exists($document, $documents)) {
            abort(404);
        }

        $fileName = $documents[$document];
        unset($documents[$document]);
        $accountant->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);
        $this->deleteDocumentFile($accountant, $fileName);

        return redirect()->route('admin.accountant.documents', $accountant)->with('message', 'Document removed successfully.');
    }

    private function normalizeAccountant(User $accountant): User
    {
        $profile = $this->decodeProfile($accountant);
        $photo = basename((string) ($profile['photo'] ?? ''));
        $accountant->setAttribute('profile_information', $profile);
        $accountant->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));

        return $accountant;
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
    private function decodeProfile(User $accountant): array
    {
        $profile = json_decode($accountant->user_information ?? '{}', true);

        return is_array($profile) ? $profile : [];
    }

    /** @return array<string, string> */
    private function decodeDocuments(User $accountant): array
    {
        $documents = json_decode($accountant->documents ?? '{}', true);

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

    private function hasFinancialOrMessageHistory(User $accountant): bool
    {
        return DB::table('payments')->where('user_id', $accountant->id)->exists()
            || DB::table('chats')->where('sender_id', $accountant->id)->orWhere('reciver_id', $accountant->id)->exists()
            || DB::table('message_thrades')->where('sender_id', $accountant->id)->orWhere('reciver_id', $accountant->id)->exists();
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

    private function deleteDocumentFile(User $accountant, string $fileName): void
    {
        $safeName = basename($fileName);

        if ($safeName === '' || $safeName !== $fileName) {
            return;
        }

        $path = public_path('assets/uploads/user-docs/'.$accountant->id.'/'.$safeName);

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
