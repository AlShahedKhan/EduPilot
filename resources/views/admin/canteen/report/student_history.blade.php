@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Student Purchase History') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Student History') }}</a></li>
                        </ul>
                    </div>
                    @if (isset($orders) && count($orders) > 0)
                        <div class="export-btn-area no-print">
                            <button onclick="window.print()" class="export_btn">{{ get_phrase('Print') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style media="print">
        .sidebar, .navbar, .no-print, .mainSection-title, .copyright-text { display: none !important; }
        .home-section { margin-left: 0 !important; }
    </style>

    <div class="row mb-3 no-print">
        <div class="col-md-5">
            <div class="eSection-wrap-2">
                <form method="GET" action="{{ route('admin.canteen.report_student_history') }}" class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <label class="eForm-label">{{ get_phrase('Select Student') }}</label>
                        <select name="student_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('— Select Student —') }}</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" {{ $student_id == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-form">{{ get_phrase('Go') }}</button>
                </form>
            </div>
        </div>
    </div>

    @if ($student_id)
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2">
                    @php $total_spent = $orders->sum('total_amount'); @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            {{ get_phrase('Purchase history for') }}: <span class="text-primary">{{ $students->firstWhere('id', $student_id)->name ?? '' }}</span>
                        </h6>
                        <span class="fw-bold text-success">{{ get_phrase('Total Spent') }}: {{ number_format($total_spent, 2) }}</span>
                    </div>

                    @if (count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table eTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ get_phrase('Order #') }}</th>
                                        <th>{{ get_phrase('Items') }}</th>
                                        <th>{{ get_phrase('Date') }}</th>
                                        <th class="text-end">{{ get_phrase('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->order_number }}</td>
                                            <td>
                                                @foreach ($order->orderItems as $oi)
                                                    <span class="badge bg-light text-dark border">{{ $oi->item_name }} ×{{ $oi->quantity }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                            <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="4" class="text-end">{{ get_phrase('Total Spent') }}</td>
                                        <td class="text-end text-success">{{ number_format($total_spent, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                            <br><span>{{ get_phrase('No purchase history for this student') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
