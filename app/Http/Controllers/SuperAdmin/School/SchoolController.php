<?php

namespace App\Http\Controllers\SuperAdmin\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\CreateSchoolRequest;
use App\Http\Requests\SuperAdmin\UpdateSchoolRequest;
use App\Models\School;
use App\Models\Session;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Database\Models\Domain;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $query = Tenant::query()->with('domains');

        if ($search !== '') {
            $query->where('data->title', 'LIKE', "%{$search}%");
        }

        $schools = $query->paginate(10)->through(fn($tenant) => tenantSchoolMap($tenant));

        return view('superadmin.school.list', compact('schools', 'search'));
    }

    public function add()
    {
        return view('superadmin.school.add_school');
    }

    public function create(CreateSchoolRequest $request)
    {
        $data = $request->validated();
        $school_email = $data['school_email'];
        $admin_email = $data['admin_email'];
        $school_url = trim($data['school_url'] ?? '');

        $slug = make_tenant_id($school_url);
        $domain = $slug . '.' . get_school_domain_base();

        if (Tenant::where('id', $slug)->exists()) {
            return redirect()->back()->withInput()->with('warning', 'This school URL / domain already exists.');
        }

        try {
            $tenant = Tenant::create([
                'id' => $slug,
                'title' => $data['school_name'],
                'email' => $school_email,
                'phone' => $data['school_phone'],
                'address' => $data['school_address'],
                'school_info' => $data['school_info'],
                'admin_name' => $data['admin_name'],
                'admin_email' => $admin_email,
                'admin_address' => $data['admin_address'] ?? '',
                'admin_phone' => $data['admin_phone'] ?? '',
                'status' => '1',
                'running_session' => '',
            ]);

            Domain::create([
                'tenant_id' => $slug,
                'domain' => $domain,
                'btcl_email' => $data['btcl_email'] ?? '',
                'btcl_phone' => $data['btcl_phone'] ?? '',
                'btcl_password' => $data['btcl_password'] ?? '',
            ]);

            tenancy()->initialize($tenant);

            $photo = '';
            if (!empty($data['photo'])) {
                $imageName = time() . '.' . $data['photo']->extension();
                $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
                $photo = $imageName;
            }

            $info = [
                'gender' => $data['gender'] ?? '',
                'blood_group' => $data['blood_group'] ?? '',
                'birthday' => isset($data['birthday']) ? strtotime($data['birthday']) : time(),
                'phone' => $data['admin_phone'] ?? '',
                'address' => $data['admin_address'] ?? '',
                'photo' => $photo,
            ];

            $tenantSchool = School::create([
                'title' => $data['school_name'],
                'email' => $school_email,
                'phone' => $data['school_phone'],
                'address' => $data['school_address'],
                'school_info' => $data['school_info'],
                'status' => '1',
            ]);

            if ($request->hasFile('school_logo')) {
                $ext = $request->school_logo->getClientOriginalExtension();
                $newFileName = time() . '.' . $ext;
                $request->school_logo->move(public_path('assets/uploads/school_logo'), $newFileName);
                $tenantSchool->school_logo = $newFileName;
                $tenantSchool->save();
            }

            $session = Session::create([
                'session_title' => date('Y'),
                'school_id' => $tenantSchool->id,
                'status' => '1',
            ]);

            $tenantSchool->running_session = $session->id;
            $tenantSchool->save();

            if (Schema::hasTable('global_settings')) {
                DB::table('global_settings')->where('key', 'running_session')->update(['value' => $session->id]);
            }

            User::create([
                'name' => $data['admin_name'],
                'email' => $admin_email,
                'password' => Hash::make($data['admin_password']),
                'role_id' => '2',
                'school_id' => $tenantSchool->id,
                'user_information' => json_encode($info),
                'status' => 1,
                'school_role' => 1,
            ]);

            if (Schema::hasTable('global_settings')) {
                $branding = [
                    'system_name' => $data['school_name'],
                    'system_title' => $data['school_name'],
                    'navbar_title' => $data['school_name'],
                    'system_email' => $school_email,
                    'copyright_text' => date('Y') . ' ' . $data['school_name'] . '. All rights reserved',
                    'footer_text' => 'Team EduPilot',
                    'footer_link' => '#',
                    'contact_email' => $school_email,
                    'banner_title' => 'Welcome to ' . $data['school_name'],
                    'banner_subtitle' => 'Empowering and inspiring every student to excel',
                    'frontend_footer_text' => $data['school_name'] . ' is a modern school management platform.',
                    'features_subtitle' => 'Everything ' . $data['school_name'] . ' needs to run efficiently',
                ];

                foreach ($branding as $key => $value) {
                    DB::table('global_settings')->where('key', $key)->update(['value' => $value]);
                }
            }

            tenancy()->end();

            Tenant::find($slug)->update(['running_session' => $session->id]);

            // Send welcome email to admin
            try {
                $root = parse_url(rtrim(request()->root(), '/'));
                $scheme = $root['scheme'] ?? 'http';
                $port = isset($root['port']) ? ':' . $root['port'] : '';

                \Illuminate\Support\Facades\Mail::to($admin_email)->send(
                    new \App\Mail\SchoolCreatedEmail(
                        schoolName: $data['school_name'],
                        adminName: $data['admin_name'],
                        adminEmail: $admin_email,
                        adminPassword: $data['admin_password'],
                        loginUrl: $scheme . '://' . $domain . $port . '/login',
                    )
                );
            } catch (\Throwable $e) {
                // Email failure shouldn't block school creation
            }

            return redirect()->back()->with('message', 'School created successfully at ' . $domain);
        } catch (\Throwable $e) {
            tenancy()->end();
            try {
                if (isset($tenant)) {
                    $tenant->delete();
                    Domain::where('tenant_id', $slug)->delete();
                }
            } catch (\Throwable $rollback) {
            }

            return redirect()->back()->withInput()->with('error', 'Failed to create school: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return redirect()->back()->with('warning', 'School not found.');
        }

        $school = tenantSchoolMap($tenant);

        $domain = Domain::where('tenant_id', $id)->first();
        $school->btcl_email = $domain->btcl_email ?? '';
        $school->btcl_phone = $domain->btcl_phone ?? '';
        $school->btcl_password = $domain->btcl_password ?? '';

        tenancy()->initialize($tenant);
        $schoolRecord = School::first();
        $school->school_logo = $schoolRecord->school_logo ?? '';
        tenancy()->end();

        return view('superadmin.school.edit', ['school' => $school]);
    }

    public function update(UpdateSchoolRequest $request, $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return redirect()->back()->with('warning', 'School not found.');
        }

        $data = $request->validated();
        $oldAdminEmail = $tenant->admin_email;

        $tenant->update([
            'title' => $data['title'] ?? $tenant->title,
            'email' => $data['email'] ?? $tenant->email,
            'phone' => $data['phone'] ?? $tenant->phone,
            'address' => $data['address'] ?? $tenant->address,
            'school_info' => $data['school_info'] ?? $tenant->school_info,
            'admin_name' => $data['admin_name'] ?? $tenant->admin_name,
            'admin_email' => $data['admin_email'] ?? $tenant->admin_email,
            'admin_address' => $data['admin_address'] ?? $tenant->admin_address,
            'admin_phone' => $data['admin_phone'] ?? $tenant->admin_phone,
        ]);

        $domain = Domain::where('tenant_id', $id)->first();
        if ($domain) {
            $domain->update([
                'btcl_email' => $data['btcl_email'] ?? '',
                'btcl_phone' => $data['btcl_phone'] ?? '',
                'btcl_password' => $data['btcl_password'] ?? '',
            ]);
        }

        tenancy()->initialize($tenant);

        $tenantSchool = School::first();
        if ($tenantSchool) {
            $tenantSchool->title = $data['title'] ?? $tenantSchool->title;
            $tenantSchool->email = $data['email'] ?? $tenantSchool->email;
            $tenantSchool->phone = $data['phone'] ?? $tenantSchool->phone;
            $tenantSchool->address = $data['address'] ?? $tenantSchool->address;
            $tenantSchool->school_info = $data['school_info'] ?? $tenantSchool->school_info;

            if ($request->hasFile('school_logo')) {
                $ext = $request->school_logo->getClientOriginalExtension();
                $newFileName = time() . '.' . $ext;
                $request->school_logo->move(public_path('assets/uploads/school_logo'), $newFileName);
                $tenantSchool->school_logo = $newFileName;
            }

            $tenantSchool->save();
        }

        $adminUser = User::where('role_id', 2)->where('email', $oldAdminEmail)->first();
        if ($adminUser) {
            $adminUser->name = $data['admin_name'] ?? $adminUser->name;
            $adminUser->email = $data['admin_email'] ?? $adminUser->email;

            $info = json_decode($adminUser->user_information ?? '{}', true) ?: [];
            $info['phone'] = $data['admin_phone'] ?? ($info['phone'] ?? '');
            $info['address'] = $data['admin_address'] ?? ($info['address'] ?? '');
            $adminUser->user_information = json_encode($info);

            $adminUser->save();
        }

        tenancy()->end();

        return redirect()->back()->with('message', 'You have successfully updated the school.');
    }

    public function statusUpdate($id = '', $status = '')
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'School not found.');
        }

        $tenant->update(['status' => $status]);

        tenancy()->initialize($tenant);
        DB::table('schools')->update(['status' => $status]);
        tenancy()->end();

        // Send status notification email
        try {
            $adminEmail = $tenant->admin_email;
            $adminName = $tenant->admin_name;
            $schoolName = $tenant->title;

            if ($adminEmail) {
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                    new \App\Mail\SchoolStatusEmail(
                        adminName: $adminName,
                        schoolName: $schoolName,
                        status: $status,
                    )
                );
            }
        } catch (\Throwable $e) {
            // Email failure shouldn't block status update
        }

        return redirect()->back()->with('message', 'School status updated successfully.');
    }

    public function delete(Request $request, $id)
    {
        $request->validate(['password' => 'required|string']);

        $superadmin = \Illuminate\Support\Facades\Auth::guard('superadmin')->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $superadmin->password)) {
            return redirect()->back()->with('error', 'Incorrect super admin password. School was not deleted.');
        }

        $tenant = Tenant::find($id);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'School not found.');
        }

        try {
            $tenant->delete();
            Domain::where('tenant_id', $id)->delete();

            return redirect()->back()->with('message', 'School deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete school: ' . $e->getMessage());
        }
    }
}
