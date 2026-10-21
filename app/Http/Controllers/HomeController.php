<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function home(Request $request)
    {


        $scheme = $request->getScheme();
        $port   = in_array($request->getPort(), [80, 443]) ? '' : ':' . $request->getPort();

        $schools = Tenant::query()->with('domains')->get()->map(function ($tenant) use ($scheme, $port) {
            $domain = optional($tenant->domains->first())->domain ?? ($tenant->id . '.' . get_school_domain_base());

            return (object) [
                'id'        => $tenant->id,
                'title'     => $tenant->title ?? 'School',
                'email'     => $tenant->email ?? '',
                'phone'     => $tenant->phone ?? '',
                'address'   => $tenant->address ?? '',
                'status'    => $tenant->status ?? '1',
                'login_url' => $scheme . '://' . $domain . $port . '/login',
            ];
        });

        return view('frontend.landing_page', compact('schools'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function superadminHome()
    {
        return view('superadminHome');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    public function webRedirectToPayFee(Request $request)
    {
        // Remove the 'Basic ' prefix
        $base64Credentials = substr($request->query('auth'), 6);

        // Decode the base64-encoded string
        $credentials = base64_decode($base64Credentials);

        // Split the decoded string into email and password
        list($email, $password, $timestamp) = explode(':', $credentials);

        // Get the current timestamp
        $timestamp1 = strtotime(date('Y-m-d'));

        $difference = $timestamp1 - $timestamp;

        if ($difference < 86400) {
            if (auth()->attempt(array('email' => $email, 'password' => $password))) {
                // Authentication passed...
                return redirect()->intended('/student/fee_manager/payment/' . $request->query('fee_id'));
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Invalid email or password',
            ]);
        } else {
            return redirect()->route('login')->withErrors([
                'email' => 'Token expired!',
            ]);
        }
    }
}
