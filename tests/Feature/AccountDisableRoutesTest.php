<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccountDisableRoutesTest extends TestCase
{
    public function test_account_disabled_routes_require_authentication(): void
    {
        $routeNames = [
            'admin.account_disableview',
            'accountant.account_disable',
            'teacher.account_disable',
            'librarian.account_disable',
            'parent.account_disable',
            'student.account_disable',
        ];

        foreach ($routeNames as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route);
            $this->assertContains('auth', $route->middleware());
        }
    }
}
