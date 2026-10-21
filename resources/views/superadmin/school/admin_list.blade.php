@extends('superadmin.navigation')

@section('content')

<div class="sa-al">
    <div class="sa-al-head">
        <div class="sa-al-head-row">
            <div>
                <h4>{{ get_phrase('Admin List') }}</h4>
                <div class="sa-bc">
                    <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a>
                    <span> / </span>
                    <a href="{{ route('superadmin.school.list') }}">{{ get_phrase('Schools') }}</a>
                    <span> / </span>
                    <span>{{ $school->title }}</span>
                    <span> / </span>
                    <span>{{ get_phrase('Admins') }}</span>
                </div>
            </div>
            <a href="{{ route('superadmin.school.list') }}" class="sa-al-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                {{ get_phrase('Back to Schools') }}
            </a>
        </div>
    </div>

    <!-- School Banner -->
    <div class="sa-al-banner">
        <div class="sa-al-banner-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6"/></svg>
        </div>
        <div>
            <div class="sa-al-banner-title">{{ $school->title }}</div>
            <div class="sa-al-banner-sub">{{ get_phrase('Managing admins for this school') }}</div>
            @if (!empty($school->tenant_id))
                <div class="sa-al-banner-domain">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    <a href="https://{{ $school->tenant_id }}.{{ get_school_domain_base() }}" target="_blank">{{ $school->tenant_id }}.{{ get_school_domain_base() }}</a>
                </div>
            @endif
        </div>
    </div>

    @php
        $total = $admins->count();
        $activeCount = $admins->filter(fn($a) => $a->account_status !== 'disable' && $a->status != 0)->count();
        $disabledCount = $total - $activeCount;
    @endphp

    <div class="sa-al-stats stagger">
        <div class="sa-al-stat">
            <div class="sa-al-stat-icon si-total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div>
                <div class="sa-al-stat-label">{{ get_phrase('Total') }}</div>
                <div class="sa-al-stat-num n-total">{{ $total }}</div>
            </div>
        </div>
        <div class="sa-al-stat">
            <div class="sa-al-stat-icon si-active">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="sa-al-stat-label">{{ get_phrase('Active') }}</div>
                <div class="sa-al-stat-num n-active">{{ $activeCount }}</div>
            </div>
        </div>
        <div class="sa-al-stat">
            <div class="sa-al-stat-icon si-disabled">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
            </div>
            <div>
                <div class="sa-al-stat-label">{{ get_phrase('Disabled') }}</div>
                <div class="sa-al-stat-num n-disabled">{{ $disabledCount }}</div>
            </div>
        </div>
    </div>

    <div class="sa-al-card">
        @if ($admins->count() > 0)
            <div style="overflow-x:auto; position:relative;">
                <table class="sa-al-tbl">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>{{ get_phrase('Admin') }}</th>
                            <th>{{ get_phrase('Email') }}</th>
                            <th>{{ get_phrase('Info') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th style="width:100px;">{{ get_phrase('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $key => $admin)
                            @php
                                $info = json_decode($admin->user_information ?? '{}', true) ?: [];
                                $photo = $info['photo'] ?? '';
                                $hasPhoto = $photo && file_exists(public_path('assets/uploads/user-images/' . $photo));
                                $colorIdx = ($key % 4) + 1;
                                $initial = strtoupper(substr($admin->name, 0, 1));
                                $isDisabled = $admin->account_status === 'disable' || $admin->status == 0;
                            @endphp
                            <tr>
                                <td style="color:#94a3b8;font-size:12px;font-weight:600;">{{ $key + 1 }}</td>
                                <td>
                                    <div class="sa-al-admin">
                                        <div class="sa-al-avatar">
                                            @if ($hasPhoto)
                                                <img src="{{ asset('assets/uploads/user-images/' . $photo) }}" alt="{{ $admin->name }}">
                                            @else
                                                <div class="sa-al-avatar-fallback av-{{ $colorIdx }}">{{ $initial }}</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="sa-al-admin-name">{{ $admin->name }}</div>
                                            <div class="sa-al-admin-role">{{ get_phrase('School Admin') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="sa-al-email"><a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a></td>
                                <td>
                                    <div class="sa-al-info">
                                        <p><span>{{ get_phrase('Phone') }}:</span> {{ $info['phone'] ?? '—' }}</p>
                                        <p><span>{{ get_phrase('Address') }}:</span> {{ \Illuminate\Support\Str::limit($info['address'] ?? '', 25) ?: '—' }}</p>
                                    </div>
                                </td>
                                <td>
                                    @if ($isDisabled)
                                        <span class="sa-al-badge sa-al-badge-disabled">{{ get_phrase('Disabled') }}</span>
                                    @else
                                        <span class="sa-al-badge sa-al-badge-active">{{ get_phrase('Active') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="adminTable-action">
                                        <button type="button" class="sa-al-actions-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ get_phrase('Actions') }}
                                            <svg width="10" height="10" viewBox="0 0 448 512" fill="currentColor"><path d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"/></svg>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.edit.admin', ['tenant_id' => $school->tenant_id, 'admin_id' => $admin->id]) }}">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    {{ get_phrase('Edit') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" onclick="showChangePasswordModal('{{ $school->tenant_id }}', '{{ $admin->id }}')">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                                    {{ get_phrase('Change Password') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.school.login_as', ['tenant_id' => $school->tenant_id, 'admin_id' => $admin->id]) }}" target="_blank" rel="noopener">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                                                    {{ get_phrase('Login as Admin') }}
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider" style="margin:4px 0;border-color:#e8f5e9;"></li>
                                            @if ($isDisabled)
                                                <li>
                                                    <a class="dropdown-item text-success" href="javascript:;" onclick="confirmModal('{{ route('superadmin.school.admin_status_update', ['tenant_id' => $school->tenant_id, 'admin_id' => $admin->id, 'action' => 'enable']) }}', 'undefined')">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                                        {{ get_phrase('Enable') }}
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmModal('{{ route('superadmin.school.admin_status_update', ['tenant_id' => $school->tenant_id, 'admin_id' => $admin->id, 'action' => 'disable']) }}', 'undefined')">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:4px;"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                                                        {{ get_phrase('Disable') }}
                                                    </a>
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
        @else
            <div class="sa-al-empty">
                <div class="sa-al-empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <h5>{{ get_phrase('No admins found') }}</h5>
                <p>{{ get_phrase('This school does not have any admin accounts yet.') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Change Password Modal -->
<div class="sa-al-modal-overlay" id="changePasswordModal">
    <div class="sa-al-modal">
        <div class="sa-al-modal-head">
            <h5>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" style="vertical-align:-3px;margin-right:6px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                {{ get_phrase('Change Password') }}
            </h5>
            <button class="sa-al-modal-close" onclick="closePasswordModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('superadmin.admin_list') }}" method="POST" id="changePasswordForm">
            @csrf
            <input type="hidden" name="user_id" id="cp_user_id" value="">
            <input type="hidden" name="tenant_id" id="cp_tenant_id" value="">
            <div class="sa-al-modal-body">
                <div class="sa-al-modal-field">
                    <label>{{ get_phrase('New Password') }}</label>
                    <input type="password" name="password" id="new_password" minlength="8" placeholder="{{ get_phrase('Enter a new password') }}" required>
                </div>
                <div class="sa-al-modal-actions">
                    <button type="button" class="sa-al-modal-btn sa-al-modal-btn-ghost" onclick="closePasswordModal()">{{ get_phrase('Cancel') }}</button>
                    <button type="submit" class="sa-al-modal-btn sa-al-modal-btn-primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        {{ get_phrase('Update Password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.sa-al-tbl').forEach(function(tbl) {
    var wrap = tbl.parentElement;
    if (!wrap || !wrap.style.overflowX) return;
    var origX = wrap.style.overflowX, origY = wrap.style.overflowY;
    wrap.addEventListener('show.bs.dropdown', function() {
        this.style.overflowX = 'visible';
        this.style.overflowY = 'visible';
    });
    wrap.addEventListener('hide.bs.dropdown', function() {
        this.style.overflowX = origX;
        this.style.overflowY = origY;
    });
});

function showChangePasswordModal(tenantId, adminId) {
    document.getElementById('cp_user_id').value = adminId;
    document.getElementById('cp_tenant_id').value = tenantId;
    document.getElementById('new_password').value = '';
    document.getElementById('changePasswordModal').classList.add('show');
}
function closePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('show');
}
document.getElementById('changePasswordModal').addEventListener('click', function(e) {
    if (e.target === this) closePasswordModal();
});
</script>
@endsection
