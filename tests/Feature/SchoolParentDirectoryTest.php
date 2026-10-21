<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolParentController;
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

class SchoolParentDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the parent directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test the parent directory.');
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
        foreach (['admin.parent' => 'GET', 'admin.parent.create' => 'POST', 'admin.parent.update' => 'PATCH', 'admin.parent.delete' => 'DELETE', 'admin.parent.status' => 'PATCH', 'admin.parent.password' => 'PATCH', 'admin.parent.documents.store' => 'POST', 'admin.parent.documents.destroy' => 'DELETE'] as $name => $method) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
        }
    }

    public function test_search_without_children_filter_and_totals_are_independent(): void
    {
        $token = Str::lower(Str::random(10));
        $withChild = $this->createParent(['name' => $token.' active']);
        $this->createUser(['name' => $token.' child', 'role_id' => 7, 'parent_id' => $withChild->id]);
        $this->createParent(['name' => $token.' disabled', 'account_status' => 'disable']);

        $view = app(SchoolParentController::class)->index(request()->create('/admin/parent', 'GET', ['search' => $token, 'status' => 'without_children']));
        $parents = $view->getData()['parents'];
        $summary = $view->getData()['summary'];

        $this->assertSame(1, $parents->total());
        $this->assertGreaterThanOrEqual(2, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);
        $this->assertGreaterThanOrEqual(1, (int) $summary->without_children);
        $this->assertStringContainsString('status=without_children', $parents->url(1));
    }

    public function test_secondary_admin_can_view_but_only_primary_can_mutate(): void
    {
        $parent = $this->createParent();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.parent'])]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewAnyParent($secondary));
        $this->assertTrue($policy->viewParent($secondary, $parent));
        $this->assertFalse($policy->updateParent($secondary, $parent));
        $this->assertTrue($policy->updateParent($this->primaryAdmin, $parent));
    }

    public function test_linked_parent_is_not_deleted_and_unlinked_parent_is_deleted(): void
    {
        $linked = $this->createParent();
        DB::table('message_thrades')->insert(['sender_id' => $linked->id, 'school_id' => $this->schoolId, 'created_at' => now(), 'updated_at' => now()]);
        $response = app(SchoolParentController::class)->destroy($linked);
        $this->assertModelExists($linked);
        $this->assertStringContainsString('cannot be deleted', (string) session('error'));
        $this->assertSame(route('admin.parent'), $response->getTargetUrl());

        $unlinked = $this->createParent();
        app(SchoolParentController::class)->destroy($unlinked);
        $this->assertModelMissing($unlinked);
    }

    public function test_rendered_directory_is_responsive_and_escapes_values(): void
    {
        $token = Str::lower(Str::random(10));
        $parent = $this->createParent(['name' => "<script>{$token}</script>", 'user_information' => '{invalid']);
        $html = app(SchoolParentController::class)->index(request()->create('/admin/parent', 'GET', ['search' => $token]))->render();

        $this->assertStringContainsString('school-parent-directory__desktop', $html);
        $this->assertStringContainsString('school-parent-directory__mobile', $html);
        $this->assertStringContainsString(e($parent->name), $html);
        $this->assertStringNotContainsString($parent->name, $html);
        $this->assertStringContainsString('data-parent-export="pdf"', $html);
    }

    private function createParent(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role_id' => 6], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Directory User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 6,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['gender' => 'Male', 'blood_group' => 'o+', 'birthday' => 946684800, 'phone' => '+8801700000000', 'address' => 'Dhaka', 'photo' => '']),
        ], $attributes));
    }
}
