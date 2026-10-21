<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolSubjectController;
use App\Http\Requests\UpdateSchoolSubjectRequest;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Gradebook;
use App\Models\School;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\SubjectPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SchoolSubjectManagementTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test subject management.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test subject management.');
        }

        $this->session = Session::query()->create([
            'session_title' => 'Subject Session '.Str::random(6),
            'status' => 1,
            'school_id' => $this->schoolId,
        ]);
        School::query()->whereKey($this->schoolId)->update(['running_session' => $this->session->id]);
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

    public function test_subject_routes_and_primary_only_policy(): void
    {
        foreach (['admin.subject_list' => 'GET', 'admin.create.subject' => 'POST', 'admin.subject.update' => 'PATCH', 'admin.subject.delete' => 'DELETE'] as $name => $method) {
            $this->assertContains($method, Route::getRoutes()->getByName($name)->methods());
        }

        $subject = $this->createSubject();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.subject_list'])]);
        $policy = app(SubjectPolicy::class);

        $this->assertTrue($policy->viewAny($secondary));
        $this->assertTrue($policy->view($secondary, $subject));
        $this->assertFalse($policy->update($secondary, $subject));
        $this->assertTrue($policy->update($this->primaryAdmin, $subject));
    }

    public function test_directory_filters_and_renders_escaped_responsive_markup(): void
    {
        $token = Str::lower(Str::random(10));
        $class = $this->createClass();
        $subject = $this->createSubject($class, ['name' => "<script>{$token}</script>"]);

        $view = app(SchoolSubjectController::class)->index(request()->create('/admin/subject', 'GET', [
            'search' => $token,
            'class_id' => $class->id,
            'session_id' => $this->session->id,
            'usage' => 'unused',
        ]));
        $html = $view->render();

        $this->assertSame(1, $view->getData()['subjects']->total());
        $this->assertStringContainsString('school-subject-page__desktop', $html);
        $this->assertStringContainsString('school-subject-page__mobile', $html);
        $this->assertStringContainsString(e($subject->name), $html);
        $this->assertStringNotContainsString($subject->name, $html);
        $this->assertStringContainsString('class_id='.$class->id, $view->getData()['subjects']->url(1));
    }

    public function test_referenced_subject_can_be_renamed_but_not_moved(): void
    {
        $class = $this->createClass();
        $otherClass = $this->createClass();
        $subject = $this->createSubject($class);
        Exam::query()->create([
            'name' => 'Exam '.Str::random(5),
            'exam_type' => 'offline',
            'starting_time' => '09:00',
            'ending_time' => '10:00',
            'total_marks' => 100,
            'status' => 'published',
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'school_id' => $this->schoolId,
            'session_id' => $this->session->id,
        ]);

        $renameRequest = $this->makeUpdateRequest($subject, [
            'name' => 'Renamed subject',
            'class_id' => $class->id,
            'session_id' => $this->session->id,
        ]);
        $renameRequest->validateResolved();
        app(SchoolSubjectController::class)->update($renameRequest, $subject);
        $this->assertSame('Renamed subject', $subject->refresh()->name);

        $moveRequest = $this->makeUpdateRequest($subject, [
            'name' => 'Renamed subject',
            'class_id' => $otherClass->id,
            'session_id' => $this->session->id,
        ]);
        $moveRequest->validateResolved();

        $this->expectException(ValidationException::class);
        app(SchoolSubjectController::class)->update($moveRequest, $subject);
    }

    public function test_gradebook_json_subject_reference_blocks_deletion_and_unused_subject_is_deleted(): void
    {
        $class = $this->createClass();
        $subject = $this->createSubject($class);
        $category = ExamCategory::query()->create([
            'name' => 'Category '.Str::random(5),
            'school_id' => $this->schoolId,
            'session_id' => $this->session->id,
            'timestamp' => now()->timestamp,
        ]);
        $student = $this->createUser(['role_id' => 7]);
        Gradebook::query()->create([
            'class_id' => $class->id,
            'section_id' => $this->createSection($class)->id,
            'student_id' => $student->id,
            'exam_category_id' => $category->id,
            'marks' => json_encode([$subject->id => 88]),
            'comment' => '',
            'school_id' => $this->schoolId,
            'session_id' => $this->session->id,
            'timestamp' => now()->timestamp,
        ]);

        app(SchoolSubjectController::class)->destroy($subject);
        $this->assertModelExists($subject);
        $this->assertStringContainsString('gradebook marks', (string) session('error'));

        $unused = $this->createSubject($class, ['name' => 'Unused '.Str::random(5)]);
        app(SchoolSubjectController::class)->destroy($unused);
        $this->assertModelMissing($unused);
    }

    public function test_composite_subject_name_is_unique_per_class_and_session(): void
    {
        $class = $this->createClass();
        $this->createSubject($class, ['name' => 'Mathematics']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->createSubject($class, ['name' => 'mathematics']);
    }

    private function createClass(): Classes
    {
        return Classes::query()->create(['name' => 'Subject Class '.Str::random(7), 'school_id' => $this->schoolId]);
    }

    private function createSection(Classes $class): \App\Models\Section
    {
        return \App\Models\Section::query()->create(['name' => 'A', 'class_id' => $class->id, 'school_id' => $this->schoolId]);
    }

    private function createSubject(?Classes $class = null, array $attributes = []): Subject
    {
        $class ??= $this->createClass();

        return Subject::query()->create(array_merge([
            'name' => 'Subject '.Str::random(8),
            'class_id' => $class->id,
            'school_id' => $this->schoolId,
            'session_id' => $this->session->id,
        ], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Subject Test User',
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

    private function makeUpdateRequest(Subject $subject, array $data): UpdateSchoolSubjectRequest
    {
        $request = UpdateSchoolSubjectRequest::create('/admin/subjects/'.$subject->id, 'PATCH', $data);
        $route = new RoutingRoute(['PATCH'], 'admin/subjects/{subject}', fn () => null);
        $route->bind($request);
        $route->setParameter('subject', $subject);
        $request->setRouteResolver(fn (): RoutingRoute => $route);
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));
        $request->setUserResolver(fn (): User => $this->primaryAdmin);

        return $request;
    }
}
