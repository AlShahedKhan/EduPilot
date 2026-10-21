@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('All Orders') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Orders') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('admin.canteen.pos') }}" class="export_btn">{{ get_phrase('New Order (POS)') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <form method="GET" action="{{ route('admin.canteen.order_list') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="eForm-label">{{ get_phrase('Search Order #') }}</label>
                        <input type="text" name="search" class="form-control eForm-control" value="{{ request('search') }}" placeholder="CNT-2024-00001">
                    </div>
                    <div class="col-md-3">
                        <label class="eForm-label">{{ get_phrase('Status') }}</label>
                        <select name="status" class="form-select eForm-select" style="padding: 9px;">
                            <option value="">{{ get_phrase('All Status') }}</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ get_phrase('Pending') }}</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ get_phrase('Completed') }}</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>{{ get_phrase('Cancelled') }}</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="eForm-label">{{ get_phrase('Date') }}</label>
                        <input type="date" name="date" class="form-control eForm-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-form">{{ get_phrase('Filter') }}</button>
                        <button type="button" class="eBtn eBtn btn-secondary" onclick="window.location.href='{{ route('admin.canteen.order_list') }}'">{{ get_phrase('Reset') }}</button>
                    </div>
                </form>
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
                                    <th>{{ get_phrase('Student') }}</th>
                                    <th>{{ get_phrase('Amount') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th>{{ get_phrase('Date') }}</th>
                                    <th class="text-end">{{ get_phrase('Options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $key => $order)
                                    <tr>
                                        <td>{{ $orders->firstItem() + $key }}</td>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->student->name ?? '-' }}</td>
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
                                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="text-center">
                                            <div class="adminTable-action">
                                                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ get_phrase('Actions') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.canteen.order_detail', $order->id) }}">{{ get_phrase('View Details') }}</a>
                                                    </li>

                                                    @if ($order->status == 0)
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.canteen.order_status', $order->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="1">
                                                            <button type="submit" class="dropdown-item text-success">{{ get_phrase('Mark Completed') }}</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.canteen.order_status', $order->id) }}" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="2">
                                                            <button type="submit" class="dropdown-item text-danger">{{ get_phrase('Cancel Order') }}</button>
                                                        </form>
                                                    </li>
                                                    @else
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.canteen.order_delete', $order->id) }}">{{ get_phrase('Delete') }}</a>
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
                    {!! $orders->appends(request()->all())->links() !!}
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br><span>{{ get_phrase('No orders found') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
