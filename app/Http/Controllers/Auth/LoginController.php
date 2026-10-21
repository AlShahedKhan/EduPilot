<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $school = School::first();
        return view('auth.login', ['school' => $school]);
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (school_status_check($input['email']) == 1) {

            if (auth()->attempt(['email' => $input['email'], 'password' => $input['password']], $request->has('remember'))) {
                if (auth()->user()->role_id == 2) {

                    session(['admin_login' => 2]);
                    return redirect()->route('admin.dashboard');
                } else if (auth()->user()->role_id == 3) {

                    session(['teacher_login' => 3]);
                    return redirect()->route('teacher.dashboard');
                } else if (auth()->user()->role_id == 4) {

                    session(['accountant_login' => 4]);
                    return redirect()->route('accountant.dashboard');
                } elseif ((auth()->user()->role_id == 5)) {

                    session(['librarian_login' => 5]);
                    return redirect()->route('librarian.dashboard');
                } elseif ((auth()->user()->role_id == 6)) {

                    session(['parent_login' => 6]);
                    return redirect()->route('parent.dashboard');
                } else if (auth()->user()->role_id == 7) {

                    session(['student_login' => 7]);
                    return redirect()->route('student.dashboard');
                } else if (auth()->user()->role_id == 8) {

                    session(['driver_login' => 8]);
                    return redirect()->route('driver.dashboard');
                } else if (auth()->user()->role_id == 9) {

                    session(['alumni_login' => 9]);
                    return redirect()->route('alumni.dashboard');
                } else if (auth()->user()->role_id == 10) {
                    session(['warden_login' => 10]);
                    return redirect()->route('warden.dashboard');
                } else {
                    return redirect()->route('landingPage');
                }
            } else {
                return redirect()->route('login')
                    ->with('error', 'Email-Address And Password Are Wrong.');
            }
        } else {
            return redirect()->route('school.inactive')
                ->with('error', 'Your school account is currently inactive. Please contact support.');
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Log into a tenant as one of its admins using a one-time token
     * created by the super admin ("Login as Admin" impersonation).
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(Request $request, $token = '')
    {
        if ($token == '') {
            return redirect()->route('login');
        }

        $row = DB::table('global_settings')->where('key', 'login_as_' . $token)->first();

        if (!$row) {
            return redirect()->route('login')->with('error', 'Invalid or expired login link.');
        }

        $data = json_decode($row->value, true);
        $adminId = $data['admin_id'] ?? null;
        $expires = $data['expires'] ?? 0;

        // Tokens are single-use: remove the row before doing anything else.
        DB::table('global_settings')->where('key', 'login_as_' . $token)->delete();

        if (!$adminId || time() > $expires) {
            return redirect()->route('login')->with('error', 'Invalid or expired login link.');
        }

        $admin = \App\Models\User::where('role_id', 2)->where('id', $adminId)->first();

        if (!$admin) {
            return redirect()->route('login')->with('error', 'Admin account not found.');
        }

        auth()->login($admin);
        session(['admin_login' => 2]);

        return redirect()->route('admin.dashboard');
    }
}
