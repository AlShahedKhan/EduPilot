@extends('superadmin.navigation')

@section('content')

<div class="sa-ct">
    <div class="sa-ct-head">
        <div class="sa-ct-head-row">
            <div>
                <h4>{{ get_phrase('Contact Details') }}</h4>
                <div class="sa-bc">
                    <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a>
                    <span> / </span>
                    <a href="{{ route('superadmin.contacts.list') }}">{{ get_phrase('Contacts') }}</a>
                    <span> / </span>
                    <span>{{ get_phrase('Details') }}</span>
                </div>
            </div>
            <a href="{{ route('superadmin.contacts.list') }}" class="sa-ct-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                {{ get_phrase('Back to List') }}
            </a>
        </div>
    </div>

    @if (Session::has('message'))
        <div class="sa-ct-alert sa-ct-alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="sa-ct-show stagger">
        <!-- Message -->
        <div class="sa-ct-card">
            <div class="sa-ct-card-head">
                <div class="sa-ct-card-icon ic-msg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                <div>
                    <div class="sa-ct-card-title">{{ get_phrase('Message') }}</div>
                    <div class="sa-ct-card-sub">{{ get_phrase('Submitted on') }} {{ $contact->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
            <div class="sa-ct-card-body">
                @if ($contact->message)
                    <div class="sa-ct-msg">{{ $contact->message }}</div>
                @else
                    <div class="sa-ct-msg sa-ct-msg-empty">{{ get_phrase('No message provided.') }}</div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Contact Info -->
            <div class="sa-ct-card" style="margin-bottom:24px;">
                <div class="sa-ct-card-head">
                    <div class="sa-ct-card-icon ic-info">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <div class="sa-ct-card-title">{{ get_phrase('Contact Info') }}</div>
                        <div class="sa-ct-card-sub">{{ get_phrase('Who sent this message') }}</div>
                    </div>
                </div>
                <div class="sa-ct-card-body">
                    <div class="sa-ct-person-header">
                        @php
                            $colors = ['av-blue','av-green','av-purple','av-amber'];
                            $colorClass = $colors[$contact->id % count($colors)];
                            $initial = strtoupper(substr($contact->name, 0, 1));
                        @endphp
                        <div class="sa-ct-person-avatar" style="background: linear-gradient(135deg, {{ $colorClass === 'av-blue' ? '#059669, #047857' : ($colorClass === 'av-green' ? '#22c55e, #16a34a' : ($colorClass === 'av-purple' ? '#8b5cf6, #7c3aed' : '#f59e0b, #d97706')) }});">
                            {{ $initial }}
                        </div>
                        <div>
                            <div class="sa-ct-person-name">{{ $contact->name }}</div>
                            <div class="sa-ct-person-email"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></div>
                        </div>
                    </div>

                    <hr class="sa-ct-divider">

                    <div class="sa-ct-meta">
                        <div class="sa-ct-info">
                            <div class="sa-ct-info-label">{{ get_phrase('Phone') }}</div>
                            <div class="sa-ct-info-value">{{ $contact->phone ?: '—' }}</div>
                        </div>
                        <div class="sa-ct-info">
                            <div class="sa-ct-info-label">{{ get_phrase('School') }}</div>
                            <div class="sa-ct-info-value">{{ $contact->school ?: '—' }}</div>
                        </div>
                        <div class="sa-ct-info">
                            <div class="sa-ct-info-label">{{ get_phrase('Date') }}</div>
                            <div class="sa-ct-info-value">{{ $contact->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="sa-ct-info">
                            <div class="sa-ct-info-label">{{ get_phrase('Status') }}</div>
                            <div class="sa-ct-info-value">
                                @if ($contact->status === 'new')
                                    <span class="sa-ct-badge sa-ct-badge-new">{{ get_phrase('New') }}</span>
                                @elseif ($contact->status === 'read')
                                    <span class="sa-ct-badge sa-ct-badge-read">{{ get_phrase('Read') }}</span>
                                @else
                                    <span class="sa-ct-badge sa-ct-badge-replied">{{ get_phrase('Replied') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="sa-ct-card">
                <div class="sa-ct-card-head">
                    <div class="sa-ct-card-icon ic-status">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <div class="sa-ct-card-title">{{ get_phrase('Update Status') }}</div>
                        <div class="sa-ct-card-sub">{{ get_phrase('Change the contact status') }}</div>
                    </div>
                </div>
                <div class="sa-ct-card-body">
                    <form action="{{ route('superadmin.contacts.status', $contact->id) }}" method="POST">
                        @csrf
                        <div class="sa-ct-status-form">
                            <select name="status" class="sa-ct-status-select">
                                <option value="new" {{ $contact->status === 'new' ? 'selected' : '' }}>{{ get_phrase('New') }}</option>
                                <option value="read" {{ $contact->status === 'read' ? 'selected' : '' }}>{{ get_phrase('Read') }}</option>
                                <option value="replied" {{ $contact->status === 'replied' ? 'selected' : '' }}>{{ get_phrase('Replied') }}</option>
                            </select>
                            <button type="submit" class="sa-ct-status-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                {{ get_phrase('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
