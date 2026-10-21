@extends('admin.navigation')

@section('content')
    @php
        $menu_permissions = empty($user->menu_permission) || $user->menu_permission == 'null' ? [] : json_decode($user->menu_permission, true);

        $info = json_decode($user->user_information ?? '{}', true);
        $photo = basename((string) ($info['photo'] ?? ''));
        $user_image = 'uploads/user-images/' . ($photo ?: 'thumbnail.png');

    @endphp


    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>{{ get_phrase('Navigation Menu Settings') }}</h4>
                    <div class="export-btn-area">
                        <a href="{{ route('admin.admin') }}" class="export_btn"><i class="mx-2 bi bi-arrow-left"></i>{{ get_phrase('Back') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center school-admin-permissions">
        <div class="col-12 col-xl-9">
            <div class="eSection-wrap">
                <div class="eMain">
                    <div class="row g-4">
                        <div class="col-12 col-md-4">
                            <div class="userImG d-flex justify-content-center mt-4">
                                <img width="100" height="100" src="{{ asset('assets') }}/{{ $user_image }}" alt="" />
                            </div>

                            <h4 class="mt-3 text-center">{{ $user->name }}</h4>
                            <p class="text-center">{{ $user->email }}</p>

                        </div>
                        <div class="col-12 col-md-8">
                            <div class="eForm-layouts">
                                <form method="POST" class="d-block" action="{{ route('admin.admin.menu_permission_update', $user) }}">
                                    @csrf
                                    @method('PATCH')

                                    <div class="school-admin-permissions__grid">
                                    @foreach ($permissionOptions as $route_name => $label)
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="permission-{{ $loop->index }}" name="permissions[]" value="{{ $route_name }}" @checked(in_array($route_name, $menu_permissions, true)) />
                                            <label class="form-check-label" for="permission-{{ $loop->index }}">{{ get_phrase($label) }}</label>
                                        </div>
                                    @endforeach
                                    </div>

                                    <div class="fpb-7 pt-2">
                                        <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
