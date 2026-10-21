@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Daily Sales Report') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Daily Report') }}</a></li>
                        </ul>
                    </div>

                    <div class="export-btn-area no-print">
                        <button onclick="printReport()" class="export_btn">{{ get_phrase('Print Report') }}</button>
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
                <form method="GET" action="{{ route('admin.canteen.report_daily') }}" class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <label class="eForm-label">{{ get_phrase('Select Date') }}</label>
                        <input type="date" name="date" class="form-control eForm-control" value="{{ $date }}">
                    </div>
                    <button type="submit" class="btn-form">{{ get_phrase('Go') }}</button>
                </form>
            </div>
        </div>
    </div>

    <div id="printArea">
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">{{ get_phrase('Report for') }}: <span class="text-primary">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</span></h6>
                        <div class="text-end">
                            <span class="fw-bold text-success fs-5">{{ get_phrase('Total Sales') }}: {{ number_format($total_sales, 2) }}</span>
                        </div>
                    </div>

                    @if (count($orders) > 0)
                        <div class="table-responsive">
                            <table class="table eTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ get_phrase('Order #') }}</th>
                                        <th>{{ get_phrase('Student') }}</th>
                                        <th>{{ get_phrase('Items') }}</th>
                                        <th>{{ get_phrase('Time') }}</th>
                                        <th class="text-end">{{ get_phrase('Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $key => $order)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $order->order_number }}</td>
                                            <td>{{ $order->student->name ?? '-' }}</td>
                                            <td>{{ $order->orderItems->sum('quantity') }} {{ get_phrase('item(s)') }}</td>
                                            <td>{{ $order->created_at->format('h:i A') }}</td>
                                            <td class="text-end">{{ number_format($order->total_amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">{{ get_phrase('Grand Total') }}</td>
                                        <td class="text-end text-success">{{ number_format($total_sales, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="empty_box center">
                            <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                            <br><span>{{ get_phrase('No sales on this date') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom_scripts')
<script>
    function printReport() {
        var printContents = document.getElementById('printArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        location.reload();
    }
</script>
@endsection
