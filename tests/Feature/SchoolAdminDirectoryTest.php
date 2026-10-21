<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolAdminController;
use App\Http\Requests\StoreSchoolAdminRequest;
use App\Http\Requests\UpdateSchoolAdminStatusRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SchoolAdminDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the school admin directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = app('db')->connection();
        $this->tenantConnection->beginTransaction();
        $this->primaryAdmin = $this->createAdmin([
            'school_role' => 1,
            'name' => 'Primary Directory Admin',
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

    public function test_directory_routes_use_safe_http_methods_and_admin_middleware(): void
    {
        $expectedMethods = [
            'admin.admin' => 'GET',
            'admin.create' => 'POST',
            'admin.update' => 'PATCH',
            'admin.admin.delete' => 'DELETE',
            'admin.admin.status' => 'PATCH',
            'admin.user_password' => 'PATCH',
            'admin.admin.menu_permission_update' => 'PATCH',
        ];

        foreach ($expectedMethods as $routeName => $method) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
            $this->assertContains('auth', $route->middleware());
            $this->assertContains('admin', $route->middleware());
        }
    }

    public function test_index_filters_searches_orders_primary_first_and_preserves_query(): void
    {
        $token = Str::lower(Str::random(10));

        foreach (range(1, 11) as $number) {
            $this->createAdmin([
                'name' => "{$token} Secondary {$number}",
                'account_status' => $number === 11 ? 'disable' : 'enable',
            ]);
        }

        $request = request()->create('/admin/admin', 'GET', [
            'search' => $token,
            'status' => 'active',
        ]);
        $request->setUserResolver(fn (): User => $this->primaryAdmin);
        $view = app(SchoolAdminController::class)->index($request);
        $admins = $view->getData()['admins'];
        $summary = $view->getData()['summary'];

        $this->assertSame(10, $admins->total());
        $this->assertStringContainsString("search={$token}", $admins->url(1));
        $this->assertStringContainsString('status=active', $admins->url(1));
        $this->assertGreaterThanOrEqual(12, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);

        $unfiltered = app(SchoolAdminController::class)->index(request()->create('/admin/admin'));
        $this->assertSame(1, (int) $unfiltered->getData()['admins']->first()->school_role);
    }

    public function test_policy_restricts_mutations_to_primary_admin_and_protects_self_owner_and_other_school(): void
    {
        $secondary = $this->createAdmin();
        $nonAdmin = $this->createAdmin(['role_id' => 3]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->update($this->primaryAdmin, $secondary));
        $this->assertTrue($policy->delete($this->primaryAdmin, $secondary));
        $this->assertFalse($policy->delete($this->primaryAdmin, $this->primaryAdmin));
        $this->assertFalse($policy->updateStatus($this->primaryAdmin, $this->primaryAdmin));
        $this->assertFalse($policy->update($this->primaryAdmin, $nonAdmin));
        $this->assertFalse($policy->create($secondary));
        $this->assertFalse($policy->viewAny($secondary));

        $secondary->menu_permission = json_encode(['admin.admin']);
        $this->assertTrue($policy->viewAny($secondary));
    }

    public function test_validated_status_update_redirects_to_directory_and_invalid_status_fails(): void
    {
        $secondary = $this->createAdmin();
        $request = $this->statusRequest($secondary, 'disable');
        $response = app(SchoolAdminController::class)->updateStatus($request, $secondary);

        $this->assertSame('disable', $secondary->refresh()->account_status);
        $this->assertSame(route('admin.admin'), $response->getTargetUrl());

        $this->expectException(ValidationException::class);
        $this->statusRequest($secondary, 'archived');
    }

    public function test_store_validation_rejects_duplicate_email_and_weak_password(): void
    {
        $secondary = $this->createAdmin();
        $request = StoreSchoolAdminRequest::create('/admin/admin', 'POST', [
            'name' => 'Duplicate Admin',
            'email' => strtoupper($secondary->email),
            'password' => 'weak',
            'password_confirmation' => 'weak',
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
        }
    }

    public function test_rendered_directory_escapes_content_and_contains_desktop_and_mobile_views(): void
    {
        $token = Str::lower(Str::random(10));
        $admin = $this->createAdmin([
            'name' => "<script>{$token}</script>",
            'email' => "{$token}@example.test",
            'user_information' => json_encode([
                'phone' => null,
                'address' => str_repeat('Long address ', 25),
                'photo' => '',
            ]),
        ]);

        $view = app(SchoolAdminController::class)->index(request()->create('/admin/admin', 'GET', ['search' => $token]));
        $html = $view->render();

        $this->assertStringContainsString('school-admin-directory__desktop', $html);
        $this->assertStringContainsString('school-admin-directory__mobile', $html);
        $this->assertStringContainsString(e($admin->name), $html);
        $this->assertStringNotContainsString($admin->name, $html);
        $this->assertStringContainsString('Not provided', $html);
    }

    /** @param array<string, mixed> $attributes */
    private function createAdmin(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Secondary Admin',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 2,
            'school_id' => 1,
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

    private function statusRequest(User $admin, string $status): UpdateSchoolAdminStatusRequest
    {
        $request = UpdateSchoolAdminStatusRequest::create('/admin/admin/'.$admin->id.'/status', 'PATCH', [
            'status' => $status,
        ]);
        $route = new \Illuminate\Routing\Route('PATCH', '/admin/admin/{admin}/status', []);
        $route->bind($request);
        $route->setParameter('admin', $admin);
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
