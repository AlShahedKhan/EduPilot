@php $profile = $accountant->profile_information; @endphp
<div class="school-accountant-profile">
    <div class="school-accountant-profile__school"><h2>{{ $schoolTitle }}</h2><span class="eBadge {{ $accountant->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $accountant->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="school-accountant-profile__identity"><img src="{{ $accountant->photo_url }}" class="rounded-circle" alt="" width="112" height="112" /><h4>{{ $accountant->name }}</h4><a href="mailto:{{ $accountant->email }}">{{ $accountant->email }}</a></div></div>
        <div class="col-lg-8">
            <ul class="nav nav-pills eNav-Tabs-justify" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="accountant-info-tab-{{ $accountant->id }}" data-bs-toggle="pill" data-bs-target="#accountant-info-{{ $accountant->id }}" type="button" role="tab" aria-controls="accountant-info-{{ $accountant->id }}" aria-selected="true">{{ get_phrase('Accountant Info') }}</button></li>
                @if ($canManageAccountant)<li class="nav-item" role="presentation"><button class="nav-link" id="accountant-password-tab-{{ $accountant->id }}" data-bs-toggle="pill" data-bs-target="#accountant-password-{{ $accountant->id }}" type="button" role="tab" aria-controls="accountant-password-{{ $accountant->id }}" aria-selected="false">{{ get_phrase('Change Password') }}</button></li>@endif
            </ul>
            <div class="tab-content eNav-Tabs-content">
                <div class="tab-pane fade show active" id="accountant-info-{{ $accountant->id }}" role="tabpanel" aria-labelledby="accountant-info-tab-{{ $accountant->id }}">
                    <dl class="school-accountant-profile__details">
                        <div><dt>{{ get_phrase('Joined') }}</dt><dd>{{ $accountant->created_at?->format('M d, Y') ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Gender') }}</dt><dd>{{ $profile['gender'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Birthday') }}</dt><dd>{{ !empty($profile['birthday']) ? date('M d, Y', (int) $profile['birthday']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Blood group') }}</dt><dd>{{ !empty($profile['blood_group']) ? strtoupper($profile['blood_group']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Phone') }}</dt><dd>@if (!empty($profile['phone']))<a href="tel:{{ $profile['phone'] }}">{{ $profile['phone'] }}</a>@else{{ get_phrase('Not provided') }}@endif</dd></div>
                        <div><dt>{{ get_phrase('Address') }}</dt><dd>{{ $profile['address'] ?? get_phrase('Not provided') }}</dd></div>
                    </dl>
                </div>
                @if ($canManageAccountant)<div class="tab-pane fade" id="accountant-password-{{ $accountant->id }}" role="tabpanel" aria-labelledby="accountant-password-tab-{{ $accountant->id }}">
                    <form action="{{ route('admin.accountant.password', $accountant) }}" method="POST" class="school-accountant-profile__password">@csrf @method('PATCH')
                        <div><label class="eForm-label" for="accountant-password-{{ $accountant->id }}-input">{{ get_phrase('New password') }}</label><input type="password" class="form-control eForm-control" name="password" id="accountant-password-{{ $accountant->id }}-input" minlength="8" autocomplete="new-password" required /></div>
                        <div><label class="eForm-label" for="accountant-password-confirmation-{{ $accountant->id }}">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" name="password_confirmation" id="accountant-password-confirmation-{{ $accountant->id }}" minlength="8" autocomplete="new-password" required /></div>
                        <button type="submit" class="btn-form">{{ get_phrase('Update Password') }}</button>
                    </form>
                </div>@endif
            </div>
        </div>
    </div>
</div>
