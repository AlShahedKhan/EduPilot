@php $profile = $warden->profile_information; @endphp
<div class="school-warden-profile">
    <div class="school-warden-profile__school"><h2>{{ $schoolTitle }}</h2><span class="eBadge {{ $warden->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $warden->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="school-warden-profile__identity"><img src="{{ $warden->photo_url }}" class="rounded-circle" alt="" width="112" height="112" /><h4>{{ $warden->name }}</h4><a href="mailto:{{ $warden->email }}">{{ $warden->email }}</a></div></div>
        <div class="col-lg-8">
            <ul class="nav nav-pills eNav-Tabs-justify" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="warden-info-tab-{{ $warden->id }}" data-bs-toggle="pill" data-bs-target="#warden-info-{{ $warden->id }}" type="button" role="tab" aria-controls="warden-info-{{ $warden->id }}" aria-selected="true">{{ get_phrase('Warden Info') }}</button></li>
                @if ($canManageWarden)<li class="nav-item" role="presentation"><button class="nav-link" id="warden-password-tab-{{ $warden->id }}" data-bs-toggle="pill" data-bs-target="#warden-password-{{ $warden->id }}" type="button" role="tab" aria-controls="warden-password-{{ $warden->id }}" aria-selected="false">{{ get_phrase('Change Password') }}</button></li>@endif
            </ul>
            <div class="tab-content eNav-Tabs-content">
                <div class="tab-pane fade show active" id="warden-info-{{ $warden->id }}" role="tabpanel" aria-labelledby="warden-info-tab-{{ $warden->id }}">
                    <dl class="school-warden-profile__details">
                        <div><dt>{{ get_phrase('Joined') }}</dt><dd>{{ $warden->created_at?->format('M d, Y') ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Assigned hostels') }}</dt><dd>{{ $warden->hostels->pluck('name')->join(', ') ?: get_phrase('Unassigned') }}</dd></div>
                        <div><dt>{{ get_phrase('Gender') }}</dt><dd>{{ $profile['gender'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Birthday') }}</dt><dd>{{ !empty($profile['birthday']) ? date('M d, Y', (int) $profile['birthday']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Blood group') }}</dt><dd>{{ !empty($profile['blood_group']) ? strtoupper($profile['blood_group']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Phone') }}</dt><dd>@if (!empty($profile['phone']))<a href="tel:{{ $profile['phone'] }}">{{ $profile['phone'] }}</a>@else{{ get_phrase('Not provided') }}@endif</dd></div>
                        <div><dt>{{ get_phrase('Address') }}</dt><dd>{{ $profile['address'] ?? get_phrase('Not provided') }}</dd></div>
                    </dl>
                </div>
                @if ($canManageWarden)<div class="tab-pane fade" id="warden-password-{{ $warden->id }}" role="tabpanel" aria-labelledby="warden-password-tab-{{ $warden->id }}">
                    <form action="{{ route('admin.warden.password', $warden) }}" method="POST" class="school-warden-profile__password">@csrf @method('PATCH')
                        <div><label class="eForm-label" for="warden-password-{{ $warden->id }}-input">{{ get_phrase('New password') }}</label><input type="password" class="form-control eForm-control" name="password" id="warden-password-{{ $warden->id }}-input" minlength="8" autocomplete="new-password" required /></div>
                        <div><label class="eForm-label" for="warden-password-confirmation-{{ $warden->id }}">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" name="password_confirmation" id="warden-password-confirmation-{{ $warden->id }}" minlength="8" autocomplete="new-password" required /></div>
                        <button type="submit" class="btn-form">{{ get_phrase('Update Password') }}</button>
                    </form>
                </div>@endif
            </div>
        </div>
    </div>
</div>
