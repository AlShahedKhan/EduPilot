@extends('superadmin.navigation')

@section('content')

<div class="sa-bk">
    <div class="sa-bk-head">
        <h4>{{ get_phrase('Database Backup') }}</h4>
        <div class="sa-bc">
            <a href="{{ route('superadmin.dashboard') }}">{{ get_phrase('Home') }}</a> / {{ get_phrase('Database Backup') }}
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalBackups = count($backups);
        $totalSize = collect($backups)->sum(function ($b) {
            $str = $b['size'];
            if (str_ends_with($str, 'MB')) return (float) $str * 1048576;
            if (str_ends_with($str, 'KB')) return (float) $str * 1024;
            return (float) $str;
        });
        $totalSizeFormatted = $totalSize >= 1048576 ? round($totalSize / 1048576, 2) . ' MB' : round($totalSize / 1024, 2) . ' KB';
        $latestBackup = $backups[0] ?? null;
        $tenantCount = \App\Models\Tenant::count();
    @endphp

    <div class="sa-bk-stats stagger">
        <div class="sa-bk-stat">
            <div class="sa-bk-stat-icon si-blue">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            </div>
            <div>
                <div class="sa-bk-stat-label">{{ get_phrase('Total Backups') }}</div>
                <div class="sa-bk-stat-num num-blue">{{ $totalBackups }}</div>
            </div>
        </div>
        <div class="sa-bk-stat">
            <div class="sa-bk-stat-icon si-green">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <div class="sa-bk-stat-label">{{ get_phrase('Total Size') }}</div>
                <div class="sa-bk-stat-num num-green">{{ $totalSizeFormatted }}</div>
            </div>
        </div>
        <div class="sa-bk-stat">
            <div class="sa-bk-stat-icon si-amber">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="sa-bk-stat-label">{{ get_phrase('Latest Backup') }}</div>
                <div class="sa-bk-stat-num num-amber" style="font-size:15px;line-height:1.3;">{{ $latestBackup ? $latestBackup['created'] : '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Create Backup --}}
    <div class="sa-bk-create-card">
        <div class="sa-bk-create-inner">
            <div class="sa-bk-create-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            </div>
            <div class="sa-bk-create-text" style="flex:1;">
                <h5>{{ get_phrase('Create New Backup') }}</h5>
                <p>
                    {{ get_phrase('Dumps all databases into a single zip file.') }}
                </p>
            </div>
            <form method="POST" action="{{ route('superadmin.backup.create') }}">
                @csrf
                <button type="submit" class="sa-bk-create-btn" onclick="return confirm('{{ get_phrase('Create a new database backup now?') }}')">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                    {{ get_phrase('Backup Now') }}
                </button>
            </form>
        </div>
        <div class="sa-bk-db-info">
            <span class="sa-bk-db-pill">{{ config('database.connections.mysql.database') }} {{ get_phrase('(Central)') }}</span>
            @foreach (\App\Models\Tenant::all() as $tenant)
                <span class="sa-bk-db-pill">{{ $tenant->id }} {{ get_phrase('(Tenant)') }}</span>
            @endforeach
        </div>
    </div>

    {{-- Backups List --}}
    <div class="sa-bk-list-card">
        <div class="sa-bk-list-head">
            <h5>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                {{ get_phrase('Backup History') }}
                <span class="sa-bk-list-count">{{ $totalBackups }}</span>
            </h5>
        </div>

        @if ($totalBackups > 0)
            <div style="overflow-x:auto;">
                <table class="sa-bk-tbl">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>{{ get_phrase('File') }}</th>
                            <th>{{ get_phrase('Size') }}</th>
                            <th style="width:160px;">{{ get_phrase('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $key => $backup)
                            <tr>
                                <td style="color:#94a3b8;font-size:12px;font-weight:600;">{{ $key + 1 }}</td>
                                <td>
                                    <div class="sa-bk-file">
                                        <div class="sa-bk-file-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                        </div>
                                        <div>
                                            <div class="sa-bk-file-name">{{ $backup['name'] }}</div>
                                            <div class="sa-bk-file-date">{{ $backup['created'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="sa-bk-size">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                        {{ $backup['size'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="sa-bk-actions">
                                        <a href="{{ route('superadmin.backup.download', $backup['name']) }}" class="sa-bk-action sa-bk-action-dl" title="{{ get_phrase('Download') }}">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                        </a>
                                        <button type="button" class="sa-bk-action sa-bk-action-mail" title="{{ get_phrase('Email') }}" onclick="showEmailModal('{{ $backup['name'] }}')">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.backup.delete', $backup['name']) }}" onsubmit="return confirm('{{ get_phrase('Delete this backup?') }}')" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="sa-bk-action sa-bk-action-del" title="{{ get_phrase('Delete') }}">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="sa-bk-empty">
                <div class="sa-bk-empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                </div>
                <h5>{{ get_phrase('No backups yet') }}</h5>
                <p>{{ get_phrase('Click "Backup Now" above to create your first database backup.') }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Email Modal -->
<div class="sa-bk-modal-overlay" id="emailModal">
    <div class="sa-bk-modal">
        <div class="sa-bk-modal-head">
            <h5>
                <div class="sa-bk-modal-head-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                {{ get_phrase('Email Backup') }}
            </h5>
            <button class="sa-bk-modal-close" onclick="closeEmailModal()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('superadmin.backup.email') }}">
            @csrf
            <div class="sa-bk-modal-body">
                <div class="sa-bk-modal-file">
                    <div class="sa-bk-modal-file-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    </div>
                    <span class="sa-bk-modal-file-name" id="emailFilenameDisplay"></span>
                </div>
                <div class="sa-bk-modal-field">
                    <label>{{ get_phrase('Send to Email') }}</label>
                    <input type="email" name="email" value="{{ auth('superadmin')->user()->email ?? '' }}" placeholder="admin@example.com" required>
                </div>
                <input type="hidden" name="filename" id="emailFilename">
                <div class="sa-bk-modal-actions">
                    <button type="submit" class="sa-bk-modal-btn sa-bk-modal-btn-green">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        {{ get_phrase('Send Backup') }}
                    </button>
                    <button type="button" class="sa-bk-modal-btn sa-bk-modal-btn-ghost" onclick="closeEmailModal()">{{ get_phrase('Cancel') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function showEmailModal(filename) {
    document.getElementById('emailFilename').value = filename;
    document.getElementById('emailFilenameDisplay').textContent = filename;
    document.getElementById('emailModal').classList.add('show');
}
function closeEmailModal() {
    document.getElementById('emailModal').classList.remove('show');
}
document.getElementById('emailModal').addEventListener('click', function(e) {
    if (e.target === this) closeEmailModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEmailModal();
});
</script>
@endsection
