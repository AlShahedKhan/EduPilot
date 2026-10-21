<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolClassController;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\Classes;
use App\Models\School;
use App\Models\Section;
use App\Models\TeacherPermission;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ClassesPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SchoolClassManagementTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test class management.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test class management.');
        }

        $this->primaryAdmin = $this->createUser(['role_id' => 2, 'school_role' => 1]);
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

    public function test_routes_use_restful_methods_and_remove_the_separate_section_editor(): void
    {
        foreach (['admin.class_list' => 'GET', 'admin.create.class' => 'POST', 'admin.class.update' => 'PATCH', 'admin.class.delete' => 'DELETE'] as $name => $method) {
            $this->assertContains($method, Route::getRoutes()->getByName($name)->methods());
        }

        $this->assertNull(Route::getRoutes()->getByName('admin.edit.section'));
        $this->assertNull(Route::getRoutes()->getByName('admin.section.update'));
    }

    public function test_secondary_admin_can_view_but_only_primary_admin_can_mutate(): void
    {
        $schoolClass = $this->createClass();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.class_list'])]);
        $policy = app(ClassesPolicy::class);

        $this->assertTrue($policy->viewAny($secondary));
        $this->assertTrue($policy->view($secondary, $schoolClass));
        $this->assertFalse($policy->update($secondary, $schoolClass));
        $this->assertTrue($policy->update($this->primaryAdmin, $schoolClass));
    }

    public function test_directory_filters_sections_and_renders_responsive_markup_without_blade_queries(): void
    {
        $token = Str::lower(Str::random(10));
        $schoolClass = $this->createClass(['name' => "<script>{$token}</script>"]);
        Section::query()->create(['name' => 'North '.$token, 'class_id' => $schoolClass->id, 'school_id' => $this->schoolId]);

        $view = app(SchoolClassController::class)->index(request()->create('/admin/class_list', 'GET', ['search' => $token, 'usage' => 'empty']));
        $html = $view->render();

        $this->assertSame(1, $view->getData()['classLists']->total());
        $this->assertStringContainsString('school-class-page__desktop', $html);
        $this->assertStringContainsString('school-class-page__mobile', $html);
        $this->assertStringContainsString(e($schoolClass->name), $html);
        $this->assertStringNotContainsString($schoolClass->name, $html);
        $this->assertStringNotContainsString('Section::', $html);
        $this->assertStringContainsString('search='.$token, $view->getData()['classLists']->url(1));
    }

    public function test_class_and_sections_are_updated_atomically_with_explicit_removals(): void
    {
        $schoolClass = $this->createClass();
        $sectionA = $this->createSection($schoolClass, 'A');
        $sectionB = $this->createSection($schoolClass, 'B');
        $request = UpdateSchoolClassRequest::create('/admin/classes/'.$schoolClass->id, 'PATCH', [
            'name' => $schoolClass->name.' Updated',
            'sections' => [
                ['id' => $sectionA->id, 'name' => 'Alpha'],
                ['name' => 'C'],
            ],
            'removed_section_ids' => [$sectionB->id],
        ]);
        $this->prepareRequest($request, $schoolClass);
        $request->validateResolved();

        app(SchoolClassController::class)->update($request, $schoolClass);

        $this->assertSame($schoolClass->name.' Updated', $schoolClass->refresh()->name);
        $this->assertEqualsCanonicalizing(['Alpha', 'C'], $schoolClass->sections()->pluck('name')->all());
        $this->assertModelMissing($sectionB);
    }

    public function test_linked_section_removal_rolls_back_the_complete_update(): void
    {
        $schoolClass = $this->createClass();
        $sectionA = $this->createSection($schoolClass, 'A');
        $sectionB = $this->createSection($schoolClass, 'B');
        $teacher = $this->createUser(['role_id' => 3]);
        TeacherPermission::query()->create([
            'school_id' => $this->schoolId,
            'class_id' => $schoolClass->id,
            'section_id' => $sectionB->id,
            'teacher_id' => $teacher->id,
            'marks' => false,
            'attendance' => false,
        ]);
        $request = UpdateSchoolClassRequest::create('/admin/classes/'.$schoolClass->id, 'PATCH', [
            'name' => 'Should Not Persist '.Str::random(6),
            'sections' => [['id' => $sectionA->id, 'name' => 'Changed']],
            'removed_section_ids' => [$sectionB->id],
        ]);
        $this->prepareRequest($request, $schoolClass);
        $request->validateResolved();

        try {
            app(SchoolClassController::class)->update($request, $schoolClass);
            $this->fail('A linked section should not be removable.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('removed_section_ids', $exception->errors());
        }

        $this->assertNotSame('Changed', $sectionA->refresh()->name);
        $this->assertModelExists($sectionB);
    }

    public function test_section_names_can_be_swapped_atomically(): void
    {
        $schoolClass = $this->createClass();
        $sectionA = $this->createSection($schoolClass, 'A');
        $sectionB = $this->createSection($schoolClass, 'B');
        $request = UpdateSchoolClassRequest::create('/admin/classes/'.$schoolClass->id, 'PATCH', [
            'name' => $schoolClass->name,
            'sections' => [
                ['id' => $sectionA->id, 'name' => 'B'],
                ['id' => $sectionB->id, 'name' => 'A'],
            ],
        ]);
        $this->prepareRequest($request, $schoolClass);
        $request->validateResolved();

        app(SchoolClassController::class)->update($request, $schoolClass);

        $this->assertSame('B', $sectionA->refresh()->name);
        $this->assertSame('A', $sectionB->refresh()->name);
    }

    public function test_a_removed_section_name_can_be_reused_in_the_same_atomic_update(): void
    {
        $schoolClass = $this->createClass();
        $sectionA = $this->createSection($schoolClass, 'A');
        $sectionB = $this->createSection($schoolClass, 'B');
        $request = UpdateSchoolClassRequest::create('/admin/classes/'.$schoolClass->id, 'PATCH', [
            'name' => $schoolClass->name,
            'sections' => [
                ['id' => $sectionB->id, 'name' => 'B'],
                ['name' => 'A'],
            ],
            'removed_section_ids' => [$sectionA->id],
        ]);
        $this->prepareRequest($request, $schoolClass);
        $request->validateResolved();

        app(SchoolClassController::class)->update($request, $schoolClass);

        $this->assertModelMissing($sectionA);
        $this->assertEqualsCanonicalizing(['A', 'B'], $schoolClass->sections()->pluck('name')->all());
    }

    public function test_linked_class_is_blocked_and_unused_class_is_deleted(): void
    {
        $linked = $this->createClass();
        $linkedSection = $this->createSection($linked, 'A');
        $teacher = $this->createUser(['role_id' => 3]);
        TeacherPermission::query()->create(['school_id' => $this->schoolId, 'class_id' => $linked->id, 'section_id' => $linkedSection->id, 'teacher_id' => $teacher->id, 'marks' => false, 'attendance' => false]);

        app(SchoolClassController::class)->destroy($linked);
        $this->assertModelExists($linked);
        $this->assertStringContainsString('teacher permissions', (string) session('error'));

        $unused = $this->createClass();
        $this->createSection($unused, 'A');
        app(SchoolClassController::class)->destroy($unused);
        $this->assertModelMissing($unused);
    }

    private function createClass(array $attributes = []): Classes
    {
        return Classes::query()->create(array_merge([
            'name' => 'Class '.Str::random(8),
            'school_id' => $this->schoolId,
        ], $attributes));
    }

    private function createSection(Classes $schoolClass, string $name): Section
    {
        return Section::query()->create(['name' => $name, 'class_id' => $schoolClass->id, 'school_id' => $this->schoolId]);
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Class Test User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 2,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['photo' => '']),
        ], $attributes));
    }

    private function prepareRequest(UpdateSchoolClassRequest $request, Classes $schoolClass): void
    {
        $route = new RoutingRoute(['PATCH'], 'admin/classes/{class}', fn () => null);
        $route->bind($request);
        $route->setParameter('class', $schoolClass);
        $request->setRouteResolver(fn (): RoutingRoute => $route);
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));
        $request->setUserResolver(fn (): User => $this->primaryAdmin);
    }
}
