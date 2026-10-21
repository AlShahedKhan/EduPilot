<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateContactStatusRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolContactController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        if (!in_array($status, ['new', 'read', 'replied'], true)) {
            $status = '';
        }

        $contacts = Contact::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        $statusCounts = Contact::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.contacts.index', [
            'contacts' => $contacts,
            'search' => $search,
            'status' => $status,
            'statusCounts' => $statusCounts,
            'totalContacts' => $statusCounts->sum(),
        ]);
    }

    public function show(Contact $contact): View
    {
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function updateStatus(UpdateContactStatusRequest $request, Contact $contact): RedirectResponse
    {
        $contact->update($request->validated());

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Message status updated.');
    }
}
