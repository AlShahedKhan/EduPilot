@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Food Items') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Food Items') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.canteen.item_create') }}', '{{ get_phrase('Add Food Item') }}')">{{ get_phrase('Add Food Item') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                @if (count($items) > 0)
                    <div class="table-responsive tScrollFix pb-2">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ get_phrase('Image') }}</th>
                                    <th>{{ get_phrase('Name') }}</th>
                                    <th>{{ get_phrase('Category') }}</th>
                                    <th>{{ get_phrase('Price') }}</th>
                                    <th>{{ get_phrase('Stock') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th class="text-end">{{ get_phrase('Options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $key => $item)
                                    <tr>
                                        <td>{{ $items->firstItem() + $key }}</td>
                                        <td>
                                            @if ($item->image)
                                                <img src="{{ asset('assets/uploads/canteen/items/' . $item->image) }}" width="45" height="45" style="object-fit:cover;border-radius:6px;" alt="{{ $item->name }}">
                                            @else
                                                <div style="width:45px;height:45px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#aaa" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category->name ?? '-' }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>
                                            @if ($item->stock_quantity > 0)
                                                <span class="eBadge ebg-success">{{ $item->stock_quantity }}</span>
                                            @else
                                                <span class="eBadge ebg-danger">{{ get_phrase('Out of Stock') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 1)
                                                <span class="eBadge ebg-success">{{ get_phrase('Available') }}</span>
                                            @else
                                                <span class="eBadge ebg-danger">{{ get_phrase('Unavailable') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="adminTable-action">
                                                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ get_phrase('Actions') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.canteen.item_edit', $item->id) }}', '{{ get_phrase('Edit Food Item') }}')">{{ get_phrase('Edit') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.canteen.item_delete', $item->id) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $items->links() !!}
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br><span>{{ get_phrase('No food items found') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
