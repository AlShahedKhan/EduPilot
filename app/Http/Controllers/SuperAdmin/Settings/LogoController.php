<?php

namespace App\Http\Controllers\SuperAdmin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoController extends Controller
{
    private string $logoDir;

    public function __construct()
    {
        $this->logoDir = public_path('assets/uploads/logo');
    }

    public function index()
    {
        $logo   = get_sa_logo();
        $favicon = get_sa_favicon();

        return view('superadmin.settings.logo', compact('logo', 'favicon'));
    }

    public function updateLogo(Request $request)
    {
        if ($request->filled('cropped_logo')) {
            $name = $request->input('cropped_logo');
            set_config('sa_logo', $name);
            return back()->with('success', get_phrase('Logo updated successfully'));
        }

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $file = $request->file('logo');
        $name = 'sa_logo_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($this->logoDir, $name);

        set_config('sa_logo', $name);

        return back()->with('success', get_phrase('Logo updated successfully'));
    }

    public function updateFavicon(Request $request)
    {
        if ($request->filled('cropped_favicon')) {
            $name = $request->input('cropped_favicon');
            set_config('sa_favicon', $name);
            return back()->with('success', get_phrase('Favicon updated successfully'));
        }

        $request->validate([
            'favicon' => 'required|image|mimes:png,ico,svg|max:1024',
        ]);

        $file = $request->file('favicon');
        $name = 'sa_favicon_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($this->logoDir, $name);

        set_config('sa_favicon', $name);

        return back()->with('success', get_phrase('Favicon updated successfully'));
    }
}
