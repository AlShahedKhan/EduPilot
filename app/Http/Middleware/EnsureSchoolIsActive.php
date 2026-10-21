<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureSchoolIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $isInactive = false;

        if (function_exists('school_status_check')) {
            $isInactive = (school_status_check(auth()->user()->email ?? '') == 0);
        } else {
            // 1. Check tenancy initialized status if using tenancy package
            if (function_exists('tenancy') && tenancy()->initialized) {
                $tenantStatus = tenancy()->tenant->status ?? null;
                if ($tenantStatus !== null && ($tenantStatus == '0' || $tenantStatus === 0)) {
                    $isInactive = true;
                }
            }

            // 2. Fallback or additional check on local schools table in tenant context
            if (!$isInactive) {
                try {
                    if (Schema::hasTable('schools')) {
                        $schoolStatus = DB::table('schools')->value('status');
                        if ($schoolStatus !== null && ($schoolStatus == '0' || $schoolStatus === 0)) {
                            $isInactive = true;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore query failure if table is not accessible
                }
            }
        }

        // If visiting school.inactive route when school is active, redirect to home
        if ($request->routeIs('school.inactive')) {
            if (!$isInactive) {
                return redirect('/');
            }
            return $next($request);
        }

        // Allow logout when inactive
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        if ($isInactive) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your school account is currently inactive. Please contact support.',
                    'status'  => 'inactive',
                ], 403);
            }

            return redirect()->route('school.inactive');
        }

        return $next($request);
    }
}
