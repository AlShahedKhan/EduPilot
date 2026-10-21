<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'school' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'school' => $request->school,
            'message' => $request->message,
        ]);

        return redirect()->route('landingPage')->with('success', 'Thank you! We will contact you soon.');
    }

    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('school', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['new', 'read', 'replied'])) {
            $query->where('status', $request->status);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('superadmin.contacts.list', compact('contacts'));
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['status' => 'read']);
        return view('superadmin.contacts.show', compact('contact'));
    }

    public function destroy($id)
    {
        Contact::findOrFail($id)->delete();
        return redirect()->route('superadmin.contacts.list')->with('message', 'Contact deleted.');
    }

    public function updateStatus(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['status' => $request->status]);
        return back()->with('message', 'Status updated.');
    }
}
