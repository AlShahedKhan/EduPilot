@extends('superadmin.navigation')

@section('content')

    <div class="sa-as">
        <div class="sa-as-head">
            <h4>{{ get_phrase('Edit School') }}</h4>
            <div class="sa-bc">
                <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a>
                <span> / </span>
                <a href="{{ route('superadmin.school.list') }}">{{ get_phrase('Schools') }}</a>
                <span> / </span>
                <span>{{ get_phrase('Edit School') }}</span>
            </div>
        </div>

        @if ($errors->any())
            <div class="sa-as-alert sa-as-alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <div>
                    <strong
                        style="display:block;margin-bottom:2px;">{{ get_phrase('Please fix the following errors:') }}</strong>
                    <ul style="margin:0;padding-left:16px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" enctype="multipart/form-data"
            action="{{ route('superadmin.school.update', ['id' => $school->id]) }}">
            @csrf

            <div class="sa-as-layout stagger">
                <!-- School Info -->
                <div class="sa-as-card">
                    <div class="sa-as-card-head">
                        <div class="sa-as-card-icon ic-school">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6" />
                            </svg>
                        </div>
                        <div>
                            <div class="sa-as-card-title">{{ get_phrase('School Information') }}</div>
                            <div class="sa-as-card-sub">{{ get_phrase('Basic school details and contact') }}</div>
                        </div>
                    </div>
                    <div class="sa-as-card-body">
                        <div class="sa-as-field">
                            <label>{{ get_phrase('School Name') }} <span class="sa-required">*</span></label>
                            <input type="text" name="title" class="sa-as-input @error('title') is-invalid @enderror"
                                value="{{ old('title', $school->title) }}" required>
                            @error('title')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sa-as-row">
                            <div class="sa-as-field">
                                <label>{{ get_phrase('School Email') }} <span class="sa-required">*</span></label>
                                <input type="email" name="email"
                                    class="sa-as-input @error('email') is-invalid @enderror"
                                    value="{{ old('email', $school->email) }}" required>
                                @error('email')
                                    <div class="sa-as-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="sa-as-field">
                                <label>{{ get_phrase('School Phone') }} <span class="sa-required">*</span></label>
                                <input type="tel" name="phone"
                                    class="sa-as-input @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $school->phone) }}" required>
                                @error('phone')
                                    <div class="sa-as-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="sa-as-field">
                            <label>{{ get_phrase('School Address') }} <span class="sa-required">*</span></label>
                            <input type="text" name="address" class="sa-as-input @error('address') is-invalid @enderror"
                                value="{{ old('address', $school->address) }}" required>
                            @error('address')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sa-as-field">
                            <label>{{ get_phrase('School Info') }} <span class="sa-required">*</span></label>
                            <textarea name="school_info" class="sa-as-input @error('school_info') is-invalid @enderror" required>{{ old('school_info', $school->school_info) }}</textarea>
                            @error('school_info')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sa-as-field">
                            <label>{{ get_phrase('School Logo') }}</label>
                            @if (!empty($school->school_logo))
                                <div class="sa-as-field-preview">
                                    <img src="{{ asset('assets/uploads/school_logo/' . $school->school_logo) }}"
                                        alt="{{ get_phrase('School Logo') }}">
                                </div>
                            @endif
                            <div class="sa-as-file">
                                <input type="file" name="school_logo" accept="image/*">
                                <div class="sa-as-file-box">
                                    <div class="sa-as-file-icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                            <circle cx="8.5" cy="8.5" r="1.5" />
                                            <polyline points="21 15 16 10 5 21" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="sa-as-file-text"><strong>{{ get_phrase('Choose a file') }}</strong>
                                            {{ get_phrase('or drag & drop') }}</div>
                                        <div class="sa-as-file-hint">PNG, JPG, SVG up to 2MB</div>
                                    </div>
                                </div>
                            </div>
                            @error('school_logo')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Info -->
                <div class="sa-as-card">
                    <div class="sa-as-card-head">
                        <div class="sa-as-card-icon ic-admin">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        <div>
                            <div class="sa-as-card-title">{{ get_phrase('Admin Information') }}</div>
                            <div class="sa-as-card-sub">{{ get_phrase('Primary admin account for this school') }}</div>
                        </div>
                    </div>
                    <div class="sa-as-card-body">
                        <div class="sa-as-field">
                            <label>{{ get_phrase('Admin Name') }} <span class="sa-required">*</span></label>
                            <input type="text" name="admin_name"
                                class="sa-as-input @error('admin_name') is-invalid @enderror"
                                value="{{ old('admin_name', $school->admin_name) }}" required>
                            @error('admin_name')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sa-as-field">
                            <label>{{ get_phrase('Admin Email') }} <span class="sa-required">*</span></label>
                            <input type="email" name="admin_email"
                                class="sa-as-input @error('admin_email') is-invalid @enderror"
                                value="{{ old('admin_email', $school->admin_email) }}" required>
                            @error('admin_email')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sa-as-row">
                            <div class="sa-as-field">
                                <label>{{ get_phrase('Admin Phone') }}</label>
                                <input type="tel" name="admin_phone"
                                    class="sa-as-input @error('admin_phone') is-invalid @enderror"
                                    value="{{ old('admin_phone', $school->admin_phone) }}">
                                @error('admin_phone')
                                    <div class="sa-as-error">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="sa-as-field">
                                <label>{{ get_phrase('Admin Address') }}</label>
                                <input type="text" name="admin_address"
                                    class="sa-as-input @error('admin_address') is-invalid @enderror"
                                    value="{{ old('admin_address', $school->admin_address) }}">
                                @error('admin_address')
                                    <div class="sa-as-error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BTCL Information -->
            <div class="sa-as-card">
                <div class="sa-as-card-head">
                    <div class="sa-as-card-icon ic-btcl">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                            <circle cx="12" cy="9" r="2.5" />
                        </svg>
                    </div>
                    <div>
                        <div class="sa-as-card-title">{{ get_phrase('BTCL Information') }}</div>
                        <div class="sa-as-card-sub">{{ get_phrase('BTCL email, phone and password for this school') }}
                        </div>
                    </div>
                </div>
                <div class="sa-as-card-body">
                    <div class="sa-as-row">
                        <div class="sa-as-field">
                            <label>{{ get_phrase('BTCL Email') }}</label>
                            <input type="email" name="btcl_email"
                                class="sa-as-input @error('btcl_email') is-invalid @enderror"
                                value="{{ old('btcl_email', $school->btcl_email) }}"
                                placeholder="{{ get_phrase('btcl@school.com') }}">
                            @error('btcl_email')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="sa-as-field">
                            <label>{{ get_phrase('BTCL Phone No.') }}</label>
                            <input type="tel" name="btcl_phone"
                                class="sa-as-input @error('btcl_phone') is-invalid @enderror"
                                value="{{ old('btcl_phone', $school->btcl_phone) }}"
                                placeholder="{{ get_phrase('+880 2XXXXXXXXX') }}">
                            @error('btcl_phone')
                                <div class="sa-as-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="sa-as-field">
                        <label>{{ get_phrase('BTCL Password') }}</label>
                        <input type="text" name="btcl_password"
                            class="sa-as-input @error('btcl_password') is-invalid @enderror"
                            value="{{ old('btcl_password', $school->btcl_password) }}"
                            placeholder="{{ get_phrase('BTCL account password') }}">
                        @error('btcl_password')
                            <div class="sa-as-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="sa-as-card">
                <div class="sa-as-submit">
                    <a href="{{ route('superadmin.school.list') }}" class="sa-as-btn sa-as-btn-ghost">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M19 12H5M12 19l-7-7 7-7" />
                        </svg>
                        {{ get_phrase('Cancel') }}
                    </a>
                    <button type="submit" class="sa-as-btn sa-as-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        {{ get_phrase('Update School') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
