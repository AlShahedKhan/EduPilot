<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolContactController;
use App\Http\Requests\UpdateContactStatusRequest;
use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SchoolContactInboxTest extends TestCase
{
    private string $centralDatabaseName;

    private ConnectionInterface $tenantConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $centralConnection = app('db')->connection(config('tenancy.database.central_connection'));
        $this->centralDatabaseName = $centralConnection->getDatabaseName();
        $tenant = Tenant::query()->first();

        if ($tenant === null) {
            $this->markTestSkipped('A tenant is required to test the school contact inbox.');
        }

        tenancy()->initialize($tenant);

        if (!Schema::hasTable('contacts')) {
            $this->markTestSkipped('Run php artisan tenants:migrate before testing the contact inbox.');
        }

        $this->tenantConnection = app('db')->connection();
        $this->tenantConnection->beginTransaction();
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

    public function test_contact_routes_require_authenticated_school_admins(): void
    {
        foreach (['admin.contacts.index', 'admin.contacts.show', 'admin.contacts.status'] as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertContains('auth', $route->middleware());
            $this->assertContains('admin', $route->middleware());
        }
    }

    public function test_index_searches_filters_and_preserves_pagination_query(): void
    {
        $token = Str::lower(Str::random(12));

        foreach (range(1, 21) as $number) {
            $this->createContact([
                'name' => "{$token} sender {$number}",
                'status' => 'new',
            ]);
        }

        $this->createContact([
            'name' => "{$token} replied sender",
            'status' => 'replied',
        ]);

        $response = app(SchoolContactController::class)->index(Request::create(
            '/admin/contacts',
            'GET',
            ['search' => $token, 'status' => 'new'],
        ));

        $contacts = $response->getData()['contacts'];
        $statusCounts = $response->getData()['statusCounts'];

        $this->assertSame(21, $contacts->total());
        $this->assertCount(20, $contacts->items());
        $this->assertStringContainsString("search={$token}", $contacts->nextPageUrl());
        $this->assertStringContainsString('status=new', $contacts->nextPageUrl());
        $this->assertGreaterThanOrEqual(21, (int) $statusCounts->get('new'));
        $this->assertGreaterThanOrEqual(1, (int) $statusCounts->get('replied'));
    }

    public function test_show_marks_new_messages_as_read_without_downgrading_replied_messages(): void
    {
        $newContact = $this->createContact(['status' => 'new']);
        $repliedContact = $this->createContact(['status' => 'replied']);
        $controller = app(SchoolContactController::class);

        $controller->show($newContact);
        $controller->show($repliedContact);

        $this->assertSame('read', $newContact->refresh()->status);
        $this->assertSame('replied', $repliedContact->refresh()->status);
    }

    public function test_status_update_is_validated_and_redirects_to_the_inbox(): void
    {
        $contact = $this->createContact(['status' => 'read']);
        $request = $this->statusRequest('replied');

        $response = app(SchoolContactController::class)->updateStatus($request, $contact);

        $this->assertSame('replied', $contact->refresh()->status);
        $this->assertSame(route('admin.contacts.index'), $response->getTargetUrl());
        $this->assertSame('Message status updated.', session('success'));
    }

    public function test_invalid_status_is_rejected(): void
    {
        $request = $this->statusRequest('archived', false);

        $this->expectException(ValidationException::class);

        $request->validateResolved();
    }

    public function test_rendered_inbox_escapes_sender_content_and_contains_responsive_views(): void
    {
        $token = Str::lower(Str::random(12));
        $contact = $this->createContact([
            'name' => "<script>{$token}</script>",
            'email' => "{$token}@example.test",
            'phone' => null,
            'message' => str_repeat('A long responsive message ', 20),
        ]);

        $admin = User::query()->where('role_id', 2)->first();

        if ($admin === null) {
            $this->markTestSkipped('A school admin is required to render the admin navigation.');
        }

        Auth::login($admin);

        $view = app(SchoolContactController::class)->index(Request::create(
            '/admin/contacts',
            'GET',
            ['search' => $token],
        ));
        $html = $view->render();

        $this->assertStringContainsString('contact-inbox__desktop-list', $html);
        $this->assertStringContainsString('contact-inbox__mobile-list', $html);
        $this->assertStringContainsString(e($contact->name), $html);
        $this->assertStringNotContainsString($contact->name, $html);
        $this->assertStringContainsString('Not provided', $html);
    }

    public function test_contacts_use_the_tenant_database_connection(): void
    {
        $this->assertNotSame(
            $this->centralDatabaseName,
            Contact::query()->getConnection()->getDatabaseName(),
        );
    }

    /**
     * @param  array<string, string|null>  $attributes
     */
    private function createContact(array $attributes = []): Contact
    {
        return Contact::query()->create(array_merge([
            'name' => 'Website Visitor',
            'email' => Str::lower(Str::random(12)).'@example.test',
            'phone' => '+8801700000000',
            'school' => 'Tenant School',
            'message' => 'I would like more information about the school.',
            'status' => 'new',
        ], $attributes));
    }

    private function statusRequest(string $status, bool $validate = true): UpdateContactStatusRequest
    {
        $request = UpdateContactStatusRequest::create('/admin/contacts/1/status', 'POST', [
            'status' => $status,
        ]);
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));

        if ($validate) {
            $request->validateResolved();
        }

        $this->assertInstanceOf(ValidatesWhenResolved::class, $request);

        return $request;
    }
}
