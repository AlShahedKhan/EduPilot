<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index()
    {
        return view('superadmin.profile.view');
    }

    public function update(Request $request)
    {
        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'designation' => $request->designation,
        ];

        $userInfo = [
            'birthday' => strtotime($request->eDefaultDateRange),
            'gender'   => $request->gender,
            'phone'    => $request->phone,
            'address'  => $request->address,
        ];

        if (empty($request->photo)) {
            $userInfo['photo'] = $request->old_photo;
        } else {
            $fileName = random(10) . '.png';
            $userInfo['photo'] = $fileName;
            $request->photo->move(public_path('assets/uploads/user-images/'), $fileName);
        }

        $data['user_information'] = json_encode($userInfo);

        SuperAdmin::where('id', Auth::guard('superadmin')->user()->id)->update($data);

        return redirect(route('superadmin.profile'))->with('message', get_phrase('Profile info updated successfully'));
    }

    public function password(Request $request, $actionType = null)
    {
        if ($actionType == 'update') {
            if ($request->new_password != $request->confirm_password) {
                return back()->with('error', "Confirm Password Doesn't match!");
            }

            if (!Hash::check($request->old_password, Auth::guard('superadmin')->user()->password)) {
                return back()->with('error', "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            SuperAdmin::where('id', Auth::guard('superadmin')->user()->id)->update($data);

            return redirect(route('superadmin.password', 'edit'))->with('message', get_phrase('Password changed successfully'));
        }

        return view('superadmin.profile.password');
    }
}
