@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Top Selling Food Report') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Top Selling') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area no-print">
                        <button onclick="window.print()" class="export_btn">{{ get_phrase('Print Report') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style media="print">
        .sidebar, .navbar, .no-print, .mainSection-title, .copyright-text { display: none !important; }
        .home-section { margin-left: 0 !important; }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <h6 class="fw-bold mb-3">{{ get_phrase('Top 20 Best Selling Items') }}</h6>
                @if (count($top_items) > 0)
                    <div class="table-responsive">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>{{ get_phrase('Rank') }}</th>
                                    <th>{{ get_phrase('Food Item') }}</th>
                                    <th class="text-center">{{ get_phrase('Total Qty Sold') }}</th>
                                    <th class="text-end">{{ get_phrase('Total Revenue') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($top_items as $key => $item)
                                    <tr>
                                        <td>
                                            @if ($key == 0)
                                                <span class="badge bg-warning text-dark">🥇 #1</span>
                                            @elseif ($key == 1)
                                                <span class="badge bg-secondary">🥈 #2</span>
                                            @elseif ($key == 2)
                                                <span class="badge" style="background:#cd7f32;color:#fff;">🥉 #3</span>
                                            @else
                                                <span class="text-muted">#{{ $key + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->total_qty }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-success">{{ number_format($item->total_revenue, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br><span>{{ get_phrase('No sales data found') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
