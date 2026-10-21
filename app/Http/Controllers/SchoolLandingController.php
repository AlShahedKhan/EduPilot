<?php

namespace App\Http\Controllers;

use App\Models\Noticeboard;
use App\Models\School;
use App\Models\User;
use App\Http\Requests\StoreContactRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SchoolLandingController extends Controller
{
    /**
     * Show the school-specific public landing page.
     */
    public function index(Request $request)
    {
        $school = School::first();

        if (!$school) {
            return redirect()->route('login');
        }

        // Global settings helper wrapped in try/catch for resilience
        $settings = $this->getSettings([
            'banner_title', 'banner_subtitle', 'contact_email', 'system_email',
            'phone', 'address', 'facebook_link', 'twitter_link', 'linkedin_link',
            'instagram_link', 'frontend_footer_text', 'system_name', 'front_logo',
            'light_logo', 'dark_logo',
        ]);

        try {
            $today = CarbonImmutable::today();
            $notices = Noticeboard::query()
                ->where('school_id', $school->id)
                ->where('status', true)
                ->where('show_on_website', true)
                ->latest()
                ->get([
                    'id', 'notice_title', 'notice', 'start_date', 'start_time',
                    'end_date', 'end_time', 'status', 'show_on_website', 'image',
                    'created_at',
                ])
                ->filter(fn (Noticeboard $notice): bool => $notice->isVisibleOnWebsite($today))
                ->take(6)
                ->values();
        } catch (\Throwable $e) {
            $notices = collect();
        }

        // Teachers (role_id = 3)
        try {
            $teachers = User::where('role_id', 3)
                ->where('status', 1)
                ->take(8)
                ->get()
                ->map(function (User $user) {
                    $info = json_decode($user->user_information ?? '{}', true) ?: [];
                    return (object) [
                        'name'        => $user->name,
                        'email'       => $user->email,
                        'designation' => $user->designation ?? 'Teacher',
                        'photo'       => $info['photo'] ?? null,
                        'gender'      => $info['gender'] ?? '',
                    ];
                });
        } catch (\Throwable $e) {
            $teachers = collect();
        }

        // Students (role_id = 7)
        try {
            $students = User::where('role_id', 7)
                ->where('school_id', $school->id)
                ->where('status', 1)
                ->take(10)
                ->get()
                ->map(function (User $user) {
                    $info = json_decode($user->user_information ?? '{}', true) ?: [];
                    return (object) [
                        'name'   => $user->name,
                        'code'   => $user->code ?? '',
                        'photo'  => $info['photo'] ?? null,
                        'gender' => $info['gender'] ?? '',
                    ];
                });
        } catch (\Throwable $e) {
            $students = collect();
        }

        // Events
        try {
            $events = \App\Models\FrontendEvent::where('status', '1')
                ->orderByDesc('timestamp')
                ->take(4)
                ->get();
        } catch (\Throwable $e) {
            $events = collect();
        }

        // Stats
        $totalStudents = User::where('role_id', 7)->count();
        $totalTeachers = User::where('role_id', 3)->count();

        // Dynamic dashboard route for authenticated users
        $dashboardRoute = route('login');
        if (auth()->check()) {
            $role_id = auth()->user()->role_id;
            $dashboardRoute = match((int) $role_id) {
                2 => route('admin.dashboard'),
                3 => route('teacher.dashboard'),
                4 => route('accountant.dashboard'),
                5 => route('librarian.dashboard'),
                6 => route('parent.dashboard'),
                7 => route('student.dashboard'),
                8 => route('driver.dashboard'),
                9 => route('alumni.dashboard'),
                10 => route('warden.dashboard'),
                default => route('admin.dashboard'),
            };
        }

        return view('frontend.school_landing', compact(
            'school',
            'settings',
            'notices',
            'events',
            'teachers',
            'students',
            'totalStudents',
            'totalTeachers',
            'dashboardRoute'
        ));
    }

    /**
     * Handle public contact form submission.
     */
    public function contactStore(StoreContactRequest $request)
    {
        if ($request->filled('website')) {
            return redirect()->to(url()->previous() . '#contact')
                ->with('success', 'Thank you for reaching out! Your message has been received.');
        }

        $school = School::firstOrFail();
        \App\Models\Contact::create([
            ...$request->safe()->only(['name', 'email', 'phone', 'message']),
            'school' => $school->title,
            'status' => 'new',
        ]);

        return redirect()->to(url()->previous() . '#contact')
            ->with('success', 'Thank you for reaching out! Your message has been received.');
    }

    /**
     * Resolve multiple global_settings keys into an associative array.
     *
     * @param  array<int, string>  $keys
     * @return array<string, string|null>
     */
    private function getSettings(array $keys): array
    {
        $result = array_fill_keys($keys, null);

        try {
            if (!Schema::hasTable('global_settings')) {
                return $result;
            }

            DB::table('global_settings')
                ->whereIn('key', $keys)
                ->get(['key', 'value'])
                ->each(function ($row) use (&$result) {
                    $result[$row->key] = $row->value;
                });
        } catch (\Throwable $e) {
            // Silently ignore DB errors (e.g. during tenant boot)
        }

        return $result;
    }
}
