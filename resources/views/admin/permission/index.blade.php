@extends('admin.navigation')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/school-teacher-permissions.css') }}" />
<div class="school-permission-page" data-permission-page data-sections-url="{{ route('admin.teacher.permission.sections', ['class' => '__CLASS__']) }}">
    <div class="mainSection-title"><div class="row"><div class="col-12"><div class="d-flex justify-content-between align-items-center flex-wrap gr-15"><div class="d-flex flex-column"><h4>{{ get_phrase('Teacher Permissions') }}</h4><ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li><li><a href="{{ route('admin.teacher.permission') }}">{{ get_phrase('Teacher Permissions') }}</a></li></ul></div></div></div></div></div>

    <div class="school-permission-page__summary">
        @foreach ([['Teachers', 'teachers', 'bi-people'], ['Class Access', 'class_access', 'bi-journal-check'], ['Marks Access', 'marks', 'bi-award'], ['Attendance Access', 'attendance', 'bi-calendar-check']] as [$label, $key, $icon])
            <div class="school-permission-page__summary-card"><span><i class="bi {{ $icon }}" aria-hidden="true"></i></span><div><small>{{ get_phrase($label) }}</small><strong>{{ (int) $summary->{$key} }}</strong></div></div>
        @endforeach
    </div>

    <div class="eSection-wrap-2 school-permission-page__panel">
        <form method="GET" action="{{ route('admin.teacher.permission') }}" class="school-permission-page__filters">
            <div><label for="permission-class" class="eForm-label">{{ get_phrase('Class') }}</label><select id="permission-class" name="class_id" class="form-select eForm-select" data-permission-class><option value="">{{ get_phrase('Select class') }}</option>@foreach ($classes as $class)<option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>@endforeach</select></div>
            <div><label for="permission-section" class="eForm-label">{{ get_phrase('Section') }}</label><select id="permission-section" name="section_id" class="form-select eForm-select" data-permission-section @disabled($classId === 0)><option value="">{{ get_phrase('Select section') }}</option>@foreach ($sections as $section)<option value="{{ $section->id }}" @selected($sectionId === $section->id)>{{ $section->name }}</option>@endforeach</select></div>
            <div><label for="permission-search" class="eForm-label">{{ get_phrase('Teacher') }}</label><input id="permission-search" type="search" name="search" value="{{ $search }}" class="form-control eForm-control" placeholder="{{ get_phrase('Search name or email') }}" /></div>
            <div><label for="permission-status" class="eForm-label">{{ get_phrase('Status') }}</label><select id="permission-status" name="status" class="form-select eForm-select"><option value="">{{ get_phrase('All statuses') }}</option><option value="active" @selected($status === 'active')>{{ get_phrase('Active') }}</option><option value="disabled" @selected($status === 'disabled')>{{ get_phrase('Disabled') }}</option></select></div>
            <div><label for="permission-filter" class="eForm-label">{{ get_phrase('Permission') }}</label><select id="permission-filter" name="permission" class="form-select eForm-select"><option value="">{{ get_phrase('All permissions') }}</option><option value="class_access" @selected($permissionFilter === 'class_access')>{{ get_phrase('Class Access') }}</option><option value="marks" @selected($permissionFilter === 'marks')>{{ get_phrase('Marks') }}</option><option value="attendance" @selected($permissionFilter === 'attendance')>{{ get_phrase('Attendance') }}</option><option value="none" @selected($permissionFilter === 'none')>{{ get_phrase('No access') }}</option></select></div>
            <div class="school-permission-page__filter-actions"><button type="submit" class="eBtn eBtn btn-secondary">{{ get_phrase('Apply') }}</button>@if ($classId || $sectionId || $search || $status || $permissionFilter)<a href="{{ route('admin.teacher.permission') }}" class="eBtn eBtn btn-light">{{ get_phrase('Reset') }}</a>@endif</div>
        </form>

        @if ($classId === 0 || $sectionId === 0)
            <div class="school-permission-page__empty"><i class="bi bi-sliders" aria-hidden="true"></i><h5>{{ get_phrase('Select a class and section') }}</h5><p>{{ get_phrase('Choose both fields to manage teacher access for that classroom.') }}</p></div>
        @elseif ($teachers->count() === 0)
            <div class="school-permission-page__empty"><img width="130" src="{{ asset('assets/images/empty_box.png') }}" alt="" /><h5>{{ get_phrase('No matching teachers') }}</h5><p>{{ get_phrase('Try changing the teacher filters.') }}</p></div>
        @else
            <form method="POST" action="{{ route('admin.teacher.permission.update') }}" data-permission-form>@csrf @method('PUT')
                <input type="hidden" name="class_id" value="{{ $classId }}" /><input type="hidden" name="section_id" value="{{ $sectionId }}" />
                <div data-permission-values>
                    @foreach ($teachers as $teacher)
                        @if ($teacher->account_status !== 'disable')
                            @php $permission = $teacher->teacherPermissions->first(); @endphp
                            <input type="hidden" name="permissions[{{ $teacher->id }}][class_access]" value="{{ $permission ? 1 : 0 }}" data-permission-value="{{ $teacher->id }}:class_access" />
                            <input type="hidden" name="permissions[{{ $teacher->id }}][marks]" value="{{ $permission?->marks ? 1 : 0 }}" data-permission-value="{{ $teacher->id }}:marks" />
                            <input type="hidden" name="permissions[{{ $teacher->id }}][attendance]" value="{{ $permission?->attendance ? 1 : 0 }}" data-permission-value="{{ $teacher->id }}:attendance" />
                        @endif
                    @endforeach
                </div>
                <div class="table-responsive school-permission-page__desktop d-none d-md-block"><table class="table eTable"><thead><tr><th>{{ get_phrase('Teacher') }}</th><th>{{ get_phrase('Status') }}</th><th>{{ get_phrase('Class Access') }}</th><th>{{ get_phrase('Marks') }}</th><th>{{ get_phrase('Attendance') }}</th></tr></thead><tbody>
                    @foreach ($teachers as $teacher)@php $permission = $teacher->teacherPermissions->first(); $disabled = $teacher->account_status === 'disable'; @endphp
                        <tr><td><strong>{{ $teacher->name }}</strong><span class="d-block text-muted">{{ $teacher->email }}</span></td><td><span class="eBadge {{ $disabled ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $disabled ? get_phrase('Disabled') : get_phrase('Active') }}</span></td>
                        @foreach (['class_access' => (bool) $permission, 'marks' => (bool) $permission?->marks, 'attendance' => (bool) $permission?->attendance] as $key => $checked)<td><label class="school-permission-page__switch"><input type="checkbox" data-permission-toggle data-teacher-id="{{ $teacher->id }}" data-permission="{{ $key }}" @checked($checked) @disabled(!$canManagePermissions || $disabled || (!$permission && $key !== 'class_access')) /><span></span><span class="visually-hidden">{{ $teacher->name }} {{ get_phrase(str_replace('_', ' ', $key)) }}</span></label></td>@endforeach
                        </tr>
                    @endforeach
                </tbody></table></div>
                <div class="school-permission-page__mobile d-md-none">@foreach ($teachers as $teacher)@php $permission = $teacher->teacherPermissions->first(); $disabled = $teacher->account_status === 'disable'; @endphp<article class="school-permission-page__card"><header><div><strong>{{ $teacher->name }}</strong><span>{{ $teacher->email }}</span></div><span class="eBadge {{ $disabled ? 'ebg-soft-danger' : 'ebg-soft-success' }}">{{ $disabled ? get_phrase('Disabled') : get_phrase('Active') }}</span></header>@foreach (['class_access' => (bool) $permission, 'marks' => (bool) $permission?->marks, 'attendance' => (bool) $permission?->attendance] as $key => $checked)<label class="school-permission-page__mobile-row"><span>{{ get_phrase(str_replace('_', ' ', $key)) }}</span><span class="school-permission-page__switch"><input type="checkbox" data-permission-toggle data-teacher-id="{{ $teacher->id }}" data-permission="{{ $key }}" @checked($checked) @disabled(!$canManagePermissions || $disabled || (!$permission && $key !== 'class_access')) /><span></span></span></label>@endforeach</article>@endforeach</div>
                <div class="school-permission-page__footer"><div>{{ $teachers->links() }}</div>@if ($canManagePermissions)<button type="submit" class="btn-form">{{ get_phrase('Save Changes') }}</button>@else<span class="text-muted">{{ get_phrase('Only the primary administrator can change permissions.') }}</span>@endif</div>
            </form>
        @endif
    </div>
</div>
<script src="{{ asset('assets/js/school-teacher-permissions.js') }}"></script>
@endsection
