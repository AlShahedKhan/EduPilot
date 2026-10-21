@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Food Categories') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('Categories') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn" onclick="rightModal('{{ route('admin.canteen.category_create') }}', '{{ get_phrase('Add Category') }}')">{{ get_phrase('Add Category') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-7 offset-md-2">
            <div class="eSection-wrap">
                @if (count($categories) > 0)
                    <div class="table-responsive tScrollFix pb-2">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ get_phrase('Category Name') }}</th>
                                    <th>{{ get_phrase('Status') }}</th>
                                    <th class="text-end">{{ get_phrase('Options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $categories->firstItem() + $key }}</td>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                           @if ($category->status == 1)
                                                <span class="eBadge ebg-success">{{ get_phrase('Active') }}</span>
                                            @else
                                                <span class="eBadge ebg-danger">{{ get_phrase('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="adminTable-action">
                                                <button type="button" class="eBtn eBtn-black dropdown-toggle table-action-btn-2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ get_phrase('Actions') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="rightModal('{{ route('admin.canteen.category_edit', $category->id) }}', '{{ get_phrase('Edit Category') }}')">{{ get_phrase('Edit') }}</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.canteen.category_delete', $category->id) }}', 'undefined');">{{ get_phrase('Delete') }}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! $categories->links() !!}
                @else
                <div class="empty_box center">
                    <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <br>
                    <span class="">{{ get_phrase('No data found') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
