@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Monthly Sales Report') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Monthly Report') }}</a></li>
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

    <div class="row mb-3 no-print">
        <div class="col-md-4">
            <div class="eSection-wrap-2">
                <form method="GET" action="{{ route('admin.canteen.report_monthly') }}" class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <label class="eForm-label">{{ get_phrase('Select Month') }}</label>
                        <input type="month" name="month" class="form-control eForm-control" value="{{ $month }}">
                    </div>
                    <button type="submit" class="btn-form">{{ get_phrase('Go') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">{{ get_phrase('Report for') }}: <span class="text-primary">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</span></h6>
                    <span class="fw-bold text-success fs-5">{{ get_phrase('Total') }}: {{ number_format($total_sales, 2) }}</span>
                </div>

                @if (count($orders) > 0)
                    @php
                        $grouped = $orders->groupBy(fn($o) => $o->created_at->format('Y-m-d'));
                    @endphp

                    @foreach ($grouped as $day => $dayOrders)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center py-2 px-3" style="background:#f8f9fa;border-radius:6px;">
                                <strong>{{ \Carbon\Carbon::parse($day)->format('d F Y') }}</strong>
                                <span class="text-success fw-bold">{{ number_format($dayOrders->sum('total_amount'), 2) }}</span>
                            </div>
                            <div class="table-responsive mt-1">
                                <table class="table eTable table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ get_phrase('Order #') }}</th>
                                            <th>{{ get_phrase('Student') }}</th>
                                            <th>{{ get_phrase('Time') }}</th>
                                            <th class="text-end">{{ get_phrase('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dayOrders as $order)
                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->student->name ?? '-' }}</td>
                                                <td>{{ $order->created_at->format('h:i A') }}</td>
                                                <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <div class="d-flex justify-content-end mt-3">
                        <div class="px-4 py-2" style="background:#e8f5e9;border-radius:8px;">
                            <strong class="text-success fs-5">{{ get_phrase('Grand Total') }}: {{ number_format($total_sales, 2) }}</strong>
                        </div>
                    </div>
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br><span>{{ get_phrase('No sales this month') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
