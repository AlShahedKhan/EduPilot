<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolLibrarianController;
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

class SchoolLibrarianDirectoryTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    private User $primaryAdmin;

    private int $schoolId;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the librarian directory.');
        }

        tenancy()->initialize($tenant);
        $this->tenantConnection = DB::connection();
        $this->tenantConnection->beginTransaction();
        $this->schoolId = (int) School::query()->value('id');

        if ($this->schoolId === 0) {
            $this->markTestSkipped('A school is required to test the librarian directory.');
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
        foreach (['admin.librarian' => 'GET', 'admin.librarian.create' => 'POST', 'admin.librarian.update' => 'PATCH', 'admin.librarian.delete' => 'DELETE', 'admin.librarian.status' => 'PATCH', 'admin.librarian.password' => 'PATCH', 'admin.librarian.documents.store' => 'POST', 'admin.librarian.documents.destroy' => 'DELETE'] as $name => $method) {
            $route = Route::getRoutes()->getByName($name);
            $this->assertNotNull($route);
            $this->assertContains($method, $route->methods());
        }
    }

    public function test_search_status_recent_filter_and_totals_are_independent(): void
    {
        $token = Str::lower(Str::random(10));
        $recent = $this->createLibrarian(['name' => $token.' active']);
        $recent->forceFill(['created_at' => now()->subDays(30)->startOfDay()])->saveQuietly();
        $older = $this->createLibrarian(['name' => $token.' disabled', 'account_status' => 'disable']);
        $older->forceFill(['created_at' => now()->subDays(31)])->saveQuietly();

        $view = app(SchoolLibrarianController::class)->index(request()->create('/admin/librarian', 'GET', ['search' => $token, 'status' => 'recent']));
        $librarians = $view->getData()['librarians'];
        $summary = $view->getData()['summary'];

        $this->assertSame(1, $librarians->total());
        $this->assertGreaterThanOrEqual(2, (int) $summary->total);
        $this->assertGreaterThanOrEqual(1, (int) $summary->disabled);
        $this->assertStringContainsString('status=recent', $librarians->url(1));
    }

    public function test_secondary_admin_can_view_but_only_primary_can_mutate(): void
    {
        $librarian = $this->createLibrarian();
        $secondary = $this->createUser(['role_id' => 2, 'menu_permission' => json_encode(['admin.librarian'])]);
        $policy = app(UserPolicy::class);

        $this->assertTrue($policy->viewAnyLibrarian($secondary));
        $this->assertTrue($policy->viewLibrarian($secondary, $librarian));
        $this->assertFalse($policy->updateLibrarian($secondary, $librarian));
        $this->assertTrue($policy->updateLibrarian($this->primaryAdmin, $librarian));
    }

    public function test_linked_librarian_is_not_deleted_and_unlinked_librarian_is_deleted(): void
    {
        $linked = $this->createLibrarian();
        DB::table('message_thrades')->insert(['sender_id' => $linked->id, 'school_id' => $this->schoolId, 'created_at' => now(), 'updated_at' => now()]);
        $response = app(SchoolLibrarianController::class)->destroy($linked);
        $this->assertModelExists($linked);
        $this->assertStringContainsString('cannot be deleted', (string) session('error'));
        $this->assertSame(route('admin.librarian'), $response->getTargetUrl());

        $unlinked = $this->createLibrarian();
        app(SchoolLibrarianController::class)->destroy($unlinked);
        $this->assertModelMissing($unlinked);
    }

    public function test_rendered_directory_is_responsive_and_escapes_values(): void
    {
        $token = Str::lower(Str::random(10));
        $librarian = $this->createLibrarian(['name' => "<script>{$token}</script>", 'user_information' => '{invalid']);
        $html = app(SchoolLibrarianController::class)->index(request()->create('/admin/librarian', 'GET', ['search' => $token]))->render();

        $this->assertStringContainsString('school-librarian-directory__desktop', $html);
        $this->assertStringContainsString('school-librarian-directory__mobile', $html);
        $this->assertStringContainsString(e($librarian->name), $html);
        $this->assertStringNotContainsString($librarian->name, $html);
        $this->assertStringContainsString('data-librarian-export="pdf"', $html);
    }

    private function createLibrarian(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role_id' => 5], $attributes));
    }

    private function createUser(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Directory User',
            'email' => Str::lower(Str::random(16)).'@example.test',
            'password' => bcrypt('Password123'),
            'role_id' => 5,
            'school_id' => $this->schoolId,
            'school_role' => 0,
            'account_status' => 'enable',
            'status' => 1,
            'user_information' => json_encode(['gender' => 'Male', 'blood_group' => 'o+', 'birthday' => 946684800, 'phone' => '+8801700000000', 'address' => 'Dhaka', 'photo' => '']),
        ], $attributes));
    }
}
