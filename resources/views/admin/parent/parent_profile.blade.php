@php $profile = $parent->profile_information; @endphp
<div class="school-parent-profile">
    <div class="school-parent-profile__school"><h2>{{ $schoolTitle }}</h2><span class="eBadge {{ $parent->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $parent->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="school-parent-profile__identity"><img src="{{ $parent->photo_url }}" class="rounded-circle" alt="" width="112" height="112" /><h4>{{ $parent->name }}</h4><a href="mailto:{{ $parent->email }}">{{ $parent->email }}</a></div></div>
        <div class="col-lg-8">
            <ul class="nav nav-pills eNav-Tabs-justify" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="parent-info-tab-{{ $parent->id }}" data-bs-toggle="pill" data-bs-target="#parent-info-{{ $parent->id }}" type="button" role="tab" aria-controls="parent-info-{{ $parent->id }}" aria-selected="true">{{ get_phrase('Parent Info') }}</button></li>
                @if ($canManageParent)<li class="nav-item" role="presentation"><button class="nav-link" id="parent-password-tab-{{ $parent->id }}" data-bs-toggle="pill" data-bs-target="#parent-password-{{ $parent->id }}" type="button" role="tab" aria-controls="parent-password-{{ $parent->id }}" aria-selected="false">{{ get_phrase('Change Password') }}</button></li>@endif
            </ul>
            <div class="tab-content eNav-Tabs-content">
                <div class="tab-pane fade show active" id="parent-info-{{ $parent->id }}" role="tabpanel" aria-labelledby="parent-info-tab-{{ $parent->id }}">
                    <dl class="school-parent-profile__details">
                        <div><dt>{{ get_phrase('Joined') }}</dt><dd>{{ $parent->created_at?->format('M d, Y') ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Gender') }}</dt><dd>{{ $profile['gender'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Birthday') }}</dt><dd>{{ !empty($profile['birthday']) ? date('M d, Y', (int) $profile['birthday']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Blood group') }}</dt><dd>{{ !empty($profile['blood_group']) ? strtoupper($profile['blood_group']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Phone') }}</dt><dd>@if (!empty($profile['phone']))<a href="tel:{{ $profile['phone'] }}">{{ $profile['phone'] }}</a>@else{{ get_phrase('Not provided') }}@endif</dd></div>
                        <div><dt>{{ get_phrase('Address') }}</dt><dd>{{ $profile['address'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Children') }}</dt><dd>@forelse ($parent->children as $child)<span class="d-block"><strong>{{ $child->name }}</strong> — {{ $child->enrollment?->class?->name ?? get_phrase('No class') }} / {{ $child->enrollment?->section?->name ?? get_phrase('No section') }}</span>@empty{{ get_phrase('No linked children') }}@endforelse</dd></div>
                    </dl>
                </div>
                @if ($canManageParent)<div class="tab-pane fade" id="parent-password-{{ $parent->id }}" role="tabpanel" aria-labelledby="parent-password-tab-{{ $parent->id }}">
                    <form action="{{ route('admin.parent.password', $parent) }}" method="POST" class="school-parent-profile__password">@csrf @method('PATCH')
                        <div><label class="eForm-label" for="parent-password-{{ $parent->id }}-input">{{ get_phrase('New password') }}</label><input type="password" class="form-control eForm-control" name="password" id="parent-password-{{ $parent->id }}-input" minlength="8" autocomplete="new-password" required /></div>
                        <div><label class="eForm-label" for="parent-password-confirmation-{{ $parent->id }}">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" name="password_confirmation" id="parent-password-confirmation-{{ $parent->id }}" minlength="8" autocomplete="new-password" required /></div>
                        <button type="submit" class="btn-form">{{ get_phrase('Update Password') }}</button>
                    </form>
                </div>@endif
            </div>
        </div>
    </div>
</div>
