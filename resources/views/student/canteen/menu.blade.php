@extends('student.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Canteen Menu') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('student.canteen.order_form') }}" class="export_btn">{{ get_phrase('Order Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($categories as $category)
        @if ($category->activeItems->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="fw-bold mb-3" style="border-left:4px solid #4CAF50;padding-left:10px;">{{ $category->name }}</h5>
                    <div class="row g-3">
                        @foreach ($category->activeItems as $item)
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="eSection-wrap-2 p-0 h-100" style="border-radius:12px;overflow:hidden;">
                                    @if ($item->image)
                                        <img src="{{ asset('assets/uploads/canteen/items/' . $item->image) }}" class="w-100" style="height:160px;object-fit:cover;" alt="{{ $item->name }}">
                                    @else
                                        <div style="height:160px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#ccc" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                        </div>
                                    @endif
                                    <div class="p-3">
                                        <h6 class="fw-bold mb-1">{{ $item->name }}</h6>
                                        @if ($item->description)
                                            <p class="text-muted mb-2" style="font-size:12px;">{{ Str::limit($item->description, 60) }}</p>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-success fs-6">{{ number_format($item->price, 2) }}</span>
                                            <span class="badge bg-light text-dark border" style="font-size:11px;">{{ get_phrase('Stock') }}: {{ $item->stock_quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if ($categories->filter(fn($c) => $c->activeItems->count() > 0)->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="eSection-wrap-2 text-center py-5">
                    <img width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <p class="mt-3 text-muted">{{ get_phrase('No food items available at the moment') }}</p>
                </div>
            </div>
        </div>
    @endif
@endsection
