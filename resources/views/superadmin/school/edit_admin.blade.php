@extends('superadmin.navigation')

@section('content')
@php
    $info = json_decode($admin->user_information, true) ?: [];
    $user_image = 'uploads/user-images/thumbnail.png';
    if(!empty($info['photo'])){
        $user_image = 'uploads/user-images/'.$info['photo'];
    }
@endphp
<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Edit Admin') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Schools') }}</a></li>
                        <li><a href="{{ route('superadmin.school.admin_list', ['id' => $school->tenant_id]) }}">{{ get_phrase('Admin List') }}</a></li>
                        <li><a href="#">{{ get_phrase('Edit Admin') }}</a></li>
                    </ul>
                </div>
                <div class="export-btn-area">
                    <a href="{{ route('superadmin.school.admin_list', ['id' => $school->tenant_id]) }}" class="export_btn"><i class="bi bi-arrow-left"></i>{{ get_phrase('Back to Admin List') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
        	<div class="title">
				<h3>{{ get_phrase('Admin Edit Form') }}</h3>
			</div>

			<div class="eMain">
				<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.update.admin', ['tenant_id' => $school->tenant_id, 'admin_id' => $admin->id]) }}">
					@csrf
					<div class="row">
						<div class="col-md-6 pb-3">
							<div class="eForm-layouts">
								<p class="column-title">{{ get_phrase('BASIC INFO') }}</p>
								<div class="fpb-7">
									<label for="name" class="eForm-label">{{ get_phrase('Name') }}</label>
									<input type="text" class="form-control eForm-control" id="name" name="name" value="{{ old('name', $admin->name) }}" required>
								</div>
								<div class="fpb-7">
									<label for="email" class="eForm-label">{{ get_phrase('Email') }}</label>
									<input type="email" class="form-control eForm-control" id="email" name="email" value="{{ old('email', $admin->email) }}" required>
								</div>
								<div class="fpb-7">
									<label for="password" class="eForm-label">{{ get_phrase('Password') }}</label>
									<input type="password" class="form-control eForm-control" id="password" name="password" minlength="8" placeholder="{{ get_phrase('Leave blank to keep current password') }}">
									<div class="generatePass d-flex pt-2">
										<div>
											<button type="button" class="btn-form" onclick="generatePassword()">{{ get_phrase('Generate Password') }}</button>
										</div>
									</div>
								</div>
								<div class="fpb-7">
									<label for="photo" class="eForm-label">{{ get_phrase('Photo') }}</label>
									<input class="form-control eForm-control-file" type="file" id="photo" name="photo" accept="image/*">
									<div class="pt-2">
										<img class="img-fluid" width="60" height="60" src="{{ asset('assets') }}/{{ $user_image }}" />
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 pb-3">
							<div class="eForm-layouts">
								<p class="column-title">{{ get_phrase('MORE INFO') }}</p>
								<div class="fpb-7">
									<label for="gender" class="eForm-label">{{ get_phrase('Gender') }}</label>
									<select name="gender" id="gender" class="form-select eForm-select eChoice-multiple-with-remove">
										<option value="">{{ get_phrase('Select a gender') }}</option>
										<option value="Male" @if(($info['gender'] ?? '') == 'Male') selected @endif>{{ get_phrase('Male') }}</option>
										<option value="Female" @if(($info['gender'] ?? '') == 'Female') selected @endif>{{ get_phrase('Female') }}</option>
									</select>
								</div>
								<div class="fpb-7">
									<label for="blood_group" class="eForm-label">{{ get_phrase('Blood group') }}</label>
									<select name="blood_group" id="blood_group" class="form-select eForm-select eChoice-multiple-with-remove">
										<option value="">{{ get_phrase('Select a blood group') }}</option>
										<option value="a+" @if(($info['blood_group'] ?? '') == 'a+') selected @endif>{{ get_phrase('A+') }}</option>
										<option value="a-" @if(($info['blood_group'] ?? '') == 'a-') selected @endif>{{ get_phrase('A-') }}</option>
										<option value="b+" @if(($info['blood_group'] ?? '') == 'b+') selected @endif>{{ get_phrase('B+') }}</option>
										<option value="b-" @if(($info['blood_group'] ?? '') == 'b-') selected @endif>{{ get_phrase('B-') }}</option>
										<option value="ab+" @if(($info['blood_group'] ?? '') == 'ab+') selected @endif>{{ get_phrase('AB+') }}</option>
										<option value="ab-" @if(($info['blood_group'] ?? '') == 'ab-') selected @endif>{{ get_phrase('AB-') }}</option>
										<option value="o+" @if(($info['blood_group'] ?? '') == 'o+') selected @endif>{{ get_phrase('O+') }}</option>
										<option value="o-" @if(($info['blood_group'] ?? '') == 'o-') selected @endif>{{ get_phrase('O-') }}</option>
									</select>
								</div>
								<div class="fpb-7">
									<label for="birthday" class="eForm-label">{{ get_phrase('Birthday') }}</label>
									<input type="date" class="form-control eForm-control" id="birthday" name="birthday" value="{{ !empty($info['birthday']) ? date('Y-m-d', $info['birthday']) : '' }}">
								</div>
								<div class="fpb-7">
									<label for="phone" class="eForm-label">{{ get_phrase('Phone') }}</label>
									<input type="tel" name="phone" class="form-control eForm-control" id="phone" value="{{ old('phone', $info['phone'] ?? '') }}">
								</div>
								<div class="fpb-7">
									<label for="address" class="eForm-label">{{ get_phrase('Address') }}</label>
									<input type="text" class="form-control eForm-control" id="address" name="address" value="{{ old('address', $info['address'] ?? '') }}">
								</div>
								<div class="pt-2">
									<button type="submit" class="btn-form float-end">{{ get_phrase('Update') }}</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
        </div>
    </div>
</div>
@endsection

<script type="text/javascript">
    "use strict";

    function generatePassword() {
        var length = 12;
        var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        var password = "";
        for (var i = 0; i < length; ++i) {
            var randomNumber = Math.floor(Math.random() * charset.length);
            password += charset[randomNumber];
        }
        document.getElementById("password").value = password;
    }
</script>