@php $profile = $librarian->profile_information; @endphp
<div class="school-librarian-profile">
    <div class="school-librarian-profile__school"><h2>{{ $schoolTitle }}</h2><span class="eBadge {{ $librarian->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $librarian->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="school-librarian-profile__identity"><img src="{{ $librarian->photo_url }}" class="rounded-circle" alt="" width="112" height="112" /><h4>{{ $librarian->name }}</h4><a href="mailto:{{ $librarian->email }}">{{ $librarian->email }}</a></div></div>
        <div class="col-lg-8">
            <ul class="nav nav-pills eNav-Tabs-justify" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="librarian-info-tab-{{ $librarian->id }}" data-bs-toggle="pill" data-bs-target="#librarian-info-{{ $librarian->id }}" type="button" role="tab" aria-controls="librarian-info-{{ $librarian->id }}" aria-selected="true">{{ get_phrase('Librarian Info') }}</button></li>
                @if ($canManageLibrarian)<li class="nav-item" role="presentation"><button class="nav-link" id="librarian-password-tab-{{ $librarian->id }}" data-bs-toggle="pill" data-bs-target="#librarian-password-{{ $librarian->id }}" type="button" role="tab" aria-controls="librarian-password-{{ $librarian->id }}" aria-selected="false">{{ get_phrase('Change Password') }}</button></li>@endif
            </ul>
            <div class="tab-content eNav-Tabs-content">
                <div class="tab-pane fade show active" id="librarian-info-{{ $librarian->id }}" role="tabpanel" aria-labelledby="librarian-info-tab-{{ $librarian->id }}">
                    <dl class="school-librarian-profile__details">
                        <div><dt>{{ get_phrase('Joined') }}</dt><dd>{{ $librarian->created_at?->format('M d, Y') ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Gender') }}</dt><dd>{{ $profile['gender'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Birthday') }}</dt><dd>{{ !empty($profile['birthday']) ? date('M d, Y', (int) $profile['birthday']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Blood group') }}</dt><dd>{{ !empty($profile['blood_group']) ? strtoupper($profile['blood_group']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Phone') }}</dt><dd>@if (!empty($profile['phone']))<a href="tel:{{ $profile['phone'] }}">{{ $profile['phone'] }}</a>@else{{ get_phrase('Not provided') }}@endif</dd></div>
                        <div><dt>{{ get_phrase('Address') }}</dt><dd>{{ $profile['address'] ?? get_phrase('Not provided') }}</dd></div>
                    </dl>
                </div>
                @if ($canManageLibrarian)<div class="tab-pane fade" id="librarian-password-{{ $librarian->id }}" role="tabpanel" aria-labelledby="librarian-password-tab-{{ $librarian->id }}">
                    <form action="{{ route('admin.librarian.password', $librarian) }}" method="POST" class="school-librarian-profile__password">@csrf @method('PATCH')
                        <div><label class="eForm-label" for="librarian-password-{{ $librarian->id }}-input">{{ get_phrase('New password') }}</label><input type="password" class="form-control eForm-control" name="password" id="librarian-password-{{ $librarian->id }}-input" minlength="8" autocomplete="new-password" required /></div>
                        <div><label class="eForm-label" for="librarian-password-confirmation-{{ $librarian->id }}">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" name="password_confirmation" id="librarian-password-confirmation-{{ $librarian->id }}" minlength="8" autocomplete="new-password" required /></div>
                        <button type="submit" class="btn-form">{{ get_phrase('Update Password') }}</button>
                    </form>
                </div>@endif
            </div>
        </div>
    </div>
</div>
