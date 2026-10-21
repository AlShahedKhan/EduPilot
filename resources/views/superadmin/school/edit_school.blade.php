<div class="row">
    <div class="col-12">
        <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('superadmin.school.update', ['id' => $school->id]) }}">
            @csrf
            <div class="row">
                <div class="col-md-6 pb-3">
                    <div class="eForm-layouts">
                        <p class="column-title">{{ get_phrase('SCHOOL INFO') }}</p>
                        <div class="fpb-7">
                            <label for="title" class="eForm-label">{{ get_phrase('School Name') }}</label>
                            <input type="text" class="form-control eForm-control" id="title" name="title" value="{{ $school->title }}" required>
                        </div>
                        <div class="fpb-7">
                            <label for="email" class="eForm-label">{{ get_phrase('School Email') }}</label>
                            <input type="email" class="form-control eForm-control" id="email" name="email" value="{{ $school->email }}" required>
                        </div>
                        <div class="fpb-7">
                            <label for="phone" class="eForm-label">{{ get_phrase('School Phone') }}</label>
                            <input type="tel" class="form-control eForm-control" id="phone" name="phone" value="{{ $school->phone }}" required>
                        </div>
                        <div class="fpb-7">
                            <label for="address" class="eForm-label">{{ get_phrase('School Address') }}</label>
                            <textarea class="form-control eForm-control" id="address" name="address" rows="2" required>{{ $school->address }}</textarea>
                        </div>
                        <div class="fpb-7">
                            <label for="school_info" class="eForm-label">{{ get_phrase('School Info') }}</label>
                            <textarea class="form-control eForm-control" id="school_info" name="school_info" rows="3" required>{{ $school->school_info }}</textarea>
                        </div>
                        <div class="fpb-7">
                            <label for="school_logo" class="eForm-label">{{ get_phrase('School Logo') }}</label>
                            <input class="form-control eForm-control-file" type="file" id="school_logo" name="school_logo" accept="image/*">
                            @if(!empty($school->school_logo))
                                <div class="pt-2">
                                    <img class="img-fluid" width="80" src="{{ asset('assets/uploads/school_logo/' . $school->school_logo) }}" alt="{{ get_phrase('School Logo') }}">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pb-3">
                    <div class="eForm-layouts">
                        <p class="column-title">{{ get_phrase('ADMIN INFO') }}</p>
                        <div class="fpb-7">
                            <label for="admin_name" class="eForm-label">{{ get_phrase('Admin Name') }}</label>
                            <input type="text" class="form-control eForm-control" id="admin_name" name="admin_name" value="{{ $school->admin_name }}" required>
                        </div>
                        <div class="fpb-7">
                            <label for="admin_email" class="eForm-label">{{ get_phrase('Admin Email') }}</label>
                            <input type="email" class="form-control eForm-control" id="admin_email" name="admin_email" value="{{ $school->admin_email }}" required>
                        </div>
                        <div class="fpb-7">
                            <label for="admin_phone" class="eForm-label">{{ get_phrase('Admin Phone') }}</label>
                            <input type="tel" class="form-control eForm-control" id="admin_phone" name="admin_phone" value="{{ $school->admin_phone }}">
                        </div>
                        <div class="fpb-7">
                            <label for="admin_address" class="eForm-label">{{ get_phrase('Admin Address') }}</label>
                            <input type="text" class="form-control eForm-control" id="admin_address" name="admin_address" value="{{ $school->admin_address }}">
                        </div>
                        <div class="fpb-7 pt-2">
                            <button class="btn-form float-end" type="submit">{{ get_phrase('Update School') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>