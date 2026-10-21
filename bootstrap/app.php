<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {


        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'role_id' => \App\Http\Middleware\RoleId::class,
            'superAdmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'parent' => \App\Http\Middleware\ParentMiddleware::class,
            'accountant' => \App\Http\Middleware\AccountantMiddleware::class,
            'librarian' => \App\Http\Middleware\LibrarianMiddleware::class,
            'teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'warden' => \App\Http\Middleware\WardenMiddleware::class,
            'alumni' => \App\Http\Middleware\AlumniMiddleware::class,
            'admin_permission' => \App\Http\Middleware\AdminPermission::class,
            'school_active' => \App\Http\Middleware\EnsureSchoolIsActive::class,
        ]);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException $e) {
            return response()->view('errors.tenant-not-found', [], 404);
        });
    })
    ->create();
