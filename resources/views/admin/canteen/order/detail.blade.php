@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Order Details') }} — {{ $order->order_number }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="{{ route('admin.canteen.order_list') }}">{{ get_phrase('Orders') }}</a></li>
                            <li><a href="#">{{ $order->order_number }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area d-flex gap-2">
                        <button onclick="printReport()" class="eBtn eBtn-black no-print">{{ get_phrase('Print Invoice') }}</button>
                        <a href="{{ route('admin.canteen.order_list') }}" class="export_btn no-print">{{ get_phrase('Back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style media="print">
        .sidebar, .navbar, .no-print, .mainSection-title, .copyright-text { display: none !important; }
        .home-section { margin-left: 0 !important; padding: 0 !important; }
    </style>

    <div id="printArea">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="eSection-wrap-2 p-4" id="invoice-area">
                    {{-- Invoice Header --}}
                    <div class="text-center mb-4">
                                    @php
                                        $school_data = App\Models\School::where('id', auth()->user()->school_id)->first();
                                    @endphp

                                    @if (!empty($school_data->school_logo))
                                        <img class=""
                                            src="{{ asset('assets/uploads/school_logo/' .DB::table('schools')->where('id', auth()->user()->school_id)->value('school_logo')) }}"
                                            width="30px" height="30px" style="border-radius: 50%; ">
                                    @else
                                        <img class="" src="{{ asset('assets') }}/images/id_logo.png"
                                            width="30px" height="30px">
                                    @endif

                                    <p>
                                    {{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}
                                    </p>

                        <p class="text-muted mb-0" style="font-size:13px;">{{ get_phrase('Canteen Invoice') }}</p>
                    </div>

                    <hr>

                    {{-- Order Info --}}
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="mb-1"><strong>{{ get_phrase('Order #') }}:</strong> {{ $order->order_number }}</p>
                            <p class="mb-1"><strong>{{ get_phrase('Date') }}:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
                            <p class="mb-1"><strong>{{ get_phrase('Status') }}:</strong>
                                @if ($order->status == 0)
                                    <span class="eBadge ebg-warning text-dark">{{ get_phrase('Pending') }}</span>
                                @elseif ($order->status == 1)
                                    <span class="eBadge ebg-success">{{ get_phrase('Completed') }}</span>
                                @else
                                    <span class="eBadge ebg-danger">{{ get_phrase('Cancelled') }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1"><strong>{{ get_phrase('Student Name') }}:</strong> {{ $order->student->name ?? '-' }}</p>
                            <p class="mb-1"><strong>{{ get_phrase('Served By') }}:</strong> {{ $order->orderedByUser->name ?? '-' }}</p>
                            @if ($order->note)
                                <p class="mb-1"><strong>{{ get_phrase('Note') }}:</strong> {{ $order->note }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    {{-- Items Table --}}
                    <div class="table-responsive">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ get_phrase('Item') }}</th>
                                    <th class="text-center">{{ get_phrase('Qty') }}</th>
                                    <th class="text-end">{{ get_phrase('Unit Price') }}</th>
                                    <th class="text-end">{{ get_phrase('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item->item_name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ number_format($item->item_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">{{ get_phrase('Total') }}</td>
                                    <td class="text-end fw-bold fs-5">{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-center mt-4 text-muted" style="font-size:12px;">
                        <p>{{ get_phrase('Thank you!') }}</p>
                    </div>
                </div>

                {{-- Status Update (no-print) --}}
                @if ($order->status == 0)
                    <div class="eSection-wrap-2 mt-3 no-print">
                        <h6 class="fw-bold mb-3">{{ get_phrase('Update Order Status') }}</h6>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.canteen.order_status', $order->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="eBtn eBtn-black">✓ {{ get_phrase('Completed') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.canteen.order_status', $order->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="2">
                                <button type="submit" class="btn btn-outline-danger">✕ {{ get_phrase('Cancel Order') }}</button>
                            </form>
                        </div>
                    </div>
                @endif
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
