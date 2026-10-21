<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index()
    {
        $total_schools = Tenant::count();

        $people = Cache::remember('superadmin_dashboard_counts', now()->addMinutes(10), function () {
            $teachers = 0;
            $students = 0;
            $admins   = 0;

            foreach (Tenant::all() as $tenant) {
                try {
                    tenancy()->initialize($tenant);
                    $teachers += User::where('role_id', 3)->count();
                    $students += User::where('role_id', 7)->count();
                    $admins   += User::where('role_id', 2)->count();
                } catch (\Throwable $e) {
                    // Tenant database unavailable — skip.
                } finally {
                    if (tenancy()->initialized) {
                        tenancy()->end();
                    }
                }
            }

            return compact('teachers', 'students', 'admins');
        });

        $recent_schools = Tenant::query()
            ->with('domains')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($tenant) => tenantSchoolMap($tenant));

        // Monthly school creation data (last 8 months)
        $monthlySchools = [];
        $monthlyLabels  = [];
        for ($i = 7; $i >= 0; $i--) {
            $date       = now()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlySchools[] = Tenant::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // School status breakdown (donut chart)
        $activeCount   = Tenant::where('data->status', '1')->count();
        $pendingCount  = Tenant::where('data->status', '2')->count();
        $inactiveCount = $total_schools - $activeCount - $pendingCount;

        // Latest school
        $latestSchool = Tenant::with('domains')->latest()->first();
        $latestSchoolMap = $latestSchool ? tenantSchoolMap($latestSchool) : null;

        // Active rate
        $activeRate = $total_schools > 0 ? round(($activeCount / $total_schools) * 100) : 0;

        return view('superadmin.dashboard', compact(
            'total_schools', 'people', 'recent_schools',
            'monthlySchools', 'monthlyLabels',
            'activeCount', 'pendingCount', 'inactiveCount',
            'latestSchoolMap', 'activeRate'
        ));
    }
}
