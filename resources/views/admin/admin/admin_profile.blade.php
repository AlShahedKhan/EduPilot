<div class="school-admin-profile"><div class="row">
    <div class="col-12">
        <div class="school_name">
            <h2 class="text-center">{{ $schoolTitle }}</h2>
        </div>
    </div>
</div>

<section class="profile">
    <div class="container"> 
      <div class="row">
        <div class="col-lg-4">
          <div class="profile_img">
              <div class="test_div">
                  <div class="student_simg">
                      <img src="{{ $user_details['photo'] }}" class="rounded-circle div-sc-five" alt="" width="110" height="110">
                  </div>
                  <div class="name_title mt-3 text-center">
                      <h4>{{ get_phrase('Name') }} : {{ $user_details['name'] }}</h4>
                      <h4>{{ get_phrase('Email') }} : {{ null_checker($user_details['email']) }}</h4>
                      
                  </div>
              </div>
          </div>
      </div>
      <div class="col-lg-8">
            <ul
              class="nav nav-pills eNav-Tabs-justify"
              id="pills-tab"
              role="tablist"
            >
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active"
                  id="pills-jHome-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-jHome"
                  type="button"
                  role="tab"
                  aria-controls="pills-jHome"
                  aria-selected="true"
                >
                {{ get_phrase('Admin Info') }}
                </button>
              </li>
              @if ($canManageAdmin)<li class="nav-item" role="presentation">
                <button
                  class="nav-link"
                  id="pills-jProfile-tab"
                  data-bs-toggle="pill"
                  data-bs-target="#pills-jProfile"
                  type="button"
                  role="tab"
                  aria-controls="pills-jProfile"
                  aria-selected="false"
                >
                {{ get_phrase('Change Password') }}
                </button>
              </li>@endif
            </ul>
            <div
              class="tab-content eNav-Tabs-content"
              id="pills-tabContent"
            >
              <div
                class="tab-pane fade show active"
                id="pills-jHome"
                role="tabpanel"
                aria-labelledby="pills-jHome-tab"
              >
                <div class="text name_title">
                    <h4>{{ get_phrase('Name') }} : {{ $user_details['name'] }}</h4>
                    <h4>{{ get_phrase('Gender') }} : {{ null_checker($user_details['gender']) }}</h4>
                    <h4>{{ get_phrase('Address') }} : {{ null_checker($user_details['address']) }}</h4>
                    <h4>{{ get_phrase('Blood') }} : {{ null_checker(strtoupper($user_details['blood_group'])) }}</h4>
                    <h4>{{ get_phrase('Contact') }} : {{ null_checker($user_details['phone']) }}</h4>
                </div>
              </div>
              @if ($canManageAdmin)<div
                class="tab-pane fade"
                id="pills-jProfile"
                role="tabpanel"
                aria-labelledby="pills-jProfile-tab"
              >
                <form action="{{ route('admin.user_password', $admin) }}" method="post" class="school-admin-profile__password">
                          @csrf
                          @method('PATCH')
                          <div class="fpb-7">
                              <label class="eForm-label" for="password{{ $user_details['id'] }}">{{ get_phrase('New password') }}</label>
                              <input type="password" class="form-control eForm-control" name="password" id="password{{ $user_details['id'] }}" minlength="8" autocomplete="new-password" required>
                            </div>
                          <div class="fpb-7">
                              <label class="eForm-label" for="password-confirmation{{ $user_details['id'] }}">{{ get_phrase('Confirm password') }}</label>
                              <input type="password" class="form-control eForm-control" name="password_confirmation" id="password-confirmation{{ $user_details['id'] }}" minlength="8" autocomplete="new-password" required>
                          </div>
                            <div class="generatePass d-flex">
                              <div class="pt-2">
                                <button type="submit" class="btn-form float-end">{{ get_phrase('Submit') }}</button>
                              </div>
                            </div> 
                        </form>  
              </div>@endif
            </div>
      </div>
    </div>
</section>
</div>
