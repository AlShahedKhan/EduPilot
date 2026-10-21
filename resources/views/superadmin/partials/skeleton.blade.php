{{-- Skeleton loading states for the superadmin panel. --}}
{{-- Usage: --}}
{{--   @include('superadmin.partials.skeleton', ['type' => 'card-grid']) --}}
{{--   @include('superadmin.partials.skeleton', ['type' => 'table', 'rows' => 5]) --}}
{{--   @include('superadmin.partials.skeleton', ['type' => 'list', 'rows' => 6]) --}}

@php
    $rows = $rows ?? 5;
@endphp

@if ($type === 'card-grid')
    <div class="sk-card-grid">
        @for ($i = 0; $i < 4; $i++)
            <div class="sk-card">
                <div class="sk-avatar"></div>
                <div class="sk-body">
                    <div class="sk sk-line"></div>
                    <div class="sk sk-line-sm"></div>
                </div>
            </div>
        @endfor
    </div>
@elseif ($type === 'table')
    <div class="sk-table-wrap">
        @for ($i = 0; $i < $rows; $i++)
            <div class="sk-table-row">
                <div class="sk sk-cell-1"></div>
                <div class="sk sk-cell-2"></div>
                <div class="sk sk-cell-3"></div>
                <div class="sk sk-cell-4"></div>
            </div>
        @endfor
    </div>
@elseif ($type === 'list')
    <div class="sk-list">
        @for ($i = 0; $i < $rows; $i++)
            <div class="sk-list-item">
                <div class="sk-avatar"></div>
                <div class="sk-body">
                    <div class="sk sk-line"></div>
                    <div class="sk sk-line-sm"></div>
                </div>
            </div>
        @endfor
    </div>
@else
    <div class="sk sk-line" style="width:100%; height:16px;"></div>
@endif
