@extends('admin.navigation')

@section('content')
<div class="contact-inbox contact-inbox--detail">
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column contact-inbox__detail-heading">
                        <h4>{{ get_phrase('Contact Message') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li>
                            <li><a href="{{ route('admin.contacts.index') }}">{{ get_phrase('Contact Messages') }}</a></li>
                            <li><a href="#">{{ $contact->name }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('admin.contacts.index') }}" class="export_btn contact-inbox__back-link">
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>{{ get_phrase('Back to messages') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger contact-inbox__alert" role="alert">
            <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="row contact-inbox__detail-grid">
        <div class="col-xl-8 col-lg-7">
            <article class="eSection-wrap-2 contact-inbox__message-panel">
                <header class="contact-inbox__message-heading">
                    <div>
                        <span class="contact-inbox__eyebrow">{{ get_phrase('Message from') }}</span>
                        <h5>{{ $contact->name }}</h5>
                    </div>
                    @if ($contact->status === 'new')
                        <span class="eBadge ebg-soft-warning">{{ get_phrase('New') }}</span>
                    @elseif ($contact->status === 'replied')
                        <span class="eBadge ebg-soft-success">{{ get_phrase('Replied') }}</span>
                    @else
                        <span class="eBadge ebg-soft-info">{{ get_phrase('Read') }}</span>
                    @endif
                </header>
                <div class="contact-inbox__message-body">{{ $contact->message }}</div>
                <footer class="contact-inbox__message-footer">
                    <i class="bi bi-clock" aria-hidden="true"></i>
                    <span>{{ get_phrase('Received') }} {{ $contact->created_at->format('d M Y, h:i A') }}</span>
                </footer>
            </article>
        </div>

        <div class="col-xl-4 col-lg-5 mt-3 mt-lg-0">
            <aside class="eSection-wrap-2 contact-inbox__sender-panel">
                <h5>{{ get_phrase('Sender information') }}</h5>
                <dl class="contact-inbox__sender-details">
                    <div>
                        <dt><i class="bi bi-person" aria-hidden="true"></i>{{ get_phrase('Name') }}</dt>
                        <dd>{{ $contact->name }}</dd>
                    </div>
                    <div>
                        <dt><i class="bi bi-envelope" aria-hidden="true"></i>{{ get_phrase('Email') }}</dt>
                        <dd><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></dd>
                    </div>
                    <div>
                        <dt><i class="bi bi-telephone" aria-hidden="true"></i>{{ get_phrase('Phone') }}</dt>
                        <dd>
                            @if ($contact->phone)
                                <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a>
                            @else
                                {{ get_phrase('Not provided') }}
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="contact-inbox__contact-actions">
                    <a href="mailto:{{ $contact->email }}" class="eBtn eBtn btn-light">
                        <i class="bi bi-envelope" aria-hidden="true"></i>{{ get_phrase('Email sender') }}
                    </a>
                    @if ($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="eBtn eBtn btn-light">
                            <i class="bi bi-telephone" aria-hidden="true"></i>{{ get_phrase('Call sender') }}
                        </a>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.contacts.status', $contact) }}" class="contact-inbox__status-form">
                    @csrf
                    <label for="status" class="eForm-label">{{ get_phrase('Message status') }}</label>
                    <select id="status" name="status" class="form-select eForm-select" required>
                        <option value="new" @selected(old('status', $contact->status) === 'new')>{{ get_phrase('New') }}</option>
                        <option value="read" @selected(old('status', $contact->status) === 'read')>{{ get_phrase('Read') }}</option>
                        <option value="replied" @selected(old('status', $contact->status) === 'replied')>{{ get_phrase('Replied') }}</option>
                    </select>
                    <button type="submit" class="eBtn eBtn btn-secondary contact-inbox__status-button">{{ get_phrase('Update status') }}</button>
                </form>
            </aside>
        </div>
    </div>
</div>
@endsection
