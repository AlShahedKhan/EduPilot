<div class="school-admin-form" data-school-admin-form data-crop-url="{{ route('crop.image') }}">
    <form method="POST" enctype="multipart/form-data" class="ajaxForm" action="{{ route('admin.create') }}">
        @csrf
        <div class="school-admin-form__photo">
            <img id="admin-create-preview" src="{{ asset('assets/uploads/user-images/thumbnail.png') }}" alt="{{ get_phrase('Admin photo preview') }}" width="88" height="88" />
            <div>
                <label for="admin-create-photo" class="eForm-label">{{ get_phrase('Profile photo') }}</label>
                <input class="form-control eForm-control-file" id="admin-create-photo" name="photo" accept="image/jpeg,image/png,image/webp" type="file" data-admin-photo-input data-preview="#admin-create-preview" data-output="#admin-create-cropped" />
                <small>{{ get_phrase('JPG, PNG, or WebP. Maximum 2 MB.') }}</small>
                <input type="hidden" name="cropped_photo" id="admin-create-cropped" />
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6"><label for="admin-create-name" class="eForm-label">{{ get_phrase('Name') }}</label><input type="text" class="form-control eForm-control" id="admin-create-name" name="name" maxlength="255" autocomplete="name" required /></div>
            <div class="col-md-6"><label for="admin-create-email" class="eForm-label">{{ get_phrase('Email') }}</label><input type="email" class="form-control eForm-control" id="admin-create-email" name="email" maxlength="255" autocomplete="email" required /></div>
            <div class="col-md-6"><label for="admin-create-password" class="eForm-label">{{ get_phrase('Password') }}</label><input type="password" class="form-control eForm-control" id="admin-create-password" name="password" minlength="8" autocomplete="new-password" required /></div>
            <div class="col-md-6"><label for="admin-create-password-confirmation" class="eForm-label">{{ get_phrase('Confirm password') }}</label><input type="password" class="form-control eForm-control" id="admin-create-password-confirmation" name="password_confirmation" minlength="8" autocomplete="new-password" required /></div>
            <div class="col-md-6"><label for="admin-create-birthday" class="eForm-label">{{ get_phrase('Birthday') }}</label><input type="text" class="form-control eForm-control js-admin-date" id="admin-create-birthday" name="birthday" value="{{ date('m/d/Y') }}" required /></div>
            <div class="col-md-6"><label for="admin-create-gender" class="eForm-label">{{ get_phrase('Gender') }}</label><select name="gender" id="admin-create-gender" class="form-select eForm-select js-admin-select" required><option value="">{{ get_phrase('Select gender') }}</option><option value="Male">{{ get_phrase('Male') }}</option><option value="Female">{{ get_phrase('Female') }}</option><option value="Others">{{ get_phrase('Others') }}</option></select></div>
            <div class="col-md-6"><label for="admin-create-phone" class="eForm-label">{{ get_phrase('Phone number') }}</label><input type="tel" class="form-control eForm-control" id="admin-create-phone" name="phone" maxlength="30" autocomplete="tel" required /></div>
            <div class="col-md-6"><label for="admin-create-blood" class="eForm-label">{{ get_phrase('Blood group') }}</label><select name="blood_group" id="admin-create-blood" class="form-select eForm-select js-admin-select"><option value="">{{ get_phrase('Select blood group') }}</option>@foreach (['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'] as $bloodGroup)<option value="{{ $bloodGroup }}">{{ strtoupper($bloodGroup) }}</option>@endforeach</select></div>
            <div class="col-12"><label for="admin-create-address" class="eForm-label">{{ get_phrase('Address') }}</label><textarea class="form-control eForm-control" id="admin-create-address" name="address" rows="3" maxlength="1000" required></textarea></div>
        </div>

        <div class="school-admin-form__footer"><button class="btn-form" type="submit">{{ get_phrase('Create Admin') }}</button></div>
    </form>
</div>
