<?php

namespace App\Http\Controllers\SuperAdmin\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateAdminRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index($id)
    {
        $tenant = Tenant::find($id);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'No tenant found for this school.');
        }

        tenancy()->initialize($tenant);
        $admins = User::where('role_id', 2)->get();
        tenancy()->end();

        $school = tenantSchoolMap($tenant);

        return view('superadmin.school.admin_list', compact('admins', 'school'));
    }

    public function edit($tenantId, $adminId)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'No tenant found for this school.');
        }

        tenancy()->initialize($tenant);
        $admin = User::where('role_id', 2)->where('id', $adminId)->first();
        tenancy()->end();

        if (!$admin) {
            return redirect()->back()->with('warning', 'No admin found.');
        }

        $school = tenantSchoolMap($tenant);

        return view('superadmin.school.edit_admin', compact('admin', 'school'));
    }

    public function update(UpdateAdminRequest $request, $tenantId, $adminId)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'No tenant found for this school.');
        }

        tenancy()->initialize($tenant);
        $admin = User::where('role_id', 2)->where('id', $adminId)->first();

        if (!$admin) {
            tenancy()->end();
            return redirect()->back()->with('warning', 'No admin found.');
        }

        $data = $request->validated();
        $info = json_decode($admin->user_information, true) ?: [];

        if (!empty($data['photo'])) {
            $imageName = time() . '.' . $data['photo']->extension();
            $data['photo']->move(public_path('assets/uploads/user-images/'), $imageName);
            $info['photo'] = $imageName;
        }

        $info['gender']      = $data['gender'] ?? ($info['gender'] ?? '');
        $info['blood_group'] = $data['blood_group'] ?? ($info['blood_group'] ?? '');
        $info['birthday']    = isset($data['birthday']) && $data['birthday'] !== '' ? strtotime($data['birthday']) : ($info['birthday'] ?? '');
        $info['phone']       = $data['phone'] ?? ($info['phone'] ?? '');
        $info['address']     = $data['address'] ?? ($info['address'] ?? '');

        $admin->name  = $data['name'];
        $admin->email = $data['email'];

        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }

        $admin->user_information = json_encode($info);
        $admin->save();
        tenancy()->end();

        return redirect()->back()->with('message', 'Admin updated successfully.');
    }

    public function password(Request $request)
    {
        $userId   = $request->input('user_id');
        $tenantId = $request->input('tenant_id');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                tenancy()->initialize($tenant);
                User::where('id', $userId)->update([
                    'password' => Hash::make($request->password),
                ]);
                tenancy()->end();
                return redirect()->back()->with('message', 'You have successfully updated the password.');
            }
        }

        return redirect()->back()->with('warning', 'Could not update password.');
    }

    public function statusUpdate($tenantId, $adminId, $action)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'No tenant found for this school.');
        }

        tenancy()->initialize($tenant);
        $admin = User::where('role_id', 2)->where('id', $adminId)->first();

        if (!$admin) {
            tenancy()->end();
            return redirect()->back()->with('warning', 'No admin found.');
        }

        $disable = ($action === 'disable');
        $admin->account_status = $disable ? 'disable' : 'enable';
        $admin->status = $disable ? 0 : 1;
        $admin->save();
        tenancy()->end();

        return redirect()->back()->with('message', $disable ? 'Admin disabled successfully.' : 'Admin enabled successfully.');
    }

    public function loginAs($tenantId, $adminId)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return redirect()->back()->with('warning', 'No tenant found for this school.');
        }

        tenancy()->initialize($tenant);

        $admin = User::where('role_id', 2)->where('id', $adminId)->first();

        if (!$admin) {
            tenancy()->end();
            return redirect()->back()->with('warning', 'No admin found for this school.');
        }

        $token = bin2hex(random_bytes(32));

        DB::table('global_settings')->updateOrInsert(
            ['key' => 'login_as_' . $token],
            [
                'value'      => json_encode(['admin_id' => $admin->id, 'expires' => time() + 300]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $domain = optional($tenant->domains->first())->domain ?? ($tenantId . '.' . get_school_domain_base());

        tenancy()->end();

        $root   = parse_url(rtrim(request()->root(), '/'));
        $scheme = $root['scheme'] ?? 'http';
        $host   = $root['host'] ?? '';
        $port   = isset($root['port']) ? ':' . $root['port'] : '';

        $url = $scheme . '://' . $domain . $port . '/login-as/' . $token;

        // Notify admin via email
        try {
            \Illuminate\Support\Facades\Mail::to($admin->email)->send(
                new \App\Mail\AdminInviteEmail(
                    adminName: $admin->name,
                    schoolName: $tenant->title ?? 'Your School',
                    loginUrl: $url,
                )
            );
        } catch (\Throwable $e) {
            // Email failure shouldn't block login-as
        }

        return redirect()->away($url);
    }
}
