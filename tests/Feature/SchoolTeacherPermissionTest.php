<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolTeacherPermissionController;
use App\Http\Controllers\TeacherController;
use App\Http\Requests\FilterSchoolTeacherPermissionRequest;
use App\Http\Requests\UpdateSchoolTeacherPermissionRequest;
use App\Models\Classes;
use App\Models\School;
use App\Models\Section;
use App\Models\TeacherPermission;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SchoolTeacherPermissionTest extends TestCase
{
    private ConnectionInterface $tenantConnection;
    private User $primaryAdmin;
    private Classes $class;
    private Section $section;
    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test teacher permissions.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test teacher permissions.');
        }

        $this->primaryAdmin = $this->createUser(['role_id' => 2, 'school_role' => 1]);
        $this->class = Classes::query()->create(['name' => 'Permission Class '.Str::random(6), 'school_id' => $this->schoolId]);
        $this->section = Section::query()->create(['name' => 'Permission Section '.Str::random(6), 'class_id' => $this->class->id, 'school_id' => $this->schoolId]);
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

    public function test_permission_routes_use_get_and_put_and_legacy_routes_are_removed(): void
    {
        $this->assertContains('GET', Route::getRoutes()->getByName('admin.teacher.permission')->methods());
        $this->assertContains('PUT', Route::getRoutes()->getByName('admin.teacher.permission.update')->methods());
        $this->assertNull(Route::getRoutes()->getByName('admin.teacher.permission_list'));
        $this->assertNull(Route::getRoutes()->getByName('admin.teacher.modify_permission'));

        $legacyUpdateRoute = collect(Route::getRoutes()->getRoutes())
            ->first(fn ($route): bool => $route->uri() === 'admin/teacher/permission_update');

        $this->assertNotNull($legacyUpdateRoute);
        $this->assertContains('PUT', $legacyUpdateRoute->methods());
        $this->assertNotContains('GET', $legacyUpdateRoute->methods());
    }

    public function test_secondary_admin_can_view_but_only_primary_can_update(): void
    {
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.teacher.permission'])]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewTeacherPermissions($secondary));
        $this->assertFalse($policy->manageTeacherPermissions($secondary));
        $this->assertTrue($policy->manageTeacherPermissions($this->primaryAdmin));
    }

    public function test_matrix_filters_and_renders_responsive_controls_without_blade_queries(): void
    {
        $teacher = $this->createUser(['role_id' => 3, 'name' => 'Matrix Teacher '.Str::random(6)]);
        TeacherPermission::query()->create(['school_id' => $this->schoolId, 'class_id' => $this->class->id, 'section_id' => $this->section->id, 'teacher_id' => $teacher->id, 'marks' => true, 'attendance' => false]);
        $request = FilterSchoolTeacherPermissionRequest::create('/admin/permission', 'GET', ['class_id' => $this->class->id, 'section_id' => $this->section->id, 'permission' => 'marks']);
        $this->prepareRequest($request);
        $request->validateResolved();
        $html = app(SchoolTeacherPermissionController::class)->index($request)->render();

        $this->assertStringContainsString('school-permission-page__desktop', $html);
        $this->assertStringContainsString('school-permission-page__mobile', $html);
        $this->assertStringContainsString(e($teacher->name), $html);
        $this->assertStringContainsString('data-permission="class_access"', $html);
        $this->assertStringNotContainsString('TeacherPermission::', $html);
    }

    public function test_update_creates_changes_and_revokes_page_permissions(): void
    {
        $granted = $this->createUser(['role_id' => 3]);
        $revoked = $this->createUser(['role_id' => 3]);
        TeacherPermission::query()->create(['school_id' => $this->schoolId, 'class_id' => $this->class->id, 'section_id' => $this->section->id, 'teacher_id' => $revoked->id, 'marks' => true, 'attendance' => true]);

        $request = UpdateSchoolTeacherPermissionRequest::create('/admin/permission', 'PUT', [
            'class_id' => $this->class->id,
            'section_id' => $this->section->id,
            'permissions' => [
                $granted->id => ['class_access' => '1', 'marks' => '1', 'attendance' => '0'],
                $revoked->id => ['class_access' => '0', 'marks' => '0', 'attendance' => '0'],
            ],
        ]);
        $this->prepareRequest($request);
        $request->validateResolved();
        app(SchoolTeacherPermissionController::class)->update($request);

        $this->assertTrue(TeacherPermission::query()->where('teacher_id', $granted->id)->where('marks', true)->exists());
        $this->assertFalse(TeacherPermission::query()->where('teacher_id', $revoked->id)->exists());
    }

    public function test_teacher_cannot_open_marks_without_the_specific_permission(): void
    {
        $teacher = $this->createUser(['role_id' => 3]);
        Auth::login($teacher);

        try {
            app(TeacherController::class)->marksFilter(request()->create('/teacher/marks', 'GET', [
                'class_id' => $this->class->id,
                'section_id' => $this->section->id,
            ]));
            $this->fail('The marks page should reject a teacher without marks permission.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Permission User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 3,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['photo' => '', 'phone' => '', 'address' => '']),
        ], $attributes));
    }

    private function prepareRequest($request): void
    {
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));
        $request->setUserResolver(fn (): User => $this->primaryAdmin);
    }
}
