<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolTeacherController;
use App\Http\Requests\StoreSchoolTeacherRequest;
use App\Http\Requests\UpdateSchoolTeacherPasswordRequest;
use App\Http\Requests\UpdateSchoolTeacherStatusRequest;
use App\Models\Appraisal;
use App\Models\Department;
use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SchoolTeacherDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private Department $department;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the school teacher directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = app('db')->connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test the school teacher directory.');
        }

        $this->department = Department::query()->create([
            'name' => 'Directory Test Department '.Str::random(8),
            'school_id' => $this->schoolId,
        ]);
        $this->primaryAdmin = $this->createUser([
            'role_id' => 2,
            'school_role' => 1,
            'name' => 'Primary Teacher Directory Admin',
        ]);

        Auth::login($this->primaryAdmin);
    }

    protected function tearDown(): void
    {
        Auth::logout();

        if (isset($this->tenantConnection) && $this->tenantConnection->transactionLevel() > 0) {
            $this->tenantConnection->rollBack();
        }

        if (tenancy()->initialized) {
            tenancy()->end();
        }

        parent::tearDown();
    }

    public function test_teacher_routes_use_safe_http_methods_and_admin_middleware(): void
    {
        $expectedMethods = [
            'admin.teacher' => 'GET',
            'admin.teacher.create' => 'POST',
            'admin.teacher.update' => 'PATCH',
            'admin.teacher.delete' => 'DELETE',
            'admin.teacher.status' => 'PATCH',
            'admin.teacher.password' => 'PATCH',
        ];

        foreach ($expectedMethods as $routeName => $method) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
            $this->assertContains('auth', $route->middleware());
            $this->assertContains('admin', $route->middleware());
        }
    }

    public function test_index_searches_filters_departments_and_preserves_query_parameters(): void
    {
        $token = Str::lower(Str::random(10));

        foreach (range(1, 11) as $number) {
            $this->createTeacher([
                'name' => "{$token} Teacher {$number}",
                'account_status' => $number === 11 ? 'disable' : 'enable',
            ]);
        }

        $this->createTeacher([
            'name' => "{$token} Unassigned",
            'department_id' => null,
        ]);

        $request = request()->create('/admin/teacher', 'GET', [
            'search' => $token,
            'status' => 'active',
            'department' => $this->department->id,
        ]);
        $view = app(SchoolTeacherController::class)->index($request);
        $teachers = $view->getData()['teachers'];
        $summary = $view->getData()['summary'];

        $this->assertSame(10, $teachers->total());
        $this->assertStringContainsString("search={$token}", $teachers->url(1));
        $this->assertStringContainsString('status=active', $teachers->url(1));
        $this->assertStringContainsString('department='.$this->department->id, $teachers->url(1));
        $this->assertGreaterThanOrEqual(12, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);
        $this->assertGreaterThanOrEqual(1, (int) $summary->unassigned);
    }

    public function test_policy_allows_permitted_secondary_admins_to_view_but_only_primary_to_mutate(): void
    {
        $teacher = $this->createTeacher();
        $secondaryAdmin = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.teacher'])]);
        $otherRole = $this->createUser(['role_id' => 4]);
        $otherSchool = School::query()->create(['title' => 'Other School', 'status' => 1]);
        $otherSchoolTeacher = $this->createTeacher(['school_id' => $otherSchool->id, 'department_id' => null]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewAnyTeacher($secondaryAdmin));
        $this->assertTrue($policy->viewTeacher($secondaryAdmin, $teacher));
        $this->assertFalse($policy->updateTeacher($secondaryAdmin, $teacher));
        $this->assertTrue($policy->createTeacher($this->primaryAdmin));
        $this->assertTrue($policy->updateTeacher($this->primaryAdmin, $teacher));
        $this->assertFalse($policy->viewTeacher($this->primaryAdmin, $otherRole));
        $this->assertFalse($policy->viewTeacher($this->primaryAdmin, $otherSchoolTeacher));
    }

    public function test_status_update_is_validated_and_redirects_to_directory(): void
    {
        $teacher = $this->createTeacher();
        $request = $this->statusRequest($teacher, 'disable');
        $response = app(SchoolTeacherController::class)->updateStatus($request, $teacher);

        $this->assertSame('disable', $teacher->refresh()->account_status);
        $this->assertSame(route('admin.teacher'), $response->getTargetUrl());

        $this->expectException(ValidationException::class);
        $this->statusRequest($teacher, 'archived');
    }

    public function test_store_validation_rejects_duplicate_email_weak_password_and_invalid_department(): void
    {
        $teacher = $this->createTeacher();
        $request = StoreSchoolTeacherRequest::create('/admin/teacher', 'POST', [
            'name' => 'Duplicate Teacher',
            'email' => strtoupper($teacher->email),
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'department_id' => 999999999,
            'designation' => 'Teacher',
            'birthday' => '01/01/2000',
            'gender' => 'Male',
            'phone' => '+8801700000000',
            'address' => 'Dhaka',
        ]);
        $this->prepareFormRequest($request);

        try {
            $request->validateResolved();
            $this->fail('Validation should have failed.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('email', $exception->errors());
            $this->assertArrayHasKey('password', $exception->errors());
            $this->assertArrayHasKey('department_id', $exception->errors());
        }
    }

    public function test_deletion_is_blocked_for_linked_academic_records_and_allowed_without_links(): void
    {
        $linkedTeacher = $this->createTeacher();
        Appraisal::query()->create([
            'title' => 'Linked appraisal',
            'teacher_id' => $linkedTeacher->id,
            'school_id' => $this->schoolId,
        ]);

        $blockedResponse = app(SchoolTeacherController::class)->destroy($linkedTeacher);

        $this->assertModelExists($linkedTeacher);
        $this->assertSame(route('admin.teacher'), $blockedResponse->getTargetUrl());
        $this->assertStringContainsString('cannot be deleted', session('error'));

        $unlinkedTeacher = $this->createTeacher();
        app(SchoolTeacherController::class)->destroy($unlinkedTeacher);
        $this->assertModelMissing($unlinkedTeacher);
    }

    public function test_password_update_hashes_a_valid_confirmed_password(): void
    {
        $teacher = $this->createTeacher();
        $request = UpdateSchoolTeacherPasswordRequest::create('/admin/teacher/'.$teacher->id.'/password', 'PATCH', [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);
        $route = new \Illuminate\Routing\Route('PATCH', '/admin/teacher/{teacher}/password', []);
        $route->bind($request);
        $route->setParameter('teacher', $teacher);
        $request->setRouteResolver(fn () => $route);
        $this->prepareFormRequest($request);
        $request->validateResolved();

        app(SchoolTeacherController::class)->updatePassword($request, $teacher);

        $this->assertTrue(Hash::check('NewPassword123', $teacher->refresh()->password));
    }

    public function test_rendered_directory_escapes_content_and_has_desktop_and_mobile_views(): void
    {
        $token = Str::lower(Str::random(10));
        $teacher = $this->createTeacher([
            'name' => "<script>{$token}</script>",
            'email' => "{$token}@example.test",
            'designation' => str_repeat('Senior Teacher ', 12),
            'user_information' => '{invalid json',
        ]);

        $view = app(SchoolTeacherController::class)->index(request()->create('/admin/teacher', 'GET', ['search' => $token]));
        $html = $view->render();

        $this->assertStringContainsString('school-teacher-directory__desktop', $html);
        $this->assertStringContainsString('school-teacher-directory__mobile', $html);
        $this->assertStringContainsString(e($teacher->name), $html);
        $this->assertStringNotContainsString($teacher->name, $html);
        $this->assertStringContainsString('Not provided', $html);
        $this->assertStringContainsString('data-teacher-export="pdf"', $html);
    }

    /** @param array<string, mixed> $attributes */
    private function createTeacher(array $attributes = []): User
    {
        return $this->createUser(array_merge([
            'role_id' => 3,
            'department_id' => $this->department->id,
            'designation' => 'Teacher',
        ], $attributes));
    }

    /** @param array<string, mixed> $attributes */
    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Directory User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 3,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode([
                'gender' => 'Male',
                'blood_group' => 'o+',
                'birthday' => 946684800,
                'phone' => '+8801700000000',
                'address' => 'Dhaka',
                'photo' => '',
            ]),
        ], $attributes));
    }

    private function statusRequest(User $teacher, string $status): UpdateSchoolTeacherStatusRequest
    {
        $request = UpdateSchoolTeacherStatusRequest::create('/admin/teacher/'.$teacher->id.'/status', 'PATCH', ['status' => $status]);
        $route = new \Illuminate\Routing\Route('PATCH', '/admin/teacher/{teacher}/status', []);
        $route->bind($request);
        $route->setParameter('teacher', $teacher);
        $request->setRouteResolver(fn () => $route);
        $this->prepareFormRequest($request);
        $request->validateResolved();

        return $request;
    }

    private function prepareFormRequest($request): void
    {
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));
        $request->setUserResolver(fn (): User => $this->primaryAdmin);
    }
}
