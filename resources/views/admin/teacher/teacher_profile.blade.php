@php $profile = $teacher->profile_information; @endphp
<div class="school-teacher-profile">
    <div class="school-teacher-profile__school"><h2>{{ $schoolTitle }}</h2><span class="eBadge {{ $teacher->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $teacher->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></div>
    <div class="row g-4">
        <div class="col-lg-4"><div class="school-teacher-profile__identity"><img src="{{ $teacher->photo_url }}" class="rounded-circle" alt="" width="112" height="112" /><h4>{{ $teacher->name }}</h4><a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a><p>{{ $teacher->designation ?: get_phrase('No designation') }}</p></div></div>
        <div class="col-lg-8">
            <ul class="nav nav-pills eNav-Tabs-justify" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="teacher-info-tab-{{ $teacher->id }}" data-bs-toggle="pill" data-bs-target="#teacher-info-{{ $teacher->id }}" type="button" role="tab" aria-controls="teacher-info-{{ $teacher->id }}" aria-selected="true">{{ get_phrase('Teacher Info') }}</button></li>
                @if ($canManageTeacher)<li class="nav-item" role="presentation"><button class="nav-link" id="teacher-password-tab-{{ $teacher->id }}" data-bs-toggle="pill" data-bs-target="#teacher-password-{{ $teacher->id }}" type="button" role="tab" aria-controls="teacher-password-{{ $teacher->id }}" aria-selected="false">{{ get_phrase('Change Password') }}</button></li>@endif
            </ul>
            <div class="tab-content eNav-Tabs-content">
                <div class="tab-pane fade show active" id="teacher-info-{{ $teacher->id }}" role="tabpanel" aria-labelledby="teacher-info-tab-{{ $teacher->id }}">
                    <dl class="school-teacher-profile__details">
                        <div><dt>{{ get_phrase('Department') }}</dt><dd>{{ $teacher->department_name ?: get_phrase('Unassigned') }}</dd></div>
                        <div><dt>{{ get_phrase('Gender') }}</dt><dd>{{ $profile['gender'] ?? get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Birthday') }}</dt><dd>{{ !empty($profile['birthday']) ? date('M d, Y', (int) $profile['birthday']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Blood group') }}</dt><dd>{{ !empty($profile['blood_group']) ? strtoupper($profile['blood_group']) : get_phrase('Not provided') }}</dd></div>
                        <div><dt>{{ get_phrase('Phone') }}</dt><dd>@if (!empty($profile['phone']))<a href="tel:{{ $profile['phone'] }}">{{ $profile['phone'] }}</a>@else{{ get_phrase('Not provided') }}@endif</dd></div>
                        <div><dt>{{ get_phrase('Address') }}</dt><dd>{{ $profile['address'] ?? get_phrase('Not provided') }}</dd></div>
                    </dl>
                </div>
                @if ($canManageTeacher)<div class="tab-pane fade" id="teacher-password-{{ $teacher->id }}" role="tabpanel" aria-labelledby="teacher-password-tab-{{ $teacher->id }}">
                    <form action="{{ route('admin.teacher.password', $teacher) }}" method="POST" class="school-teacher-profile__password">@csrf @method('PATCH')
                        <div><label class="eForm-label" for="teacher-password-{{ $teacher->id }}-input">{{ get_phrase('New password') }}</label><input type="password" class="form-control eForm-control" name="password" id="teacher-password-{{ $teacher->id }}-input" minlength="8" autocomplete="new-password" required /></div>
                        <div><label class="eForm-label" for="teacher-password-confirmation-{{ $teacher->id }}">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" name="password_confirmation" id="teacher-password-confirmation-{{ $teacher->id }}" minlength="8" autocomplete="new-password" required /></div>
                        <button type="submit" class="btn-form">{{ get_phrase('Update Password') }}</button>
                    </form>
                </div>@endif
            </div>
        </div>
    </div>
</div>
