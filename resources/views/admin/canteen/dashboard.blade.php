@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Canteen Dashboard') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('admin.canteen.pos') }}" class="export_btn">{{ get_phrase('New Order (POS)') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="eSection-wrap-2 text-center p-4">
                <div class="mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#4CAF50" viewBox="0 0 24 24">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                </div>
                <h3 class="fw-bold text-success">{{ number_format($today_sales, 2) }}</h3>
                <p class="text-muted mb-0">{{ get_phrase("Today's Sales") }}</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="eSection-wrap-2 text-center p-4">
                <div class="mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#2196F3" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L21 8l-9 9z"/>
                    </svg>
                </div>
                <h3 class="fw-bold text-primary">{{ $today_orders }}</h3>
                <p class="text-muted mb-0">{{ get_phrase("Today's Orders") }}</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="eSection-wrap-2 text-center p-4">
                <div class="mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#FF9800" viewBox="0 0 24 24">
                        <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>
                    </svg>
                </div>
                <h3 class="fw-bold text-warning">{{ $total_items }}</h3>
                <p class="text-muted mb-0">{{ get_phrase('Total Food Items') }}</p>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="eSection-wrap-2 text-center p-4">
                <div class="mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#F44336" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <h3 class="fw-bold text-danger">{{ $pending_orders }}</h3>
                <p class="text-muted mb-0">{{ get_phrase('Pending Orders') }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">{{ get_phrase('Recent Orders') }}</h6>
                    <button type="button" class="eBtn eBtn btn-secondary" onclick="window.location.href='{{ route('admin.canteen.order_list') }}'">{{ get_phrase('View All') }}</button>

                </div>
                @if (count($recent_orders) > 0)
                    <div class="table-responsive">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>{{ get_phrase('Order #') }}</th>
                                    <th>{{ get_phrase('Student') }}</th>
                                    <th>{{ get_phrase('Items') }}</th>
                                    <th>{{ get_phrase('Amount') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th class="text-end">{{ get_phrase('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recent_orders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->student->name ?? '-' }}</td>
                                        <td>{{ $order->orderItems->sum('quantity') }}</td>
                                        <td>{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @if ($order->status == 0)
                                                <span class="eBadge ebg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @elseif ($order->status == 1)
                                                <span class="eBadge ebg-success">{{ get_phrase('Completed') }}</span>
                                            @else
                                                <span class="eBadge ebg-danger">{{ get_phrase('Cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="eBtn eBtn btn-secondary" onclick="window.location.href='{{ route('admin.canteen.order_detail', $order->id) }}'">{{ get_phrase('View') }}</button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br><span>{{ get_phrase('No orders yet') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
