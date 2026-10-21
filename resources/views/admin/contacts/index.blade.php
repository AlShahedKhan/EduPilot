@extends('admin.navigation')

@section('content')
<div class="contact-inbox">
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Contact Messages') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Back Office') }}</a></li>
                            <li><a href="{{ route('admin.contacts.index') }}">{{ get_phrase('Contact Messages') }}</a></li>
                        </ul>
                    </div>
                    <p class="contact-inbox__intro mb-0">{{ get_phrase('Review and manage messages sent from your school website.') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success contact-inbox__alert" role="status">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <nav class="contact-inbox__summary" aria-label="{{ get_phrase('Message status filters') }}">
        <a href="{{ route('admin.contacts.index', ['search' => $search]) }}"
            class="contact-inbox__summary-card {{ $status === '' ? 'is-active' : '' }}"
            @if ($status === '') aria-current="page" @endif>
            <span class="contact-inbox__summary-icon contact-inbox__summary-icon--all"><i class="bi bi-inbox" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('All messages') }}</small><strong>{{ $totalContacts }}</strong></span>
        </a>
        <a href="{{ route('admin.contacts.index', ['search' => $search, 'status' => 'new']) }}"
            class="contact-inbox__summary-card {{ $status === 'new' ? 'is-active' : '' }}"
            @if ($status === 'new') aria-current="page" @endif>
            <span class="contact-inbox__summary-icon contact-inbox__summary-icon--new"><i class="bi bi-envelope" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('New') }}</small><strong>{{ $statusCounts->get('new', 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.contacts.index', ['search' => $search, 'status' => 'read']) }}"
            class="contact-inbox__summary-card {{ $status === 'read' ? 'is-active' : '' }}"
            @if ($status === 'read') aria-current="page" @endif>
            <span class="contact-inbox__summary-icon contact-inbox__summary-icon--read"><i class="bi bi-envelope-open" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('Read') }}</small><strong>{{ $statusCounts->get('read', 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.contacts.index', ['search' => $search, 'status' => 'replied']) }}"
            class="contact-inbox__summary-card {{ $status === 'replied' ? 'is-active' : '' }}"
            @if ($status === 'replied') aria-current="page" @endif>
            <span class="contact-inbox__summary-icon contact-inbox__summary-icon--replied"><i class="bi bi-reply" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('Replied') }}</small><strong>{{ $statusCounts->get('replied', 0) }}</strong></span>
        </a>
    </nav>

    <div class="eSection-wrap-2 contact-inbox__panel">
        <form method="GET" action="{{ route('admin.contacts.index') }}" class="contact-inbox__toolbar">
            <div class="search-input contact-inbox__search d-flex justify-content-start align-items-center">
                <span aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <path d="M2,7A4.951,4.951,0,0,1,7,2a4.951,4.951,0,0,1,5,5,4.951,4.951,0,0,1-5,5A4.951,4.951,0,0,1,2,7Zm12.3,8.7a.99.99,0,0,0,1.4-1.4l-3.1-3.1A6.847,6.847,0,0,0,14,7,6.957,6.957,0,0,0,7,0,6.957,6.957,0,0,0,0,7a6.957,6.957,0,0,0,7,7,6.847,6.847,0,0,0,4.2-1.4Z" fill="#797c8b" />
                    </svg>
                </span>
                <label for="contact-search" class="visually-hidden">{{ get_phrase('Search messages') }}</label>
                <input id="contact-search" type="search" name="search" value="{{ $search }}" placeholder="{{ get_phrase('Search by name, email, phone, or message') }}" class="form-control" />
            </div>
            <div class="contact-inbox__filter-actions">
                <label for="contact-status" class="visually-hidden">{{ get_phrase('Filter by status') }}</label>
                <select id="contact-status" name="status" class="form-select eForm-select">
                    <option value="">{{ get_phrase('All statuses') }}</option>
                    <option value="new" @selected($status === 'new')>{{ get_phrase('New') }}</option>
                    <option value="read" @selected($status === 'read')>{{ get_phrase('Read') }}</option>
                    <option value="replied" @selected($status === 'replied')>{{ get_phrase('Replied') }}</option>
                </select>
                <button type="submit" class="eBtn eBtn btn-secondary contact-inbox__button">{{ get_phrase('Filter') }}</button>
                @if ($search !== '' || $status !== '')
                    <a href="{{ route('admin.contacts.index') }}" class="eBtn eBtn btn-light contact-inbox__button">{{ get_phrase('Reset') }}</a>
                @endif
            </div>
        </form>

        @if ($contacts->count() > 0)
            <div class="table-responsive contact-inbox__desktop-list d-none d-md-block">
                <table class="table eTable contact-inbox__table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Sender') }}</th>
                            <th>{{ get_phrase('Phone') }}</th>
                            <th>{{ get_phrase('Message') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th>{{ get_phrase('Received') }}</th>
                            <th class="text-end">{{ get_phrase('Option') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr class="{{ $contact->status === 'new' ? 'contact-inbox__unread' : '' }}">
                                <td>{{ $contacts->firstItem() + $loop->index }}</td>
                                <td class="contact-inbox__sender">
                                    <span class="contact-inbox__sender-name">
                                        @if ($contact->status === 'new')
                                            <span class="contact-inbox__unread-dot" aria-hidden="true"></span>
                                            <span class="visually-hidden">{{ get_phrase('Unread message') }}:</span>
                                        @endif
                                        {{ $contact->name }}
                                    </span>
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                </td>
                                <td class="contact-inbox__breakable">{{ $contact->phone ?: get_phrase('Not provided') }}</td>
                                <td class="contact-inbox__preview" title="{{ $contact->message }}">{{ \Illuminate\Support\Str::limit($contact->message, 70) }}</td>
                                <td>
                                    @if ($contact->status === 'new')
                                        <span class="eBadge ebg-soft-warning">{{ get_phrase('New') }}</span>
                                    @elseif ($contact->status === 'replied')
                                        <span class="eBadge ebg-soft-success">{{ get_phrase('Replied') }}</span>
                                    @else
                                        <span class="eBadge ebg-soft-info">{{ get_phrase('Read') }}</span>
                                    @endif
                                </td>
                                <td class="contact-inbox__date">{{ $contact->created_at->format('d M Y') }}<small>{{ $contact->created_at->format('h:i A') }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.contacts.show', $contact) }}" class="contact-inbox__view-link">
                                        {{ get_phrase('View') }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="contact-inbox__mobile-list d-md-none">
                @foreach ($contacts as $contact)
                    <article class="contact-inbox__message-card {{ $contact->status === 'new' ? 'is-unread' : '' }}">
                        <div class="contact-inbox__message-card-header">
                            <div class="contact-inbox__sender">
                                <span class="contact-inbox__sender-name">
                                    @if ($contact->status === 'new')
                                        <span class="contact-inbox__unread-dot" aria-hidden="true"></span>
                                        <span class="visually-hidden">{{ get_phrase('Unread message') }}:</span>
                                    @endif
                                    {{ $contact->name }}
                                </span>
                                <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                            </div>
                            @if ($contact->status === 'new')
                                <span class="eBadge ebg-soft-warning">{{ get_phrase('New') }}</span>
                            @elseif ($contact->status === 'replied')
                                <span class="eBadge ebg-soft-success">{{ get_phrase('Replied') }}</span>
                            @else
                                <span class="eBadge ebg-soft-info">{{ get_phrase('Read') }}</span>
                            @endif
                        </div>
                        <p class="contact-inbox__message-card-preview">{{ \Illuminate\Support\Str::limit($contact->message, 130) }}</p>
                        <div class="contact-inbox__message-card-meta">
                            <span><i class="bi bi-clock" aria-hidden="true"></i> {{ $contact->created_at->format('d M Y, h:i A') }}</span>
                            @if ($contact->phone)
                                <a href="tel:{{ $contact->phone }}"><i class="bi bi-telephone" aria-hidden="true"></i> {{ $contact->phone }}</a>
                            @endif
                        </div>
                        <a href="{{ route('admin.contacts.show', $contact) }}" class="contact-inbox__mobile-action">
                            {{ get_phrase('View message') }} <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="admin-tInfo-pagi contact-inbox__pagination d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                <p class="admin-tInfo">{{ get_phrase('Showing') }} {{ $contacts->firstItem() }} - {{ $contacts->lastItem() }} {{ get_phrase('from') }} {{ $contacts->total() }} {{ get_phrase('messages') }}</p>
                <div class="admin-pagi">{{ $contacts->links() }}</div>
            </div>
        @else
            <div class="empty_box center contact-inbox__empty">
                <img class="mb-3" width="150" src="{{ asset('assets/images/empty_box.png') }}" alt="" />
                <br>
                @if ($totalContacts === 0)
                    <h5>{{ get_phrase('Your inbox is empty') }}</h5>
                    <p>{{ get_phrase('Messages sent from your school website will appear here.') }}</p>
                @else
                    <h5>{{ get_phrase('No matching messages') }}</h5>
                    <p>{{ get_phrase('Try changing your search or status filter.') }}</p>
                    <a href="{{ route('admin.contacts.index') }}" class="eBtn eBtn btn-secondary contact-inbox__empty-action">{{ get_phrase('Clear filters') }}</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
