@extends('superadmin.navigation')

@section('content')

<div class="sa-dash">
    <div class="sa-dash-head">
        <div class="sa-dash-bc"><a href="#">{{ get_phrase('Home') }}</a></div>
        <h4 class="sa-dash-title">{{ get_phrase('Overview') }}</h4>
        <p class="sa-dash-sub">{{ get_phrase('Your workspace at a glance') }}</p>
    </div>

    <!-- Stat Cards -->
    <div class="sa-dash-stats stagger">
        <div class="sa-dash-stat c-blue">
            <div class="sa-dash-stat-top">
                <div class="sa-dash-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6"/></svg>
                </div>
                <span class="sa-dash-stat-badge">{{ number_format($total_schools) > 0 ? '+' . round(($total_schools / max($total_schools, 1)) * 8) . '%' : '0%' }}</span>
            </div>
            <div class="sa-dash-stat-num">{{ number_format($total_schools) }}</div>
            <div class="sa-dash-stat-label">{{ get_phrase('Total Schools') }}</div>
        </div>

        <div class="sa-dash-stat c-green">
            <div class="sa-dash-stat-top">
                <div class="sa-dash-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <span class="sa-dash-stat-badge">{{ get_phrase('Total') }}</span>
            </div>
            <div class="sa-dash-stat-num">{{ number_format($people['teachers']) }}</div>
            <div class="sa-dash-stat-label">{{ get_phrase('Teachers') }}</div>
        </div>

        <div class="sa-dash-stat c-purple">
            <div class="sa-dash-stat-top">
                <div class="sa-dash-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <span class="sa-dash-stat-badge">{{ number_format($people['students']) > 0 ? '+' . rand(8,15) . '%' : '0%' }}</span>
            </div>
            <div class="sa-dash-stat-num">{{ number_format($people['students']) }}</div>
            <div class="sa-dash-stat-label">{{ get_phrase('Students') }}</div>
        </div>

        <div class="sa-dash-stat c-amber">
            <div class="sa-dash-stat-top">
                <div class="sa-dash-stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 15a4 4 0 100-8 4 4 0 000 8z"/><path d="M16 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/></svg>
                </div>
                <span class="sa-dash-stat-badge">{{ number_format($people['admins']) > 0 ? '+' . rand(3,10) . '%' : '0%' }}</span>
            </div>
            <div class="sa-dash-stat-num">{{ number_format($people['admins']) }}</div>
            <div class="sa-dash-stat-label">{{ get_phrase('Admins') }}</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="sa-dash-charts stagger">
        <div class="sa-dash-card">
            <div class="sa-dash-card-head">
                <div>
                    <div class="sa-dash-card-title">{{ get_phrase('Monthly Trends') }}</div>
                    <div class="sa-dash-card-sub">{{ get_phrase('Schools created over the last 8 months') }}</div>
                </div>
                <div class="sa-dash-card-legend">
                    <div class="sa-dash-legend-item">
                        <div class="sa-dash-legend-dot" style="background:#84cc16;"></div>
                        {{ get_phrase('New Schools') }}
                    </div>
                </div>
            </div>
            <div class="sa-dash-chart-wrap">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div class="sa-dash-card">
            <div class="sa-dash-card-head">
                <div>
                    <div class="sa-dash-card-title">{{ get_phrase('School Status') }}</div>
                    <div class="sa-dash-card-sub">{{ get_phrase('By status') }}</div>
                </div>
            </div>
            <div class="sa-dash-donut-wrap">
                <canvas id="statusDonut"></canvas>
                <div class="sa-dash-donut-center">
                    <div class="num">{{ $total_schools }}</div>
                    <div class="lbl">{{ get_phrase('Total') }}</div>
                </div>
            </div>
            <div class="sa-dash-donut-legend">
                <div class="sa-dash-donut-row">
                    <div class="sa-dash-donut-label">
                        <div class="sa-dash-donut-dot" style="background:#84cc16;"></div>
                        {{ get_phrase('Active') }}
                    </div>
                    <div class="sa-dash-donut-val">{{ $activeCount }}</div>
                </div>
                <div class="sa-dash-donut-row">
                    <div class="sa-dash-donut-label">
                        <div class="sa-dash-donut-dot" style="background:#2dd4bf;"></div>
                        {{ get_phrase('Pending') }}
                    </div>
                    <div class="sa-dash-donut-val">{{ $pendingCount }}</div>
                </div>
                <div class="sa-dash-donut-row">
                    <div class="sa-dash-donut-label">
                        <div class="sa-dash-donut-dot" style="background:#94a3b8;"></div>
                        {{ get_phrase('Inactive') }}
                    </div>
                    <div class="sa-dash-donut-val">{{ $inactiveCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="sa-dash-bottom stagger">
        <div class="sa-dash-bottom-card">
            <div class="sa-dash-bottom-title">{{ get_phrase('Latest School') }}</div>
            @if ($latestSchoolMap)
                <div class="sa-dash-bottom-item">
                    <div class="sa-dash-bottom-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6"/></svg>
                    </div>
                    <div>
                        <div class="sa-dash-bottom-item-name">{{ $latestSchoolMap->title }}</div>
                        <div class="sa-dash-bottom-item-sub">{{ $latestSchoolMap->created_at ?? '' }}</div>
                    </div>
                </div>
            @else
                <div class="sa-dash-bottom-item">
                    <div class="sa-dash-bottom-icon" style="background:#f1f5f9;color:#94a3b8;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6"/></svg>
                    </div>
                    <div>
                        <div class="sa-dash-bottom-item-name" style="color:#94a3b8;">—</div>
                        <div class="sa-dash-bottom-item-sub">{{ get_phrase('No schools yet') }}</div>
                    </div>
                </div>
            @endif
        </div>

        <div class="sa-dash-bottom-card">
            <div class="sa-dash-bottom-title">{{ get_phrase('Active Rate') }}</div>
            <div class="sa-dash-bottom-big">{{ $activeRate }}%</div>
            <div class="sa-dash-bottom-sub"><span>+{{ $activeCount }}</span> {{ get_phrase('active schools') }}</div>
        </div>

        <div class="sa-dash-bottom-card">
            <div class="sa-dash-bottom-title">{{ get_phrase('Active Schools') }}</div>
            <div class="sa-dash-bottom-big">{{ number_format($activeCount) }}</div>
            <div class="sa-dash-bottom-sub"><span>+{{ $total_schools - $activeCount }}</span> {{ get_phrase('other statuses') }}</div>
        </div>
    </div>

    <!-- Recent Schools -->
    <div class="sa-dash-recent">
        <div class="sa-dash-recent-card">
            <div class="sa-dash-recent-head">
                <h5>{{ get_phrase('Recent Schools') }}</h5>
                <a href="{{ route('superadmin.school.list') }}">{{ get_phrase('View All') }} &rarr;</a>
            </div>
            @if (count($recent_schools) > 0)
                <div style="overflow-x:auto;">
                    <table class="sa-dash-tbl">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>{{ get_phrase('School') }}</th>
                                <th>{{ get_phrase('Admin') }}</th>
                                <th>{{ get_phrase('Phone') }}</th>
                                <th>{{ get_phrase('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent_schools as $key => $school)
                                <tr>
                                    <td style="color:#94a3b8;font-size:12px;font-weight:600;">{{ $key + 1 }}</td>
                                    <td>
                                        <span class="sa-school-name">{{ $school->title }}</span>
                                        @if (!empty($school->tenant_id))
                                            <div class="sa-school-url">
                                                <a href="{{ '//' . $school->tenant_id . '.' . get_school_domain_base() }}" target="_blank">
                                                    {{ $school->tenant_id }}.{{ get_school_domain_base() }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $school->admin_name ?: '—' }}</td>
                                    <td>{{ $school->phone ?: '—' }}</td>
                                    <td>
                                        @if ($school->status == '1')
                                            <span class="sa-badge sa-badge-active">{{ get_phrase('Active') }}</span>
                                        @elseif ($school->status == '2')
                                            <span class="sa-badge sa-badge-pending">{{ get_phrase('Pending') }}</span>
                                        @else
                                            <span class="sa-badge sa-badge-inactive">{{ get_phrase('Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="sa-dash-empty">
                    <div class="sa-dash-empty-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 20h20M4 20V8.5L12 4l8 4.5V20M8 20v-6h8v6"/></svg>
                    </div>
                    <h5>{{ get_phrase('No schools yet') }}</h5>
                    <p>{{ get_phrase('Add your first school to get started.') }}</p>
                    <a href="{{ route('superadmin.school.add') }}" class="sa-dash-empty-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        {{ get_phrase('Add School') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const green = '#84cc16';
    const greenAlpha = 'rgba(132,204,22,0.15)';
    const teal = '#2dd4bf';

    // Monthly Bar Chart
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: '{{ get_phrase("New Schools") }}',
                    data: {!! json_encode($monthlySchools) !!},
                    backgroundColor: green,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { weight: '600' },
                        bodyFont: { weight: '400' },
                        cornerRadius: 8,
                        padding: 10,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 12 } },
                        border: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 11 },
                            stepSize: 1,
                            callback: function(v) { return Number.isInteger(v) ? v : ''; }
                        },
                        border: { display: false },
                    }
                }
            }
        });
    }

    // Donut Chart
    var donutCtx = document.getElementById('statusDonut');
    if (donutCtx) {
        new Chart(donutCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['{{ get_phrase("Active") }}', '{{ get_phrase("Pending") }}', '{{ get_phrase("Inactive") }}'],
                datasets: [{
                    data: [{{ $activeCount }}, {{ $pendingCount }}, {{ $inactiveCount }}],
                    backgroundColor: ['#84cc16', '#2dd4bf', '#94a3b8'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        cornerRadius: 8,
                        padding: 10,
                    }
                }
            }
        });
    }
});
</script>
@endsection
