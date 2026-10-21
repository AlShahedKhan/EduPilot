@extends('student.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('My Order History') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('My Orders') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('student.canteen.order_form') }}" class="export_btn">{{ get_phrase('Order Again') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                @if (count($orders) > 0)
                    <div class="table-responsive tScrollFix pb-2">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ get_phrase('Order #') }}</th>
                                    <th>{{ get_phrase('Items') }}</th>
                                    <th>{{ get_phrase('Amount') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th>{{ get_phrase('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $key => $order)
                                    <tr>
                                        <td>{{ $orders->firstItem() + $key }}</td>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>
                                            @foreach ($order->orderItems as $oi)
                                                <span class="eBadge ebg-light text-dark border me-1" style="font-size:11px;">
                                                    {{ $oi->item_name }} ×{{ $oi->quantity }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td><strong>{{ number_format($order->total_amount, 2) }}</strong></td>
                                        <td>
                                            @if ($order->status == 0)
                                                <span class="eBadge ebg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                            @elseif ($order->status == 1)
                                                <span class="eBadge ebg-success">{{ get_phrase('Completed') }}</span>
                                            @else
                                                <span class="eBadge ebg-danger">{{ get_phrase('Cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $orders->links() !!}
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span>{{ get_phrase('No orders yet') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
