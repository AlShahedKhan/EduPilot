<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolClassRoomController;
use App\Models\Classes;
use App\Models\ClassRoom;
use App\Models\Routine;
use App\Models\School;
use App\Models\Section;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\ClassRoomPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolClassRoomManagementTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test class room management.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test class room management.');
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

    public function test_routes_use_restful_methods(): void
    {
        foreach (['admin.class_room_list' => 'GET', 'admin.create.class_room' => 'POST', 'admin.class_room.update' => 'PATCH', 'admin.class_room.delete' => 'DELETE'] as $name => $method) {
            $this->assertContains($method, Route::getRoutes()->getByName($name)->methods());
        }
    }

    public function test_secondary_admin_can_view_but_only_primary_admin_can_mutate(): void
    {
        $classRoom = $this->createRoom();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.class_room_list'])]);
        $policy = app(ClassRoomPolicy::class);

        $this->assertTrue($policy->viewAny($secondary));
        $this->assertTrue($policy->view($secondary, $classRoom));
        $this->assertFalse($policy->update($secondary, $classRoom));
        $this->assertTrue($policy->update($this->primaryAdmin, $classRoom));
    }

    public function test_directory_search_usage_totals_and_responsive_markup(): void
    {
        $token = Str::lower(Str::random(10));
        $available = $this->createRoom(['name' => "<script>{$token}</script>"]);
        $used = $this->createRoom(['name' => 'Used '.$token]);
        $this->createRoutine($used);

        $view = app(SchoolClassRoomController::class)->index(request()->create('/admin/class_room', 'GET', ['search' => $token, 'usage' => 'available']));
        $html = $view->render();

        $this->assertSame(1, $view->getData()['classRooms']->total());
        $this->assertGreaterThanOrEqual(1, (int) $view->getData()['summary']->in_use);
        $this->assertStringContainsString('school-room-page__desktop', $html);
        $this->assertStringContainsString('school-room-page__mobile', $html);
        $this->assertStringContainsString(e($available->name), $html);
        $this->assertStringNotContainsString($available->name, $html);
        $this->assertStringContainsString('usage=available', $view->getData()['classRooms']->url(1));
    }

    public function test_used_room_cannot_be_deleted_and_unused_room_can_be_deleted(): void
    {
        $used = $this->createRoom();
        $this->createRoutine($used);
        app(SchoolClassRoomController::class)->destroy($used);

        $this->assertModelExists($used);
        $this->assertStringContainsString('routines', (string) session('error'));

        $unused = $this->createRoom();
        app(SchoolClassRoomController::class)->destroy($unused);
        $this->assertModelMissing($unused);
    }

    public function test_room_names_are_unique_per_school_at_the_database_layer(): void
    {
        $room = $this->createRoom();

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->createRoom(['name' => $room->name]);
    }

    private function createRoutine(ClassRoom $classRoom): Routine
    {
        $schoolClass = Classes::query()->create(['name' => 'Routine Class '.Str::random(6), 'school_id' => $this->schoolId]);
        $section = Section::query()->create(['name' => 'A', 'class_id' => $schoolClass->id, 'school_id' => $this->schoolId]);
        $session = Session::query()->create(['session_title' => 'Session '.Str::random(6), 'status' => 1, 'school_id' => $this->schoolId]);
        $subject = Subject::query()->create(['name' => 'Subject '.Str::random(6), 'class_id' => $schoolClass->id, 'school_id' => $this->schoolId, 'session_id' => $session->id]);
        $teacher = $this->createUser(['role_id' => 3]);

        return Routine::query()->create([
            'class_id' => $schoolClass->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'starting_hour' => 9,
            'starting_minute' => 0,
            'ending_hour' => 10,
            'ending_minute' => 0,
            'day' => 'monday',
            'teacher_id' => $teacher->id,
            'room_id' => $classRoom->id,
            'school_id' => $this->schoolId,
            'session_id' => $session->id,
        ]);
    }

    private function createRoom(array $attributes = []): ClassRoom
    {
        return ClassRoom::query()->create(array_merge(['name' => 'Room '.Str::random(8), 'school_id' => $this->schoolId], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Room Test User',
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
}
