<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolAccountantController;
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

class SchoolAccountantDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the accountant directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test the accountant directory.');
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
        foreach (['admin.accountant' => 'GET', 'admin.accountant.create' => 'POST', 'admin.accountant.update' => 'PATCH', 'admin.accountant.delete' => 'DELETE', 'admin.accountant.status' => 'PATCH', 'admin.accountant.password' => 'PATCH', 'admin.accountant.documents.store' => 'POST', 'admin.accountant.documents.destroy' => 'DELETE'] as $name => $method) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
        }
    }

    public function test_search_status_recent_filter_and_totals_are_independent(): void
    {
        $token = Str::lower(Str::random(10));
        $recent = $this->createAccountant(['name' => $token.' active']);
        $recent->forceFill(['created_at' => now()->subDays(30)->startOfDay()])->saveQuietly();
        $older = $this->createAccountant(['name' => $token.' disabled', 'account_status' => 'disable']);
        $older->forceFill(['created_at' => now()->subDays(31)])->saveQuietly();

        $view = app(SchoolAccountantController::class)->index(request()->create('/admin/accountant', 'GET', ['search' => $token, 'status' => 'recent']));
        $accountants = $view->getData()['accountants'];
        $summary = $view->getData()['summary'];

        $this->assertSame(1, $accountants->total());
        $this->assertGreaterThanOrEqual(2, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);
        $this->assertStringContainsString('status=recent', $accountants->url(1));
    }

    public function test_secondary_admin_can_view_but_only_primary_can_mutate(): void
    {
        $accountant = $this->createAccountant();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.accountant'])]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewAnyAccountant($secondary));
        $this->assertTrue($policy->viewAccountant($secondary, $accountant));
        $this->assertFalse($policy->updateAccountant($secondary, $accountant));
        $this->assertTrue($policy->updateAccountant($this->primaryAdmin, $accountant));
    }

    public function test_linked_accountant_is_not_deleted_and_unlinked_accountant_is_deleted(): void
    {
        $linked = $this->createAccountant();
        DB::table('message_thrades')->insert(['sender_id' => $linked->id, 'school_id' => $this->schoolId, 'created_at' => now(), 'updated_at' => now()]);
        $response = app(SchoolAccountantController::class)->destroy($linked);
        $this->assertModelExists($linked);
        $this->assertStringContainsString('cannot be deleted', (string) session('error'));
        $this->assertSame(route('admin.accountant'), $response->getTargetUrl());

        $unlinked = $this->createAccountant();
        app(SchoolAccountantController::class)->destroy($unlinked);
        $this->assertModelMissing($unlinked);
    }

    public function test_rendered_directory_is_responsive_and_escapes_values(): void
    {
        $token = Str::lower(Str::random(10));
        $accountant = $this->createAccountant(['name' => "<script>{$token}</script>", 'user_information' => '{invalid']);
        $html = app(SchoolAccountantController::class)->index(request()->create('/admin/accountant', 'GET', ['search' => $token]))->render();

        $this->assertStringContainsString('school-accountant-directory__desktop', $html);
        $this->assertStringContainsString('school-accountant-directory__mobile', $html);
        $this->assertStringContainsString(e($accountant->name), $html);
        $this->assertStringNotContainsString($accountant->name, $html);
        $this->assertStringContainsString('data-accountant-export="pdf"', $html);
    }

    private function createAccountant(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role_id' => 4], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Directory User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 4,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['gender' => 'Male', 'blood_group' => 'o+', 'birthday' => 946684800, 'phone' => '+8801700000000', 'address' => 'Dhaka', 'photo' => '']),
        ], $attributes));
    }
}
