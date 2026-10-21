@extends('admin.navigation')

@section('content')
<div class="school-admin-directory" data-directory-root>
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('School Admins') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Users') }}</a></li>
                            <li><a href="{{ route('admin.admin') }}">{{ get_phrase('Admins') }}</a></li>
                        </ul>
                    </div>
                    @if ($canManageAdmins)
                        <div class="export-btn-area">
                            <button type="button" class="export_btn school-admin-directory__create" data-admin-modal="right"
                                data-modal-url="{{ route('admin.open_modal') }}" data-modal-title="{{ get_phrase('Create Admin') }}">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ get_phrase('Create Admin') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning school-admin-directory__alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i><span>{{ session('warning') }}</span>
        </div>
    @endif

    <nav class="school-admin-directory__summary" aria-label="{{ get_phrase('Admin status filters') }}">
        <a href="{{ route('admin.admin', ['search' => $search]) }}" class="school-admin-directory__summary-card {{ $status === '' ? 'is-active' : '' }}" @if ($status === '') aria-current="page" @endif>
            <span class="school-admin-directory__summary-icon school-admin-directory__summary-icon--all"><i class="bi bi-people" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('All admins') }}</small><strong>{{ (int) ($summary->total ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.admin', ['search' => $search, 'status' => 'active']) }}" class="school-admin-directory__summary-card {{ $status === 'active' ? 'is-active' : '' }}" @if ($status === 'active') aria-current="page" @endif>
            <span class="school-admin-directory__summary-icon school-admin-directory__summary-icon--active"><i class="bi bi-person-check" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('Active') }}</small><strong>{{ (int) ($summary->active ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.admin', ['search' => $search, 'status' => 'disabled']) }}" class="school-admin-directory__summary-card {{ $status === 'disabled' ? 'is-active' : '' }}" @if ($status === 'disabled') aria-current="page" @endif>
            <span class="school-admin-directory__summary-icon school-admin-directory__summary-icon--disabled"><i class="bi bi-person-x" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('Disabled') }}</small><strong>{{ (int) ($summary->disabled ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.admin', ['search' => $search, 'status' => 'primary']) }}" class="school-admin-directory__summary-card {{ $status === 'primary' ? 'is-active' : '' }}" @if ($status === 'primary') aria-current="page" @endif>
            <span class="school-admin-directory__summary-icon school-admin-directory__summary-icon--owner"><i class="bi bi-shield-check" aria-hidden="true"></i></span>
            <span><small>{{ get_phrase('Primary') }}</small><strong>{{ (int) ($summary->primary_admins ?? 0) }}</strong></span>
        </a>
    </nav>

    <div class="eSection-wrap-2 school-admin-directory__panel">
        <div class="school-admin-directory__toolbar">
            <form method="GET" action="{{ route('admin.admin') }}" class="school-admin-directory__filters">
                <div class="search-input school-admin-directory__search d-flex justify-content-start align-items-center">
                    <span aria-hidden="true"><i class="bi bi-search"></i></span>
                    <label for="admin-search" class="visually-hidden">{{ get_phrase('Search admins') }}</label>
                    <input id="admin-search" type="search" name="search" value="{{ $search }}" placeholder="{{ get_phrase('Search by name or email') }}" class="form-control" />
                </div>
                <div class="school-admin-directory__filter-actions">
                    <label for="admin-status" class="visually-hidden">{{ get_phrase('Filter by status') }}</label>
                    <select id="admin-status" name="status" class="form-select eForm-select">
                        <option value="">{{ get_phrase('All statuses') }}</option>
                        <option value="active" @selected($status === 'active')>{{ get_phrase('Active') }}</option>
                        <option value="disabled" @selected($status === 'disabled')>{{ get_phrase('Disabled') }}</option>
                        <option value="primary" @selected($status === 'primary')>{{ get_phrase('Primary') }}</option>
                    </select>
                    <button type="submit" class="eBtn eBtn btn-secondary">{{ get_phrase('Filter') }}</button>
                    @if ($search !== '' || $status !== '')
                        <a href="{{ route('admin.admin') }}" class="eBtn eBtn btn-light">{{ get_phrase('Reset') }}</a>
                    @endif
                </div>
            </form>

            @if ($admins->count() > 0)
                <div class="dropdown">
                    <button class="eBtn-3 dropdown-toggle school-admin-directory__export" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-box-arrow-up" aria-hidden="true"></i>{{ get_phrase('Export') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                        <li><button type="button" class="dropdown-item" data-admin-export="pdf">{{ get_phrase('PDF') }}</button></li>
                        <li><button type="button" class="dropdown-item" data-admin-export="print">{{ get_phrase('Print') }}</button></li>
                    </ul>
                </div>
            @endif
        </div>

        @if ($admins->count() > 0)
            <div class="table-responsive school-admin-directory__desktop d-none d-md-block">
                <table class="table eTable school-admin-directory__table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ get_phrase('Admin') }}</th>
                            <th>{{ get_phrase('Contact') }}</th>
                            <th>{{ get_phrase('Role') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th class="text-end">{{ get_phrase('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            @php
                                $profile = $admin->profile_information;
                                $isPrimary = (int) $admin->school_role === 1;
                                $canManageTarget = $canManageAdmins && !$isPrimary && auth()->id() !== $admin->id;
                            @endphp
                            <tr>
                                <td>{{ $admins->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="school-admin-directory__identity">
                                        <img src="{{ $admin->photo_url }}" alt="" width="48" height="48" />
                                        <div>
                                            <strong>{{ $admin->name }}</strong>
                                            <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="school-admin-directory__contact">
                                    <span><i class="bi bi-telephone" aria-hidden="true"></i>{{ $profile['phone'] ?? get_phrase('Not provided') }}</span>
                                    <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $profile['address'] ?? get_phrase('Not provided') }}</span>
                                </td>
                                <td>
                                    @if ($isPrimary)
                                        <span class="eBadge ebg-soft-info"><i class="bi bi-shield-check" aria-hidden="true"></i>{{ get_phrase('Primary') }}</span>
                                    @else
                                        <span class="eBadge ebg-soft-info">{{ get_phrase('Secondary') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($admin->account_status === 'disable')
                                        <span class="eBadge ebg-soft-danger">{{ get_phrase('Disabled') }}</span>
                                    @else
                                        <span class="eBadge ebg-soft-success">{{ get_phrase('Active') }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="adminTable-action">
                                        <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">{{ get_phrase('Actions') }}</button>
                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <li><button class="dropdown-item" type="button" data-admin-modal="large" data-modal-url="{{ route('admin.admin.admin_profile', $admin) }}" data-modal-title="{{ get_phrase('Admin Profile') }}">{{ get_phrase('Profile') }}</button></li>
                                            @if ($canManageAdmins)
                                                <li><button class="dropdown-item" type="button" data-admin-modal="right" data-modal-url="{{ route('admin.open_edit_modal', $admin) }}" data-modal-title="{{ get_phrase('Edit Admin') }}">{{ get_phrase('Edit') }}</button></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.documents', $admin->id) }}">{{ get_phrase('Documents') }}</a></li>
                                            @endif
                                            @if ($canManageTarget)
                                                <li><a class="dropdown-item" href="{{ route('admin.admin.menu_permission', $admin) }}">{{ get_phrase('Permissions') }}</a></li>
                                                <li>
                                                    <form id="status-form-{{ $admin->id }}" method="POST" action="{{ route('admin.admin.status', $admin) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $admin->account_status === 'disable' ? 'enable' : 'disable' }}" />
                                                        <button type="button" class="dropdown-item" data-confirm-form="status-form-{{ $admin->id }}" data-confirm-text="{{ $admin->account_status === 'disable' ? get_phrase('Enable this admin account?') : get_phrase('Disable this admin account?') }}">
                                                            {{ $admin->account_status === 'disable' ? get_phrase('Enable') : get_phrase('Disable') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form id="delete-form-{{ $admin->id }}" method="POST" action="{{ route('admin.admin.delete', $admin) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item text-danger" data-confirm-form="delete-form-{{ $admin->id }}" data-confirm-text="{{ get_phrase('Delete this secondary admin permanently?') }}">{{ get_phrase('Delete') }}</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="school-admin-directory__mobile d-md-none">
                @foreach ($admins as $admin)
                    @php
                        $profile = $admin->profile_information;
                        $isPrimary = (int) $admin->school_role === 1;
                        $canManageTarget = $canManageAdmins && !$isPrimary && auth()->id() !== $admin->id;
                    @endphp
                    <article class="school-admin-directory__card">
                        <header>
                            <div class="school-admin-directory__identity">
                                <img src="{{ $admin->photo_url }}" alt="" width="52" height="52" />
                                <div><strong>{{ $admin->name }}</strong><a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a></div>
                            </div>
                            <span class="eBadge {{ $admin->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $admin->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span>
                        </header>
                        <div class="school-admin-directory__card-meta">
                            <span><i class="bi bi-shield-check" aria-hidden="true"></i>{{ $isPrimary ? get_phrase('Primary admin') : get_phrase('Secondary admin') }}</span>
                            <span><i class="bi bi-telephone" aria-hidden="true"></i>{{ $profile['phone'] ?? get_phrase('Not provided') }}</span>
                            <span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $profile['address'] ?? get_phrase('Not provided') }}</span>
                        </div>
                        <div class="school-admin-directory__card-actions">
                            <button type="button" data-admin-modal="large" data-modal-url="{{ route('admin.admin.admin_profile', $admin) }}" data-modal-title="{{ get_phrase('Admin Profile') }}">{{ get_phrase('Profile') }}</button>
                            @if ($canManageAdmins)
                                <button type="button" data-admin-modal="right" data-modal-url="{{ route('admin.open_edit_modal', $admin) }}" data-modal-title="{{ get_phrase('Edit Admin') }}">{{ get_phrase('Edit') }}</button>
                                <a href="{{ route('admin.documents', $admin->id) }}">{{ get_phrase('Documents') }}</a>
                            @endif
                            @if ($canManageTarget)
                                <a href="{{ route('admin.admin.menu_permission', $admin) }}">{{ get_phrase('Permissions') }}</a>
                                <button type="button" data-confirm-form="mobile-status-form-{{ $admin->id }}" data-confirm-text="{{ $admin->account_status === 'disable' ? get_phrase('Enable this admin account?') : get_phrase('Disable this admin account?') }}">{{ $admin->account_status === 'disable' ? get_phrase('Enable') : get_phrase('Disable') }}</button>
                                <form id="mobile-status-form-{{ $admin->id }}" method="POST" action="{{ route('admin.admin.status', $admin) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $admin->account_status === 'disable' ? 'enable' : 'disable' }}" /></form>
                                <button type="button" class="text-danger" data-confirm-form="mobile-delete-form-{{ $admin->id }}" data-confirm-text="{{ get_phrase('Delete this secondary admin permanently?') }}">{{ get_phrase('Delete') }}</button>
                                <form id="mobile-delete-form-{{ $admin->id }}" method="POST" action="{{ route('admin.admin.delete', $admin) }}">@csrf @method('DELETE')</form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="admin-tInfo-pagi school-admin-directory__pagination d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                <p class="admin-tInfo">{{ get_phrase('Showing') }} {{ $admins->firstItem() }} - {{ $admins->lastItem() }} {{ get_phrase('from') }} {{ $admins->total() }} {{ get_phrase('admins') }}</p>
                <div class="admin-pagi">{{ $admins->links() }}</div>
            </div>
        @else
            <div class="empty_box center school-admin-directory__empty">
                <img class="mb-3" width="150" src="{{ asset('assets/images/empty_box.png') }}" alt="" />
                @if ((int) ($summary->total ?? 0) === 0)
                    <h5>{{ get_phrase('No school admins yet') }}</h5>
                    <p>{{ get_phrase('Create a secondary admin to help manage this school.') }}</p>
                @else
                    <h5>{{ get_phrase('No matching admins') }}</h5>
                    <p>{{ get_phrase('Try changing your search or status filter.') }}</p>
                    <a href="{{ route('admin.admin') }}" class="eBtn eBtn btn-secondary">{{ get_phrase('Clear filters') }}</a>
                @endif
            </div>
        @endif
    </div>

    <section id="admin-directory-export" class="school-admin-directory__print-view" aria-hidden="true">
        <h1>{{ get_phrase('School Admin List') }}</h1>
        <table>
            <thead><tr><th>#</th><th>{{ get_phrase('Name') }}</th><th>{{ get_phrase('Email') }}</th><th>{{ get_phrase('Phone') }}</th><th>{{ get_phrase('Role') }}</th><th>{{ get_phrase('Status') }}</th></tr></thead>
            <tbody>
                @foreach ($admins as $admin)
                    <tr><td>{{ $admins->firstItem() + $loop->index }}</td><td>{{ $admin->name }}</td><td>{{ $admin->email }}</td><td>{{ $admin->profile_information['phone'] ?? get_phrase('Not provided') }}</td><td>{{ (int) $admin->school_role === 1 ? get_phrase('Primary') : get_phrase('Secondary') }}</td><td>{{ $admin->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <div class="modal fade" id="adminActionConfirmation" tabindex="-1" aria-labelledby="adminActionConfirmationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body school-admin-directory__confirm"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><h5 id="adminActionConfirmationLabel">{{ get_phrase('Are you sure?') }}</h5><p data-confirm-message></p><div><button type="button" class="eBtn eBtn btn-light" data-bs-dismiss="modal">{{ get_phrase('Cancel') }}</button><button type="button" class="eBtn eBtn btn-secondary" data-confirm-submit>{{ get_phrase('Confirm') }}</button></div></div></div></div>
    </div>
</div>

<script src="{{ asset('assets/js/school-admin-directory.js') }}"></script>
@endsection
