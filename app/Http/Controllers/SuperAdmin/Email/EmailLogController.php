<?php

namespace App\Http\Controllers\SuperAdmin\Email;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index(Request $request)
    {
        $query = EmailLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('to', 'LIKE', "%{$search}%")
                  ->orWhere('from', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->paginate(20);

        return view('superadmin.email_logs.list', compact('logs'));
    }

    public function destroy($id)
    {
        EmailLog::findOrFail($id)->delete();
        return redirect()->back()->with('message', 'Log deleted.');
    }

    public function clear()
    {
        EmailLog::truncate();
        return redirect()->back()->with('message', 'All email logs cleared.');
    }
}
