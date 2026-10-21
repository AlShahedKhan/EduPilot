<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\ContactController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\Email\ComposeController;
use App\Http\Controllers\SuperAdmin\Email\EmailLogController;
use App\Http\Controllers\SuperAdmin\ProfileController;
use App\Http\Controllers\SuperAdmin\School\SchoolAdminController;
use App\Http\Controllers\SuperAdmin\School\SchoolController;
use App\Http\Controllers\SuperAdmin\Settings\SmtpController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\SuperAdmin\Auth\LoginController as SuperAdminLoginController;
use App\Http\Controllers\Updater;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Loop over central domains and wrap everything inside a single middleware stack
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->middleware(['web', 'throttle:30,1'])->group(function () {

        // Route::get('/', function () {
        //     return redirect()->route('landingPage');
        // });
        // Clear application cache: (superadmin only)
        Route::get('/clear-cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');

            return 'Cache cleared';
        })->name('clear.cache')->middleware('auth:superadmin', 'superAdmin');

        // Landing page routes are here
        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'home')->name('landingPage');
            Route::get('web_redirect_to_pay_fee', 'webRedirectToPayFee')->name('webRedirectToPayFee');
        });

        // Contact form submission (public, no auth)
        Route::post('contact/store', [ContactController::class, 'store'])->name('contact.store');

        // Central superadmin auth routes are here
        Route::prefix('central')->group(function () {
            Route::get('login', [SuperAdminLoginController::class, 'showLoginForm'])->name('superadmin.login');
            Route::post('login', [SuperAdminLoginController::class, 'login']);
            Route::post('logout', [SuperAdminLoginController::class, 'logout'])->name('superadmin.logout');
        });

        // ─── Superadmin routes ──────────────────────────────────────────
        Route::prefix('central')->middleware('auth:superadmin', 'superAdmin')->group(function () {

            // Dashboard
            Route::get('dashboard', [DashboardController::class, 'index'])->name('superadmin.dashboard');

            // School routes
            Route::controller(SchoolController::class)->prefix('school')->group(function () {
                Route::get('list', 'index')->name('superadmin.school.list');
                Route::get('add', 'add')->name('superadmin.school.add');
                Route::post('create', 'create')->name('superadmin.school.create');
                Route::get('edit/{id}', 'edit')->name('superadmin.edit.school');
                Route::post('update/{id}', 'update')->name('superadmin.school.update');
                Route::get('status_update/{id}/{status}', 'statusUpdate')->name('superadmin.school.status_update');
                Route::post('delete/{id}', 'delete')->name('superadmin.school.delete');
            });

            // School Admin routes
            Route::controller(SchoolAdminController::class)->prefix('school')->group(function () {
                Route::get('admin_list/{id}', 'index')->name('superadmin.school.admin_list');
                Route::get('admin/edit/{tenant_id}/{admin_id}', 'edit')->name('superadmin.edit.admin');
                Route::post('admin/update/{tenant_id}/{admin_id}', 'update')->name('superadmin.update.admin');
                Route::get('admin/status_update/{tenant_id}/{admin_id}/{action}', 'statusUpdate')->name('superadmin.school.admin_status_update');
                Route::get('login_as/{tenant_id}/{admin_id}', 'loginAs')->name('superadmin.school.login_as');
            });

            // Settings — SMTP
            Route::controller(SmtpController::class)->prefix('settings')->group(function () {
                Route::get('smtp', 'index')->name('superadmin.smtp_settings');
            });
            Route::post('smtp/update', [SmtpController::class, 'update'])->name('superadmin.smtp.update');
            Route::post('smtp/test', [SmtpController::class, 'test'])->name('superadmin.smtp.test');

            // Settings — Logo & Favicon
            Route::get('settings/logo', [\App\Http\Controllers\SuperAdmin\Settings\LogoController::class, 'index'])->name('superadmin.settings.logo');
            Route::put('settings/logo', [\App\Http\Controllers\SuperAdmin\Settings\LogoController::class, 'updateLogo'])->name('superadmin.settings.logo.update');
            Route::put('settings/favicon', [\App\Http\Controllers\SuperAdmin\Settings\LogoController::class, 'updateFavicon'])->name('superadmin.settings.favicon.update');

            // Image crop route
            Route::post('crop-image', [CropController::class, 'crop'])->name('superadmin.crop.image');

            // Profile
            Route::get('profile', [ProfileController::class, 'index'])->name('superadmin.profile');
            Route::post('profile/update', [ProfileController::class, 'update'])->name('superadmin.profile.update');
            Route::any('password/{action_type}', [ProfileController::class, 'password'])->name('superadmin.password');
            Route::post('admin_password/', [SchoolAdminController::class, 'password'])->name('superadmin.admin_list');

            // Contact management
            Route::get('contacts', [ContactController::class, 'index'])->name('superadmin.contacts.list');
            Route::get('contacts/{id}', [ContactController::class, 'show'])->name('superadmin.contacts.show');
            Route::delete('contacts/{id}', [ContactController::class, 'destroy'])->name('superadmin.contacts.delete');
            Route::post('contacts/{id}/status', [ContactController::class, 'updateStatus'])->name('superadmin.contacts.status');

            // Email logs
            Route::controller(EmailLogController::class)->prefix('email-logs')->group(function () {
                Route::get('/', 'index')->name('superadmin.email_logs');
                Route::delete('/{id}', 'destroy')->name('superadmin.email_logs.delete');
                Route::post('/clear', 'clear')->name('superadmin.email_logs.clear');
            });

            // Email compose
            Route::controller(ComposeController::class)->prefix('email-logs')->group(function () {
                Route::get('compose', 'index')->name('superadmin.email.compose');
                Route::post('send', 'send')->name('superadmin.email.send');
            });

            // Database Backup
            Route::controller(BackupController::class)->prefix('backup')->group(function () {
                Route::get('/', 'index')->name('superadmin.backup');
                Route::post('create', 'create')->name('superadmin.backup.create');
                Route::get('download/{filename}', 'download')->name('superadmin.backup.download');
                Route::post('email', 'email')->name('superadmin.backup.email');
                Route::delete('delete/{filename}', 'delete')->name('superadmin.backup.delete');
            });
        });
        // Superadmin routes end here

        // Updater routes are here
        Route::controller(Updater::class)->prefix('central')->middleware('superAdmin', 'auth:superadmin')->group(function () {
            Route::post('product/update', 'update')->name('superadmin.product.update');
        });
        // Updater routes end here
    }); // Closes Route::domain group
} // Closes foreach loop
