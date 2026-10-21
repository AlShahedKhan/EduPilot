<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Paginator::useBootstrap();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(6)->by(($request->user() ? $request->user()->id : $request->ip()) . '|' . $request->input('email'));
        });
        RateLimiter::for('password_request', function (Request $request) {
            return Limit::perMinute(5)->by(($request->user() ? $request->user()->id : $request->ip()) . '|' . $request->input('email'));
        });
    }
}
