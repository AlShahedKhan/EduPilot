@extends('superadmin.navigation')

@section('content')

<div class="sa-el">
    <div class="sa-el-head">
        <h4>{{ get_phrase('Email Logs') }}</h4>
        <div class="sa-breadcrumb">
            <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a> / {{ get_phrase('Email Logs') }}
        </div>
    </div>

    <div class="sa-el-stats stagger">
        @php
            $total = $logs->total();
            $sentCount = \App\Models\EmailLog::where('status','sent')->count();
            $failedCount = \App\Models\EmailLog::where('status','failed')->count();
            $pendingCount = \App\Models\EmailLog::where('status','pending')->count();
        @endphp
        <div class="sa-el-stat">
            <div class="sa-el-stat-icon total">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
            </div>
            <div class="sa-el-stat-text">
                <div class="sa-el-stat-label">{{ get_phrase('Total') }}</div>
                <div class="sa-el-stat-num total">{{ number_format($total) }}</div>
            </div>
        </div>
        <div class="sa-el-stat">
            <div class="sa-el-stat-icon sent">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
            </div>
            <div class="sa-el-stat-text">
                <div class="sa-el-stat-label">{{ get_phrase('Sent') }}</div>
                <div class="sa-el-stat-num sent">{{ number_format($sentCount) }}</div>
            </div>
        </div>
        <div class="sa-el-stat">
            <div class="sa-el-stat-icon failed">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
            </div>
            <div class="sa-el-stat-text">
                <div class="sa-el-stat-label">{{ get_phrase('Failed') }}</div>
                <div class="sa-el-stat-num failed">{{ number_format($failedCount) }}</div>
            </div>
        </div>
        <div class="sa-el-stat">
            <div class="sa-el-stat-icon pending">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="sa-el-stat-text">
                <div class="sa-el-stat-label">{{ get_phrase('Pending') }}</div>
                <div class="sa-el-stat-num pending">{{ number_format($pendingCount) }}</div>
            </div>
        </div>
    </div>

    <div class="sa-el-card">
        <div class="sa-el-toolbar">
            <div class="sa-el-toolbar-left">
                <form method="GET" action="{{ route('superadmin.email_logs') }}" style="display:flex;gap:8px;align-items:center;">
                    <div class="sa-el-search">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" name="search" placeholder="{{ get_phrase('Search to, from, subject...') }}" value="{{ request('search') }}">
                    </div>
                    <select name="status" class="sa-el-select">
                        <option value="">{{ get_phrase('All Status') }}</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>{{ get_phrase('Sent') }}</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ get_phrase('Failed') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                    </select>
                    <button type="submit" class="sa-el-btn sa-el-btn-ghost">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                        {{ get_phrase('Filter') }}
                    </button>
                </form>
            </div>
            <div class="sa-el-toolbar-right">
                <a href="{{ route('superadmin.email.compose') }}" class="sa-el-btn sa-el-btn-blue">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    {{ get_phrase('Compose') }}
                </a>
                @if ($total > 0)
                <form method="POST" action="{{ route('superadmin.email_logs.clear') }}" onsubmit="return confirm('{{ get_phrase('Delete all email logs?') }}')">
                    @csrf
                    <button type="submit" class="sa-el-btn sa-el-btn-red">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        {{ get_phrase('Clear All') }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if (count($logs) > 0)
            <div style="overflow-x:auto;">
                <table class="sa-el-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>{{ get_phrase('To') }}</th>
                            <th>{{ get_phrase('From') }}</th>
                            <th>{{ get_phrase('Subject') }}</th>
                            <th>{{ get_phrase('Body') }}</th>
                            <th>{{ get_phrase('School') }}</th>
                            <th>{{ get_phrase('Status') }}</th>
                            <th>{{ get_phrase('Time') }}</th>
                            <th style="width:44px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $key => $log)
                            <tr>
                                <td class="sa-el-idx">{{ $logs->firstItem() + $key }}</td>
                                <td class="sa-el-to">{{ $log->to }}</td>
                                <td class="sa-el-from">{{ $log->from ?: '—' }}</td>
                                <td class="sa-el-subject" title="{{ e($log->subject) }}">{{ \Illuminate\Support\Str::limit($log->subject, 40) ?: '—' }}</td>
                                <td class="sa-el-body" title="{{ e($log->body) }}">{{ \Illuminate\Support\Str::limit($log->body, 50) ?: '—' }}</td>
                                <td>
                                    @if ($log->tenant_id)
                                        <span class="sa-el-school">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                                            {{ $log->tenant_id }}
                                        </span>
                                    @else
                                        <span class="sa-el-school">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="sa-el-badge sa-el-badge-{{ $log->status }}">
                                        {{ get_phrase(ucfirst($log->status)) }}
                                    </span>
                                </td>
                                <td class="sa-el-time">{{ $log->created_at->diffForHumans() }}</td>
                                <td>
                                    <button type="button" class="sa-el-del" title="{{ get_phrase('View Details') }}"
                                        onclick="showLogDetail(this)"
                                        data-to="{{ e($log->to) }}"
                                        data-from="{{ e($log->from ?: '—') }}"
                                        data-subject="{{ e($log->subject ?: '—') }}"
                                        data-body="{{ e($log->body ?: '—') }}"
                                        data-school="{{ e($log->tenant_id ?: 'Central') }}"
                                        data-status="{{ $log->status }}"
                                        data-time="{{ $log->created_at->format('d M Y, h:i A') }}"
                                        data-mailable="{{ e($log->mailable_class ?: '—') }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sa-el-pagination">
                {!! $logs->withQueryString()->links() !!}
            </div>
        @else
            <div class="sa-el-empty">
                <div class="sa-el-empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                </div>
                <h5>{{ get_phrase('No email logs found') }}</h5>
                <p>{{ get_phrase('Emails sent from the system will appear here.') }}</p>
                <a href="{{ route('superadmin.email.compose') }}" class="sa-el-btn sa-el-btn-blue" style="margin-top:18px;display:inline-flex;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    {{ get_phrase('Compose Email') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div class="sa-el-modal-overlay" id="logDetailModal">
    <div class="sa-el-modal">
        <div class="sa-el-modal-head">
            <h5>{{ get_phrase('Email Details') }}</h5>
            <button class="sa-el-modal-close" onclick="closeLogDetail()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="sa-el-modal-body">
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('To') }}</div>
                <div class="sa-el-modal-val" id="modal-to"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('From') }}</div>
                <div class="sa-el-modal-val" id="modal-from"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('Subject') }}</div>
                <div class="sa-el-modal-val" id="modal-subject"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('School') }}</div>
                <div class="sa-el-modal-val" id="modal-school"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('Status') }}</div>
                <div class="sa-el-modal-val" id="modal-status"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('Mailable') }}</div>
                <div class="sa-el-modal-val" id="modal-mailable" style="font-family:monospace;font-size:12px;color:#64748b;"></div>
            </div>
            <div class="sa-el-modal-row">
                <div class="sa-el-modal-label">{{ get_phrase('Time') }}</div>
                <div class="sa-el-modal-val" id="modal-time"></div>
            </div>
            <div>
                <div class="sa-el-modal-label" style="margin-bottom:6px;">{{ get_phrase('Body') }}</div>
                <div class="sa-el-modal-body-text" id="modal-body"></div>
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetail(btn) {
    document.getElementById('modal-to').textContent = btn.dataset.to;
    document.getElementById('modal-from').textContent = btn.dataset.from;
    document.getElementById('modal-subject').textContent = btn.dataset.subject;
    document.getElementById('modal-school').textContent = btn.dataset.school;
    document.getElementById('modal-body').textContent = btn.dataset.body;
    document.getElementById('modal-mailable').textContent = btn.dataset.mailable;
    document.getElementById('modal-time').textContent = btn.dataset.time;
    var statusEl = document.getElementById('modal-status');
    var s = btn.dataset.status;
    var colors = {sent:'#16a34a',failed:'#dc2626',pending:'#d97706'};
    statusEl.innerHTML = '<span class="sa-el-badge sa-el-badge-' + s + '">' + s.charAt(0).toUpperCase() + s.slice(1) + '</span>';
    document.getElementById('logDetailModal').classList.add('show');
}
function closeLogDetail() {
    document.getElementById('logDetailModal').classList.remove('show');
}
document.getElementById('logDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogDetail();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLogDetail();
});
</script>
@endsection
