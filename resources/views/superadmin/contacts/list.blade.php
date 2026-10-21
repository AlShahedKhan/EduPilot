@extends('superadmin.navigation')

@section('content')

<div class="sa-ct">
    <div class="sa-ct-head">
        <h4>{{ get_phrase('Contact Submissions') }}</h4>
        <div class="sa-bc">
            <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a>
            <span> / </span>
            <span>{{ get_phrase('Contacts') }}</span>
        </div>
    </div>

    @php
        $total = $contacts->total();
        $newCount = \App\Models\Contact::where('status', 'new')->count();
        $readCount = \App\Models\Contact::where('status', 'read')->count();
        $repliedCount = \App\Models\Contact::where('status', 'replied')->count();
    @endphp

    <div class="sa-ct-stats stagger">
        <div class="sa-ct-stat">
            <div class="sa-ct-stat-icon si-total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
                <div class="sa-ct-stat-label">{{ get_phrase('Total') }}</div>
                <div class="sa-ct-stat-num n-total">{{ $total }}</div>
            </div>
        </div>
        <div class="sa-ct-stat">
            <div class="sa-ct-stat-icon si-new">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            </div>
            <div>
                <div class="sa-ct-stat-label">{{ get_phrase('New') }}</div>
                <div class="sa-ct-stat-num n-new">{{ $newCount }}</div>
            </div>
        </div>
        <div class="sa-ct-stat">
            <div class="sa-ct-stat-icon si-read">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/></svg>
            </div>
            <div>
                <div class="sa-ct-stat-label">{{ get_phrase('Read') }}</div>
                <div class="sa-ct-stat-num n-read">{{ $readCount }}</div>
            </div>
        </div>
        <div class="sa-ct-stat">
            <div class="sa-ct-stat-icon si-replied">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="sa-ct-stat-label">{{ get_phrase('Replied') }}</div>
                <div class="sa-ct-stat-num n-replied">{{ $repliedCount }}</div>
            </div>
        </div>
    </div>

    @if (Session::has('message'))
        <div class="sa-ct-alert sa-ct-alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="sa-ct-card">
        <div class="sa-ct-toolbar">
            <div class="sa-ct-toolbar-left">
                <form method="GET" action="{{ route('superadmin.contacts.list') }}" style="display:flex;gap:8px;align-items:center;">
                    <div class="sa-ct-search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" name="search" placeholder="{{ get_phrase('Search name, email...') }}" value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="sa-ct-btn sa-ct-btn-ghost">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                        {{ get_phrase('Filter') }}
                    </button>
                </form>
            </div>
            <div class="sa-ct-toolbar-right">
                <a href="{{ route('superadmin.contacts.list') }}" class="sa-ct-btn sa-ct-btn-ghost {{ !request('status') ? 'active' : '' }}" style="{{ !request('status') ? 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border-color:#22c55e;' : '' }}">
                    {{ get_phrase('All') }}
                </a>
                <a href="{{ route('superadmin.contacts.list', ['status' => 'new']) }}" class="sa-ct-btn sa-ct-btn-ghost {{ request('status') == 'new' ? 'active' : '' }}" style="{{ request('status') == 'new' ? 'background:#f59e0b;color:#fff;border-color:#f59e0b;' : '' }}">
                    {{ get_phrase('New') }}
                </a>
                <a href="{{ route('superadmin.contacts.list', ['status' => 'read']) }}" class="sa-ct-btn sa-ct-btn-ghost {{ request('status') == 'read' ? 'active' : '' }}" style="{{ request('status') == 'read' ? 'background:#6366f1;color:#fff;border-color:#6366f1;' : '' }}">
                    {{ get_phrase('Read') }}
                </a>
                <a href="{{ route('superadmin.contacts.list', ['status' => 'replied']) }}" class="sa-ct-btn sa-ct-btn-ghost {{ request('status') == 'replied' ? 'active' : '' }}" style="{{ request('status') == 'replied' ? 'background:#22c55e;color:#fff;border-color:#22c55e;' : '' }}">
                    {{ get_phrase('Replied') }}
                </a>
            </div>
        </div>

        @if ($contacts->count() > 0)
            <div style="overflow-x:auto;">
                <table class="sa-ct-tbl">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>{{ get_phrase('Contact') }}</th>
                            <th>{{ get_phrase('Phone') }}</th>
                            <th>{{ get_phrase('School') }}</th>
                            <th>{{ get_phrase('Message') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th>{{ get_phrase('Date') }}</th>
                            <th style="width:90px;">{{ get_phrase('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            @php
                                $colors = ['av-blue','av-green','av-purple','av-amber'];
                                $colorClass = $colors[$contact->id % count($colors)];
                                $initial = strtoupper(substr($contact->name, 0, 1));
                            @endphp
                            <tr class="{{ $contact->status === 'new' ? 'unread' : '' }}">
                                <td style="color:#94a3b8;font-size:12px;font-weight:600;">{{ $contacts->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="sa-ct-person">
                                        <div class="sa-ct-avatar {{ $colorClass }}">{{ $initial }}</div>
                                        <div>
                                            <div class="sa-ct-person-name">{{ $contact->name }}</div>
                                            <div class="sa-ct-person-email"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="sa-ct-phone">{{ $contact->phone ?: '—' }}</td>
                                <td class="sa-ct-school" title="{{ e($contact->school) }}">{{ $contact->school ?: '—' }}</td>
                                <td class="sa-ct-msg" title="{{ e($contact->message) }}">{{ \Illuminate\Support\Str::limit($contact->message, 30) ?: '—' }}</td>
                                <td>
                                    @if ($contact->status === 'new')
                                        <span class="sa-ct-badge sa-ct-badge-new">{{ get_phrase('New') }}</span>
                                    @elseif ($contact->status === 'read')
                                        <span class="sa-ct-badge sa-ct-badge-read">{{ get_phrase('Read') }}</span>
                                    @else
                                        <span class="sa-ct-badge sa-ct-badge-replied">{{ get_phrase('Replied') }}</span>
                                    @endif
                                </td>
                                <td class="sa-ct-date">{{ $contact->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <div class="sa-ct-actions">
                                        <a href="{{ route('superadmin.contacts.show', $contact->id) }}" class="sa-ct-action-btn view" title="{{ get_phrase('View') }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <button type="button" class="sa-ct-action-btn delete" title="{{ get_phrase('Delete') }}" onclick="showDeleteModal({{ $contact->id }}, '{{ e($contact->name) }}')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="sa-ct-pagination">
                {{ $contacts->appends(request()->all())->links() }}
            </div>
        @else
            <div class="sa-ct-empty">
                <div class="sa-ct-empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <h5>{{ get_phrase('No contact submissions found') }}</h5>
                <p>{{ get_phrase('When someone submits the contact form, it will appear here.') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="sa-ct-modal-overlay" id="deleteContactModal">
    <div class="sa-ct-modal">
        <div class="sa-ct-modal-head">
            <h5>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="vertical-align:-3px;margin-right:6px;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                {{ get_phrase('Delete Contact') }}
            </h5>
            <button class="sa-ct-modal-close" onclick="closeDeleteModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sa-ct-modal-body">
            <p>{{ get_phrase('Are you sure you want to delete the submission from') }} <strong id="deleteContactName"></strong>? {{ get_phrase('This action cannot be undone.') }}</p>
            <div class="sa-ct-modal-actions">
                <button class="sa-ct-modal-btn sa-ct-modal-btn-ghost" onclick="closeDeleteModal()">{{ get_phrase('Cancel') }}</button>
                <form id="deleteContactForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="sa-ct-modal-btn sa-ct-modal-btn-red">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        {{ get_phrase('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showDeleteModal(id, name) {
    document.getElementById('deleteContactName').textContent = name;
    document.getElementById('deleteContactForm').action = '{{ route("superadmin.contacts.delete", "__ID__") }}'.replace('__ID__', id);
    document.getElementById('deleteContactModal').classList.add('show');
}
function closeDeleteModal() {
    document.getElementById('deleteContactModal').classList.remove('show');
}
document.getElementById('deleteContactModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection
