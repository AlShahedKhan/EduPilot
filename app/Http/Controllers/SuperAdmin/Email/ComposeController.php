<?php

namespace App\Http\Controllers\SuperAdmin\Email;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\SendEmailRequest;
use Illuminate\Support\Facades\Mail;

class ComposeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index()
    {
        return view('superadmin.email_logs.compose');
    }

    public function send(SendEmailRequest $request)
    {
        $to      = $request->input('to');
        $subject = $request->input('subject');
        $body    = $request->input('body');
        $from    = get_mail_config('MAIL_FROM_ADDRESS');
        $fromName = get_mail_config('MAIL_FROM_NAME', 'School Management');

        try {
            Mail::raw($body, function ($message) use ($to, $subject, $from, $fromName) {
                $message->to($to)
                    ->subject($subject)
                    ->from($from, $fromName);
            });

            return redirect()->route('superadmin.email_logs')->with('message', 'Email sent successfully to ' . $to);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
