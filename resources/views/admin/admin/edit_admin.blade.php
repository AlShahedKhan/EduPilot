@php
    $photo = basename((string) ($profile['photo'] ?? ''));
    $photoUrl = asset('assets/uploads/user-images/'.($photo ?: 'thumbnail.png'));
    $birthday = !empty($profile['birthday']) ? date('m/d/Y', (int) $profile['birthday']) : date('m/d/Y');
@endphp

<div class="school-admin-form" data-school-admin-form data-crop-url="{{ route('crop.image') }}">
    <form method="POST" enctype="multipart/form-data" class="ajaxForm" action="{{ route('admin.update', $user) }}">
        @csrf
        @method('PATCH')
        <div class="school-admin-form__photo">
            <img id="admin-edit-preview-{{ $user->id }}" src="{{ $photoUrl }}" alt="{{ get_phrase('Admin photo preview') }}" width="88" height="88" />
            <div>
                <label for="admin-edit-photo-{{ $user->id }}" class="eForm-label">{{ get_phrase('Profile photo') }}</label>
                <input class="form-control eForm-control-file" id="admin-edit-photo-{{ $user->id }}" name="photo" accept="image/jpeg,image/png,image/webp" type="file" data-admin-photo-input data-preview="#admin-edit-preview-{{ $user->id }}" data-output="#admin-edit-cropped-{{ $user->id }}" />
                <small>{{ get_phrase('Leave empty to keep the current photo.') }}</small>
                <input type="hidden" name="cropped_photo" id="admin-edit-cropped-{{ $user->id }}" />
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6"><label for="admin-edit-name-{{ $user->id }}" class="eForm-label">{{ get_phrase('Name') }}</label><input type="text" class="form-control eForm-control" id="admin-edit-name-{{ $user->id }}" name="name" value="{{ $user->name }}" maxlength="255" autocomplete="name" required /></div>
            <div class="col-md-6"><label for="admin-edit-email-{{ $user->id }}" class="eForm-label">{{ get_phrase('Email') }}</label><input type="email" class="form-control eForm-control" id="admin-edit-email-{{ $user->id }}" name="email" value="{{ $user->email }}" maxlength="255" autocomplete="email" required /></div>
            <div class="col-md-6"><label for="admin-edit-birthday-{{ $user->id }}" class="eForm-label">{{ get_phrase('Birthday') }}</label><input type="text" class="form-control eForm-control js-admin-date" id="admin-edit-birthday-{{ $user->id }}" name="birthday" value="{{ $birthday }}" required /></div>
            <div class="col-md-6"><label for="admin-edit-gender-{{ $user->id }}" class="eForm-label">{{ get_phrase('Gender') }}</label><select name="gender" id="admin-edit-gender-{{ $user->id }}" class="form-select eForm-select js-admin-select" required><option value="">{{ get_phrase('Select gender') }}</option>@foreach (['Male', 'Female', 'Others'] as $gender)<option value="{{ $gender }}" @selected(($profile['gender'] ?? '') === $gender)>{{ get_phrase($gender) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="admin-edit-phone-{{ $user->id }}" class="eForm-label">{{ get_phrase('Phone number') }}</label><input type="tel" class="form-control eForm-control" id="admin-edit-phone-{{ $user->id }}" name="phone" value="{{ $profile['phone'] ?? '' }}" maxlength="30" autocomplete="tel" required /></div>
            <div class="col-md-6"><label for="admin-edit-blood-{{ $user->id }}" class="eForm-label">{{ get_phrase('Blood group') }}</label><select name="blood_group" id="admin-edit-blood-{{ $user->id }}" class="form-select eForm-select js-admin-select"><option value="">{{ get_phrase('Select blood group') }}</option>@foreach (['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'] as $bloodGroup)<option value="{{ $bloodGroup }}" @selected(($profile['blood_group'] ?? '') === $bloodGroup)>{{ strtoupper($bloodGroup) }}</option>@endforeach</select></div>
            <div class="col-12"><label for="admin-edit-address-{{ $user->id }}" class="eForm-label">{{ get_phrase('Address') }}</label><textarea class="form-control eForm-control" id="admin-edit-address-{{ $user->id }}" name="address" rows="3" maxlength="1000" required>{{ $profile['address'] ?? '' }}</textarea></div>
        </div>

        <div class="school-admin-form__footer"><button class="btn-form" type="submit">{{ get_phrase('Update Admin') }}</button></div>
    </form>
</div>
