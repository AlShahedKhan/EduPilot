@extends('admin.navigation')

@section('content')
<div class="school-teacher-directory" data-teacher-directory>
    <div class="mainSection-title">
        <div class="row"><div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Teachers') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Users') }}</a></li>
                        <li><a href="{{ route('admin.teacher') }}">{{ get_phrase('Teachers') }}</a></li>
                    </ul>
                </div>
                @if ($canManageTeachers)
                    <button type="button" class="export_btn school-teacher-directory__create" data-teacher-modal="right"
                        data-modal-url="{{ route('admin.teacher.open_modal') }}" data-modal-title="{{ get_phrase('Create Teacher') }}">
                        <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ get_phrase('Create Teacher') }}
                    </button>
                @endif
            </div>
        </div></div>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning school-teacher-directory__alert" role="alert"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i><span>{{ session('warning') }}</span></div>
    @endif

    <nav class="school-teacher-directory__summary" aria-label="{{ get_phrase('Teacher status filters') }}">
        <a href="{{ route('admin.teacher', array_filter(['search' => $search, 'department' => $departmentId ?: null])) }}" class="school-teacher-directory__summary-card {{ $status === '' ? 'is-active' : '' }}" @if ($status === '') aria-current="page" @endif>
            <span class="school-teacher-directory__summary-icon"><i class="bi bi-people" aria-hidden="true"></i></span><span><small>{{ get_phrase('All teachers') }}</small><strong>{{ (int) ($summary->total ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.teacher', array_filter(['search' => $search, 'department' => $departmentId ?: null, 'status' => 'active'])) }}" class="school-teacher-directory__summary-card {{ $status === 'active' ? 'is-active' : '' }}" @if ($status === 'active') aria-current="page" @endif>
            <span class="school-teacher-directory__summary-icon school-teacher-directory__summary-icon--active"><i class="bi bi-person-check" aria-hidden="true"></i></span><span><small>{{ get_phrase('Active') }}</small><strong>{{ (int) ($summary->active ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.teacher', array_filter(['search' => $search, 'department' => $departmentId ?: null, 'status' => 'disabled'])) }}" class="school-teacher-directory__summary-card {{ $status === 'disabled' ? 'is-active' : '' }}" @if ($status === 'disabled') aria-current="page" @endif>
            <span class="school-teacher-directory__summary-icon school-teacher-directory__summary-icon--disabled"><i class="bi bi-person-x" aria-hidden="true"></i></span><span><small>{{ get_phrase('Disabled') }}</small><strong>{{ (int) ($summary->disabled ?? 0) }}</strong></span>
        </a>
        <a href="{{ route('admin.teacher', array_filter(['search' => $search, 'status' => 'unassigned'])) }}" class="school-teacher-directory__summary-card {{ $status === 'unassigned' ? 'is-active' : '' }}" @if ($status === 'unassigned') aria-current="page" @endif>
            <span class="school-teacher-directory__summary-icon school-teacher-directory__summary-icon--unassigned"><i class="bi bi-building-exclamation" aria-hidden="true"></i></span><span><small>{{ get_phrase('Unassigned') }}</small><strong>{{ (int) ($summary->unassigned ?? 0) }}</strong></span>
        </a>
    </nav>

    <div class="eSection-wrap-2 school-teacher-directory__panel">
        <div class="school-teacher-directory__toolbar">
            <form method="GET" action="{{ route('admin.teacher') }}" class="school-teacher-directory__filters">
                <div class="search-input school-teacher-directory__search d-flex align-items-center">
                    <span aria-hidden="true"><i class="bi bi-search"></i></span>
                    <label for="teacher-search" class="visually-hidden">{{ get_phrase('Search teachers') }}</label>
                    <input id="teacher-search" type="search" name="search" value="{{ $search }}" placeholder="{{ get_phrase('Search name, email, or designation') }}" class="form-control" />
                </div>
                <div class="school-teacher-directory__filter-actions">
                    <label for="teacher-status" class="visually-hidden">{{ get_phrase('Filter by status') }}</label>
                    <select id="teacher-status" name="status" class="form-select eForm-select">
                        <option value="">{{ get_phrase('All statuses') }}</option>
                        <option value="active" @selected($status === 'active')>{{ get_phrase('Active') }}</option>
                        <option value="disabled" @selected($status === 'disabled')>{{ get_phrase('Disabled') }}</option>
                        <option value="unassigned" @selected($status === 'unassigned')>{{ get_phrase('Unassigned') }}</option>
                    </select>
                    <label for="teacher-department" class="visually-hidden">{{ get_phrase('Filter by department') }}</label>
                    <select id="teacher-department" name="department" class="form-select eForm-select">
                        <option value="">{{ get_phrase('All departments') }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected($departmentId === $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="eBtn eBtn btn-secondary">{{ get_phrase('Filter') }}</button>
                    @if ($search !== '' || $status !== '' || $departmentId > 0)<a href="{{ route('admin.teacher') }}" class="eBtn eBtn btn-light">{{ get_phrase('Reset') }}</a>@endif
                </div>
            </form>

            @if ($teachers->count() > 0)
                <div class="dropdown">
                    <button class="eBtn-3 dropdown-toggle school-teacher-directory__export" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-box-arrow-up" aria-hidden="true"></i>{{ get_phrase('Export') }}</button>
                    <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2">
                        <li><button type="button" class="dropdown-item" data-teacher-export="pdf">{{ get_phrase('PDF') }}</button></li>
                        <li><button type="button" class="dropdown-item" data-teacher-export="print">{{ get_phrase('Print') }}</button></li>
                    </ul>
                </div>
            @endif
        </div>

        @if ($teachers->count() > 0)
            <div class="table-responsive school-teacher-directory__desktop d-none d-md-block">
                <table class="table eTable school-teacher-directory__table">
                    <thead><tr><th>#</th><th>{{ get_phrase('Teacher') }}</th><th>{{ get_phrase('Contact') }}</th><th>{{ get_phrase('Department') }}</th><th>{{ get_phrase('Status') }}</th><th class="text-end">{{ get_phrase('Options') }}</th></tr></thead>
                    <tbody>
                    @foreach ($teachers as $teacher)
                        @php $profile = $teacher->profile_information; @endphp
                        <tr>
                            <td>{{ $teachers->firstItem() + $loop->index }}</td>
                            <td><div class="school-teacher-directory__identity"><img src="{{ $teacher->photo_url }}" alt="" width="48" height="48" /><div><strong>{{ $teacher->name }}</strong><a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a><small>{{ $teacher->designation ?: get_phrase('No designation') }}</small></div></div></td>
                            <td class="school-teacher-directory__contact"><span><i class="bi bi-telephone" aria-hidden="true"></i>{{ $profile['phone'] ?? get_phrase('Not provided') }}</span><span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $profile['address'] ?? get_phrase('Not provided') }}</span></td>
                            <td><span class="school-teacher-directory__department"><i class="bi bi-building" aria-hidden="true"></i>{{ $teacher->department_name ?: get_phrase('Unassigned') }}</span></td>
                            <td><span class="eBadge {{ $teacher->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $teacher->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></td>
                            <td class="text-end"><div class="adminTable-action">
                                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">{{ get_phrase('Actions') }}</button>
                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                    <li><button class="dropdown-item" type="button" data-teacher-modal="large" data-modal-url="{{ route('admin.teacher.teacher_profile', $teacher) }}" data-modal-title="{{ get_phrase('Teacher Profile') }}">{{ get_phrase('Profile') }}</button></li>
                                    @if ($canManageTeachers)
                                        <li><button class="dropdown-item" type="button" data-teacher-modal="right" data-modal-url="{{ route('admin.teacher_edit_modal', $teacher) }}" data-modal-title="{{ get_phrase('Edit Teacher') }}">{{ get_phrase('Edit') }}</button></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.teacher.documents', $teacher) }}">{{ get_phrase('Documents') }}</a></li>
                                        <li><form id="teacher-status-{{ $teacher->id }}" method="POST" action="{{ route('admin.teacher.status', $teacher) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $teacher->account_status === 'disable' ? 'enable' : 'disable' }}" /><button type="button" class="dropdown-item" data-confirm-form="teacher-status-{{ $teacher->id }}" data-confirm-text="{{ $teacher->account_status === 'disable' ? get_phrase('Enable this teacher account?') : get_phrase('Disable this teacher account?') }}">{{ $teacher->account_status === 'disable' ? get_phrase('Enable') : get_phrase('Disable') }}</button></form></li>
                                        <li><form id="teacher-delete-{{ $teacher->id }}" method="POST" action="{{ route('admin.teacher.delete', $teacher) }}">@csrf @method('DELETE')<button type="button" class="dropdown-item text-danger" data-confirm-form="teacher-delete-{{ $teacher->id }}" data-confirm-text="{{ get_phrase('Delete this teacher permanently? Teachers with linked academic records cannot be deleted.') }}">{{ get_phrase('Delete') }}</button></form></li>
                                    @endif
                                </ul>
                            </div></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="school-teacher-directory__mobile d-md-none">
                @foreach ($teachers as $teacher)
                    @php $profile = $teacher->profile_information; @endphp
                    <article class="school-teacher-directory__card">
                        <header><div class="school-teacher-directory__identity"><img src="{{ $teacher->photo_url }}" alt="" width="52" height="52" /><div><strong>{{ $teacher->name }}</strong><a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a><small>{{ $teacher->designation ?: get_phrase('No designation') }}</small></div></div><span class="eBadge {{ $teacher->account_status === 'disable' ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $teacher->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</span></header>
                        <div class="school-teacher-directory__card-meta"><span><i class="bi bi-building" aria-hidden="true"></i>{{ $teacher->department_name ?: get_phrase('Unassigned') }}</span><span><i class="bi bi-telephone" aria-hidden="true"></i>{{ $profile['phone'] ?? get_phrase('Not provided') }}</span><span><i class="bi bi-geo-alt" aria-hidden="true"></i>{{ $profile['address'] ?? get_phrase('Not provided') }}</span></div>
                        <div class="school-teacher-directory__card-actions">
                            <button type="button" data-teacher-modal="large" data-modal-url="{{ route('admin.teacher.teacher_profile', $teacher) }}" data-modal-title="{{ get_phrase('Teacher Profile') }}">{{ get_phrase('Profile') }}</button>
                            @if ($canManageTeachers)
                                <button type="button" data-teacher-modal="right" data-modal-url="{{ route('admin.teacher_edit_modal', $teacher) }}" data-modal-title="{{ get_phrase('Edit Teacher') }}">{{ get_phrase('Edit') }}</button>
                                <a href="{{ route('admin.teacher.documents', $teacher) }}">{{ get_phrase('Documents') }}</a>
                                <button type="button" data-confirm-form="mobile-teacher-status-{{ $teacher->id }}" data-confirm-text="{{ $teacher->account_status === 'disable' ? get_phrase('Enable this teacher account?') : get_phrase('Disable this teacher account?') }}">{{ $teacher->account_status === 'disable' ? get_phrase('Enable') : get_phrase('Disable') }}</button>
                                <form id="mobile-teacher-status-{{ $teacher->id }}" method="POST" action="{{ route('admin.teacher.status', $teacher) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="{{ $teacher->account_status === 'disable' ? 'enable' : 'disable' }}" /></form>
                                <button type="button" class="text-danger" data-confirm-form="mobile-teacher-delete-{{ $teacher->id }}" data-confirm-text="{{ get_phrase('Delete this teacher permanently? Teachers with linked academic records cannot be deleted.') }}">{{ get_phrase('Delete') }}</button>
                                <form id="mobile-teacher-delete-{{ $teacher->id }}" method="POST" action="{{ route('admin.teacher.delete', $teacher) }}">@csrf @method('DELETE')</form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="admin-tInfo-pagi school-teacher-directory__pagination d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15"><p class="admin-tInfo">{{ get_phrase('Showing') }} {{ $teachers->firstItem() }} - {{ $teachers->lastItem() }} {{ get_phrase('from') }} {{ $teachers->total() }} {{ get_phrase('teachers') }}</p><div class="admin-pagi">{{ $teachers->links() }}</div></div>
        @else
            <div class="empty_box center school-teacher-directory__empty"><img class="mb-3" width="150" src="{{ asset('assets/images/empty_box.png') }}" alt="" />
                @if ((int) ($summary->total ?? 0) === 0)<h5>{{ get_phrase('No teachers yet') }}</h5><p>{{ get_phrase('Create a teacher to begin building the school directory.') }}</p>
                @else<h5>{{ get_phrase('No matching teachers') }}</h5><p>{{ get_phrase('Try changing your search or filters.') }}</p><a href="{{ route('admin.teacher') }}" class="eBtn eBtn btn-secondary">{{ get_phrase('Clear filters') }}</a>@endif
            </div>
        @endif
    </div>

    <section id="teacher-directory-export" class="school-teacher-directory__print-view" aria-hidden="true">
        <h1>{{ get_phrase('Teacher List') }}</h1><table><thead><tr><th>#</th><th>{{ get_phrase('Name') }}</th><th>{{ get_phrase('Email') }}</th><th>{{ get_phrase('Phone') }}</th><th>{{ get_phrase('Department') }}</th><th>{{ get_phrase('Designation') }}</th><th>{{ get_phrase('Status') }}</th></tr></thead><tbody>
        @foreach ($teachers as $teacher)<tr><td>{{ $teachers->firstItem() + $loop->index }}</td><td>{{ $teacher->name }}</td><td>{{ $teacher->email }}</td><td>{{ $teacher->profile_information['phone'] ?? get_phrase('Not provided') }}</td><td>{{ $teacher->department_name ?: get_phrase('Unassigned') }}</td><td>{{ $teacher->designation ?: get_phrase('Not provided') }}</td><td>{{ $teacher->account_status === 'disable' ? get_phrase('Disabled') : get_phrase('Active') }}</td></tr>@endforeach
        </tbody></table>
    </section>

    <div class="modal fade" id="teacherActionConfirmation" tabindex="-1" aria-labelledby="teacherActionConfirmationLabel" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body school-teacher-directory__confirm"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><h5 id="teacherActionConfirmationLabel">{{ get_phrase('Are you sure?') }}</h5><p data-confirm-message></p><div><button type="button" class="eBtn eBtn btn-light" data-bs-dismiss="modal">{{ get_phrase('Cancel') }}</button><button type="button" class="eBtn eBtn btn-secondary" data-confirm-submit>{{ get_phrase('Confirm') }}</button></div></div></div></div></div>
</div>

<script src="{{ asset('assets/js/school-teacher-directory.js') }}"></script>
@endsection
