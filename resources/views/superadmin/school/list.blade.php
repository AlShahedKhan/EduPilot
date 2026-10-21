@extends('superadmin.navigation')

@section('content')

    <div class="sa-sl">
        <div class="sa-sl-head">
            <div class="sa-sl-head-row">
                <div>
                    <h4>{{ get_phrase('School List') }}</h4>
                    <div class="sa-bc">
                        <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a> /
                        {{ get_phrase('Schools') }} / {{ get_phrase('School List') }}
                    </div>
                </div>
                <a href="{{ route('superadmin.school.add') }}" class="sa-sl-add">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    {{ get_phrase('Add School') }}
                </a>
            </div>
        </div>

        @php
            $total = $schools->total();
            $activeCount = \App\Models\Tenant::where('data->status', '1')->count();
            $pendingCount = \App\Models\Tenant::where('data->status', '2')->count();
            $inactiveCount = \App\Models\Tenant::where('data->status', '0')->count();
        @endphp

        <div class="sa-sl-stats stagger">
            <div class="sa-sl-stat">
                <div class="sa-sl-stat-icon si-total">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6" />
                    </svg>
                </div>
                <div>
                    <div class="sa-sl-stat-label">{{ get_phrase('Total') }}</div>
                    <div class="sa-sl-stat-num n-total">{{ $total }}</div>
                </div>
            </div>
            <div class="sa-sl-stat">
                <div class="sa-sl-stat-icon si-active">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                </div>
                <div>
                    <div class="sa-sl-stat-label">{{ get_phrase('Active') }}</div>
                    <div class="sa-sl-stat-num n-active">{{ $activeCount }}</div>
                </div>
            </div>
            <div class="sa-sl-stat">
                <div class="sa-sl-stat-icon si-pending">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div>
                    <div class="sa-sl-stat-label">{{ get_phrase('Pending') }}</div>
                    <div class="sa-sl-stat-num n-pending">{{ $pendingCount }}</div>
                </div>
            </div>
            <div class="sa-sl-stat">
                <div class="sa-sl-stat-icon si-inactive">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M15 9l-6 6M9 9l6 6" />
                    </svg>
                </div>
                <div>
                    <div class="sa-sl-stat-label">{{ get_phrase('Inactive') }}</div>
                    <div class="sa-sl-stat-num n-inactive">{{ $inactiveCount }}</div>
                </div>
            </div>
        </div>

        <div class="sa-sl-card">
            <div class="sa-sl-toolbar">
                <div class="sa-sl-toolbar-left">
                    <form method="GET" action="{{ route('superadmin.school.list') }}"
                        style="display:flex;gap:8px;align-items:center;">
                        <div class="sa-sl-search">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <circle cx="11" cy="11" r="8" />
                                <path d="M21 21l-4.35-4.35" />
                            </svg>
                            <input type="text" name="search" placeholder="{{ get_phrase('Search school name...') }}"
                                value="{{ $search }}">
                        </div>
                        <button type="submit" class="sa-sl-btn sa-sl-btn-ghost">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z" />
                            </svg>
                            {{ get_phrase('Filter') }}
                        </button>
                    </form>
                </div>
                <div class="sa-sl-toolbar-right">
                    <button class="sa-sl-btn sa-sl-btn-ghost" onclick="Export()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                        </svg>
                        {{ get_phrase('Export PDF') }}
                    </button>
                    <button class="sa-sl-btn sa-sl-btn-ghost" onclick="printableDiv('school_list_print')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="6 9 6 2 18 2 18 9" />
                            <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        {{ get_phrase('Print') }}
                    </button>
                </div>
            </div>

            @if (count($schools) > 0)
                <div style="overflow-x:auto; position:relative;">
                    <table class="sa-sl-tbl">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>{{ get_phrase('School') }}</th>
                                <th>{{ get_phrase('Address') }}</th>
                                <th>{{ get_phrase('Phone') }}</th>
                                <th>{{ get_phrase('BTCL Info') }}</th>
                                <th>{{ get_phrase('Info') }}</th>
                                <th>{{ get_phrase('Status') }}</th>
                                <th style="width:100px;">{{ get_phrase('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schools as $key => $school)
                                <tr>
                                    <td style="color:#94a3b8;font-size:12px;font-weight:600;">
                                        {{ $schools->firstItem() + $key }}</td>
                                    <td>
                                        <div class="sa-sl-school">
                                            <div class="sa-sl-school-avatar">
                                                {{ strtoupper(substr($school->title, 0, 1)) }}</div>
                                            <div>
                                                <div class="sa-sl-school-name">{{ $school->title }}</div>
                                                @if (!empty($school->tenant_id))
                                                    <div class="sa-sl-school-url">
                                                        <a href="https://{{ $school->tenant_id }}.{{ get_school_domain_base() }}"
                                                            target="_blank">
                                                            {{ $school->tenant_id }}.{{ get_school_domain_base() }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="sa-sl-addr">{{ $school->address ?: '—' }}</td>
                                    <td class="sa-sl-phone">{{ $school->phone ?: '—' }}</td>
                                    <td>
                                        @if (!empty($school->btcl_email) || !empty($school->btcl_phone) || !empty($school->btcl_password))
                                            <div class="sa-sl-btcl">
                                                <div class="sa-sl-btcl-row"><span>E:</span> {{ $school->btcl_email ?: '—' }}</div>
                                                <div class="sa-sl-btcl-row"><span>P:</span> {{ $school->btcl_phone ?: '—' }}</div>
                                                <div class="sa-sl-btcl-row"><span>PW:</span> {{ $school->btcl_password ?: '—' }}</div>
                                            </div>
                                        @else
                                            <span style="color:#94a3b8;">—</span>
                                        @endif
                                    </td>
                                    <td class="sa-sl-info" title="{{ e($school->school_info) }}">
                                        {{ \Illuminate\Support\Str::limit($school->school_info, 40) ?: '—' }}</td>
                                    <td>
                                        @if ($school->status == '1')
                                            <span class="sa-sl-badge sa-sl-badge-active">{{ get_phrase('Active') }}</span>
                                        @elseif ($school->status == '2')
                                            <span
                                                class="sa-sl-badge sa-sl-badge-pending">{{ get_phrase('Pending') }}</span>
                                        @else
                                            <span
                                                class="sa-sl-badge sa-sl-badge-inactive">{{ get_phrase('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="adminTable-action">
                                            <button type="button" class="sa-sl-actions-btn dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ get_phrase('Actions') }}
                                                <svg width="10" height="10" viewBox="0 0 448 512"
                                                    fill="currentColor">
                                                    <path
                                                        d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
                                                </svg>
                                            </button>
                                            <ul
                                                class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                <li>
                                                    @if ($school->status == '1')
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="confirmModal('{{ route('superadmin.school.status_update', ['id' => $school->id, 'status' => '0']) }}', 'undefined');">{{ get_phrase('Deactivate') }}</a>
                                                    @else
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="confirmModal('{{ route('superadmin.school.status_update', ['id' => $school->id, 'status' => '1']) }}', 'undefined');">{{ get_phrase('Activate') }}</a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('superadmin.edit.school', ['id' => $school->id]) }}">{{ get_phrase('Edit') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('superadmin.school.admin_list', ['id' => $school->id]) }}">{{ get_phrase('Admin List') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="javascript:;"
                                                        data-delete-url="{{ route('superadmin.school.delete', ['id' => $school->id]) }}"
                                                        data-school-name="{{ $school->title }}"
                                                        onclick="showDeleteSchoolModal(this)">{{ get_phrase('Delete') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="sa-sl-pagination">
                    {!! $schools->appends(request()->all())->links() !!}
                </div>
            @else
                <div class="sa-sl-empty">
                    <div class="sa-sl-empty-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6" />
                        </svg>
                    </div>
                    <h5>{{ get_phrase('No schools found') }}</h5>
                    <p>{{ get_phrase('Get started by adding your first school.') }}</p>
                    <a href="{{ route('superadmin.school.add') }}" class="sa-sl-add"
                        style="margin-top:16px;display:inline-flex;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        {{ get_phrase('Add School') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="sa-sl-modal-overlay" id="deleteSchoolModal">
        <div class="sa-sl-modal">
            <div class="sa-sl-modal-head">
                <h5>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                        stroke-width="2" style="vertical-align:-3px;margin-right:6px;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                    {{ get_phrase('Delete School') }}
                </h5>
                <button class="sa-sl-modal-close" onclick="closeDeleteModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="" method="POST" id="deleteSchoolForm">
                @csrf
                <div class="sa-sl-modal-body">
                    <p>{{ get_phrase('You are about to permanently delete') }} <strong id="deleteSchoolName"></strong>.
                        {{ get_phrase('This will remove the school database and all of its data and cannot be undone.') }}
                    </p>
                    <div class="sa-sl-modal-field">
                        <label>{{ get_phrase('Enter your super admin password to confirm') }}</label>
                        <input type="password" name="password" id="superadmin_password"
                            placeholder="{{ get_phrase('Super admin password') }}" autocomplete="off" required>
                    </div>
                    <div class="sa-sl-modal-actions">
                        <button type="button" class="sa-sl-modal-btn sa-sl-modal-btn-ghost"
                            onclick="closeDeleteModal()">{{ get_phrase('Cancel') }}</button>
                        <button type="submit" class="sa-sl-modal-btn sa-sl-modal-btn-red">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                            </svg>
                            {{ get_phrase('Delete School') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Print/PDF hidden table -->
    @if (count($schools) > 0)
        <div id="school_list_print" style="display:none;">
            <h4 style="font-size:16px;font-weight:600;text-align:center;margin-bottom:15px;">
                {{ get_phrase('School List') }}</h4>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">#</th>
                        <th style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ get_phrase('Name') }}</th>
                        <th style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ get_phrase('Address') }}</th>
                        <th style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ get_phrase('Phone') }}</th>
                        <th style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ get_phrase('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schools as $key => $school)
                        <tr>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">
                                {{ $schools->firstItem() + $key }}</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">
                                <strong>{{ $school->title }}</strong></td>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ $school->address }}</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">{{ $school->phone }}</td>
                            <td style="padding:8px;border:1px solid #e2e8f0;font-size:12px;">
                                {{ $school->status == '1' ? 'Active' : ($school->status == '2' ? 'Pending' : 'Inactive') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <script>
        document.querySelectorAll('.sa-sl-tbl').forEach(function(tbl) {
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

        function showDeleteSchoolModal(el) {
            var url = el.getAttribute('data-delete-url');
            var name = el.getAttribute('data-school-name');
            document.getElementById('deleteSchoolForm').action = url;
            document.getElementById('deleteSchoolName').textContent = name;
            document.getElementById('superadmin_password').value = '';
            document.getElementById('deleteSchoolModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteSchoolModal').classList.remove('show');
        }
        document.getElementById('deleteSchoolModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        function Export() {
            const element = document.getElementById("school_list_print");
            var clonedElement = element.cloneNode(true);
            $(clonedElement).css("display", "block");
            var opt = {
                margin: 1,
                filename: 'school_list_{{ date('y-m-d') }}.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                }
            };
            html2pdf().set(opt).from(clonedElement).save();
            clonedElement.remove();
        }

        function printableDiv(printableAreaDivId) {
            var printContents = document.getElementById(printableAreaDivId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
@endsection
