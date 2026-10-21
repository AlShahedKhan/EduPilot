@extends('superadmin.navigation')

@php $errors = $errors ?? session()->get('errors', new \Illuminate\Support\ViewErrorBag); @endphp

@section('content')

<div class="sa-compose-wrap">
    <div class="sa-compose-card">
        <div class="sa-compose-head">
            <h5>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-3px;margin-right:6px;">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
                {{ get_phrase('Compose Email') }}
            </h5>
            <a href="{{ route('superadmin.email_logs') }}" class="sa-compose-btn sa-compose-btn-cancel">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                {{ get_phrase('Back') }}
            </a>
        </div>

        <form method="POST" action="{{ route('superadmin.email.send') }}">
            @csrf
            <div class="sa-compose-body">
                <div class="sa-compose-field">
                    <label for="to">{{ get_phrase('To') }} *</label>
                    <input type="email" id="to" name="to" value="{{ old('to') }}" placeholder="user@example.com" required>
                    @error('to') <span style="color:#ef4444;font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="sa-compose-field">
                    <label for="subject">{{ get_phrase('Subject') }} *</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="{{ get_phrase('Enter email subject') }}" required>
                    @error('subject') <span style="color:#ef4444;font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="sa-compose-field">
                    <label for="body">{{ get_phrase('Message') }} *</label>
                    <textarea id="body" name="body" placeholder="{{ get_phrase('Type your email message here...') }}" required>{{ old('body') }}</textarea>
                    @error('body') <span style="color:#ef4444;font-size:12px;">{{ $message }}</span> @enderror
                </div>

                <div class="sa-compose-info">
                    <strong>{{ get_phrase('Sending from') }}:</strong> {{ get_mail_config('MAIL_FROM_ADDRESS') }} ({{ get_mail_config('MAIL_HOST') }}:{{ get_mail_config('MAIL_PORT') }})
                </div>

                <div class="sa-compose-actions">
                    <button type="submit" class="sa-compose-btn sa-compose-btn-send">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        {{ get_phrase('Send Email') }}
                    </button>
                    <a href="{{ route('superadmin.email_logs') }}" class="sa-compose-btn sa-compose-btn-cancel">{{ get_phrase('Cancel') }}</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
