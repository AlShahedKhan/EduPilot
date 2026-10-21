<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolWardenController;
use App\Models\School;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolWardenDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the warden directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test the warden directory.');
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
        foreach (['admin.warden' => 'GET', 'admin.warden.create' => 'POST', 'admin.warden.update' => 'PATCH', 'admin.warden.delete' => 'DELETE', 'admin.warden.status' => 'PATCH', 'admin.warden.password' => 'PATCH', 'admin.warden.documents.store' => 'POST', 'admin.warden.documents.destroy' => 'DELETE'] as $name => $method) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
        }
    }

    public function test_search_unassigned_filter_and_totals_are_independent(): void
    {
        $token = Str::lower(Str::random(10));
        $assigned = $this->createWarden(['name' => $token.' active']);
        DB::table('hostels')->insert(['name' => $token.' hostel', 'type' => 'Boys', 'address' => 'Dhaka', 'warden_id' => $assigned->id, 'school_id' => $this->schoolId, 'created_at' => now(), 'updated_at' => now()]);
        $this->createWarden(['name' => $token.' disabled', 'account_status' => 'disable']);

        $view = app(SchoolWardenController::class)->index(request()->create('/admin/warden', 'GET', ['search' => $token, 'status' => 'unassigned']));
        $wardens = $view->getData()['wardens'];
        $summary = $view->getData()['summary'];

        $this->assertSame(1, $wardens->total());
        $this->assertGreaterThanOrEqual(2, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);
        $this->assertGreaterThanOrEqual(1, (int) $summary->unassigned);
        $this->assertStringContainsString('status=unassigned', $wardens->url(1));
    }

    public function test_secondary_admin_can_view_but_only_primary_can_mutate(): void
    {
        $warden = $this->createWarden();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.warden'])]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewAnyWarden($secondary));
        $this->assertTrue($policy->viewWarden($secondary, $warden));
        $this->assertFalse($policy->updateWarden($secondary, $warden));
        $this->assertTrue($policy->updateWarden($this->primaryAdmin, $warden));
    }

    public function test_linked_warden_is_not_deleted_and_unlinked_warden_is_deleted(): void
    {
        $linked = $this->createWarden();
        DB::table('hostels')->insert(['name' => 'Linked Hostel', 'type' => 'Girls', 'address' => 'Dhaka', 'warden_id' => $linked->id, 'school_id' => $this->schoolId, 'created_at' => now(), 'updated_at' => now()]);
        $response = app(SchoolWardenController::class)->destroy($linked);
        $this->assertModelExists($linked);
        $this->assertStringContainsString('cannot be deleted', (string) session('error'));
        $this->assertSame(route('admin.warden'), $response->getTargetUrl());

        $unlinked = $this->createWarden();
        app(SchoolWardenController::class)->destroy($unlinked);
        $this->assertModelMissing($unlinked);
    }

    public function test_rendered_directory_is_responsive_and_escapes_values(): void
    {
        $token = Str::lower(Str::random(10));
        $warden = $this->createWarden(['name' => "<script>{$token}</script>", 'user_information' => '{invalid']);
        $html = app(SchoolWardenController::class)->index(request()->create('/admin/warden', 'GET', ['search' => $token]))->render();

        $this->assertStringContainsString('school-warden-directory__desktop', $html);
        $this->assertStringContainsString('school-warden-directory__mobile', $html);
        $this->assertStringContainsString(e($warden->name), $html);
        $this->assertStringNotContainsString($warden->name, $html);
        $this->assertStringContainsString('data-warden-export="pdf"', $html);
    }

    public function test_warden_role_repair_is_idempotent(): void
    {
        DB::table('roles')->where('role_id', 10)->delete();
        $migration = require database_path('migrations/tenant/2026_08_14_134713_add_warden_role_to_roles_table.php');
        $migration->up();
        $migration->up();

        $this->assertSame(1, DB::table('roles')->where('role_id', 10)->count());
        $this->assertSame('warden', DB::table('roles')->where('role_id', 10)->value('name'));
    }

    private function createWarden(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role_id' => 10], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Directory User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 10,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['gender' => 'Male', 'blood_group' => 'o+', 'birthday' => 946684800, 'phone' => '+8801700000000', 'address' => 'Dhaka', 'photo' => '']),
        ], $attributes));
    }
}
