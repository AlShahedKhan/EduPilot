<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return never
     */
    protected function unauthenticated($request, array $guards): never
    {
        throw new AuthenticationException(
            'Unauthenticated.',
            $guards,
            $request->expectsJson()
                ? null
                : (in_array('superadmin', $guards, true) ? route('superadmin.login') : $this->redirectTo($request)),
        );
    }
}
