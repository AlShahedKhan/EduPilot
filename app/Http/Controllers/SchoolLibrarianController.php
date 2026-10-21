<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolLibrarianDocumentRequest;
use App\Http\Requests\StoreSchoolLibrarianRequest;
use App\Http\Requests\UpdateSchoolLibrarianPasswordRequest;
use App\Http\Requests\UpdateSchoolLibrarianRequest;
use App\Http\Requests\UpdateSchoolLibrarianStatusRequest;
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

class SchoolLibrarianController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAnyLibrarian', User::class);

        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->toString();

        if (!in_array($status, ['active', 'disabled', 'recent'], true)) {
            $status = '';
        }

        $baseQuery = User::query()
            ->where('role_id', 5)
            ->where('school_id', auth()->user()->school_id);

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN account_status = 'disable' THEN 1 ELSE 0 END) as disabled")
            ->selectRaw("SUM(CASE WHEN account_status IS NULL OR account_status != 'disable' THEN 1 ELSE 0 END) as active")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [now()->subDays(30)->startOfDay()])
            ->first();

        $librarians = $baseQuery
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
            ->through(fn (User $librarian): User => $this->normalizeLibrarian($librarian));

        return view('admin.librarian.librarian_list', [
            'librarians' => $librarians,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'canManageLibrarians' => Gate::allows('createLibrarian', User::class),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('createLibrarian', User::class);

        return view('admin.librarian.add_librarian');
    }

    public function store(StoreSchoolLibrarianRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $photo = $this->resolvePhoto($request);

        $librarian = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => 5,
            'school_id' => auth()->user()->school_id,
            'user_information' => $this->profileInformation($validated, $photo),
            'status' => 1,
            'account_status' => 'enable',
        ]);

        $mailFailed = false;

        if ($this->mailIsConfigured()) {
            try {
                Mail::to($librarian->email)->send(new NewUserEmail($validated));
            } catch (\Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()->route('admin.librarian')->with('message', 'Librarian created successfully.');

        if ($mailFailed) {
            $response->with('warning', 'The librarian was created, but the welcome email could not be sent.');
        }

        return $response;
    }

    public function edit(User $librarian): View
    {
        Gate::authorize('updateLibrarian', $librarian);

        return view('admin.librarian.edit_librarian', [
            'user' => $librarian,
            'profile' => $this->decodeProfile($librarian),
        ]);
    }

    public function update(UpdateSchoolLibrarianRequest $request, User $librarian): RedirectResponse
    {
        $validated = $request->validated();
        $currentProfile = $this->decodeProfile($librarian);
        $photo = $this->resolvePhoto($request, (string) ($currentProfile['photo'] ?? ''));

        $librarian->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_information' => $this->profileInformation($validated, $photo),
        ]);

        $previousPhoto = (string) ($currentProfile['photo'] ?? '');

        if ($photo !== $previousPhoto) {
            $this->deletePhoto($previousPhoto);
        }

        return redirect()->route('admin.librarian')->with('message', 'Librarian updated successfully.');
    }

    public function destroy(User $librarian): RedirectResponse
    {
        Gate::authorize('deleteLibrarian', $librarian);

        if ($this->hasFinancialOrMessageHistory($librarian)) {
            return redirect()->route('admin.librarian')->with(
                'error',
                'This librarian has linked financial or message history and cannot be deleted. Disable the account instead.',
            );
        }

        $photo = (string) ($this->decodeProfile($librarian)['photo'] ?? '');
        $documents = $this->decodeDocuments($librarian);
        $librarian->delete();
        $this->deletePhoto($photo);

        foreach ($documents as $fileName) {
            $this->deleteDocumentFile($librarian, $fileName);
        }

        return redirect()->route('admin.librarian')->with('message', 'Librarian deleted successfully.');
    }

    public function updateStatus(UpdateSchoolLibrarianStatusRequest $request, User $librarian): RedirectResponse
    {
        $librarian->update(['account_status' => $request->validated('status')]);

        return redirect()->route('admin.librarian')->with('message', 'Librarian status updated successfully.');
    }

    public function profile(User $librarian): View
    {
        Gate::authorize('viewLibrarian', $librarian);

        return view('admin.librarian.librarian_profile', [
            'librarian' => $this->normalizeLibrarian($librarian),
            'schoolTitle' => School::query()->whereKey(auth()->user()->school_id)->value('title'),
            'canManageLibrarian' => Gate::allows('updateLibrarianPassword', $librarian),
        ]);
    }

    public function updatePassword(UpdateSchoolLibrarianPasswordRequest $request, User $librarian): RedirectResponse
    {
        $librarian->update(['password' => Hash::make($request->validated('password'))]);

        return redirect()->route('admin.librarian')->with('message', 'Librarian password updated successfully.');
    }

    public function documents(User $librarian): View
    {
        Gate::authorize('manageLibrarianDocuments', $librarian);

        return view('admin.librarian.documents', [
            'librarian' => $librarian,
            'documents' => $this->decodeDocuments($librarian),
        ]);
    }

    public function storeDocument(StoreSchoolLibrarianDocumentRequest $request, User $librarian): RedirectResponse
    {
        $validated = $request->validated();
        $documents = $this->decodeDocuments($librarian);
        $key = Str::slug($validated['file_name']);
        $key = $key !== '' ? $key : 'document';
        $key .= '-'.Str::lower(Str::random(6));
        $file = $validated['file'];
        $fileName = Str::uuid().'.'.$file->extension();
        $file->move(public_path('assets/uploads/user-docs/'.$librarian->id), $fileName);
        $documents[$key] = $fileName;
        $librarian->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);

        return redirect()->route('admin.librarian.documents', $librarian)->with('message', 'Document uploaded successfully.');
    }

    public function destroyDocument(User $librarian, string $document): RedirectResponse
    {
        Gate::authorize('manageLibrarianDocuments', $librarian);
        $documents = $this->decodeDocuments($librarian);

        if (!array_key_exists($document, $documents)) {
            abort(404);
        }

        $fileName = $documents[$document];
        unset($documents[$document]);
        $librarian->update(['documents' => json_encode($documents, JSON_THROW_ON_ERROR)]);
        $this->deleteDocumentFile($librarian, $fileName);

        return redirect()->route('admin.librarian.documents', $librarian)->with('message', 'Document removed successfully.');
    }

    private function normalizeLibrarian(User $librarian): User
    {
        $profile = $this->decodeProfile($librarian);
        $photo = basename((string) ($profile['photo'] ?? ''));
        $librarian->setAttribute('profile_information', $profile);
        $librarian->setAttribute('photo_url', asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png')));

        return $librarian;
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
    private function decodeProfile(User $librarian): array
    {
        $profile = json_decode($librarian->user_information ?? '{}', true);

        return is_array($profile) ? $profile : [];
    }

    /** @return array<string, string> */
    private function decodeDocuments(User $librarian): array
    {
        $documents = json_decode($librarian->documents ?? '{}', true);

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

    private function hasFinancialOrMessageHistory(User $librarian): bool
    {
        return DB::table('payments')->where('user_id', $librarian->id)->exists()
            || DB::table('chats')->where('sender_id', $librarian->id)->orWhere('reciver_id', $librarian->id)->exists()
            || DB::table('message_thrades')->where('sender_id', $librarian->id)->orWhere('reciver_id', $librarian->id)->exists();
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

    private function deleteDocumentFile(User $librarian, string $fileName): void
    {
        $safeName = basename($fileName);

        if ($safeName === '' || $safeName !== $fileName) {
            return;
        }

        $path = public_path('assets/uploads/user-docs/'.$librarian->id.'/'.$safeName);

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
