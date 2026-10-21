@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('POS — New Order') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Canteen') }}</a></li>
                            <li><a href="#">{{ get_phrase('POS') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('admin.canteen.order_list') }}" class="export_btn">{{ get_phrase('All Orders') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT: Student + Food Items --}}
        <div class="col-lg-8">
            <div class="eSection-wrap-2 mb-3">
                <label class="eForm-label fw-bold mb-2">{{ get_phrase('Select Student') }}</label>
                <select id="student_select" class="form-select eForm-select eChoice-multiple-with-remove">
                    <option value="">{{ get_phrase('— Search student —') }}</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Category Tabs --}}
            @if (count($categories) > 0)
                <ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
                    @foreach ($categories as $ci => $cat)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $ci == 0 ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#cat-{{ $cat->id }}" type="button">
                                {{ $cat->name }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content" id="categoryTabsContent">
                    @foreach ($categories as $ci => $cat)
                        <div class="tab-pane fade {{ $ci == 0 ? 'show active' : '' }}" id="cat-{{ $cat->id }}">
                            <div class="row g-3">
                                @forelse ($cat->activeItems as $item)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card h-100 shadow-sm canteen-item-card" style="cursor:pointer;border-radius:10px;" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                                            @if ($item->image)
                                                <img src="{{ asset('assets/uploads/canteen/items/' . $item->image) }}" class="card-img-top" style="height:140px;object-fit:cover;border-radius:10px 10px 0 0;" alt="{{ $item->name }}">
                                            @else
                                                <div style="height:140px;background:#f5f5f5;border-radius:10px 10px 0 0;display:flex;align-items:center;justify-content:center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#ccc" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                                </div>
                                            @endif
                                            <div class="card-body p-3">
                                                <h6 class="mb-1 fw-bold">{{ $item->name }}</h6>
                                                <p class="text-muted mb-1" style="font-size:12px;">{{ get_phrase('Stock') }}: {{ $item->stock_quantity }}</p>
                                                <p class="text-success fw-bold mb-0">{{ number_format($item->price, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center text-muted py-4">{{ get_phrase('No items in this category') }}</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="eSection-wrap-2 text-center py-5">
                    <img width="100px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <p class="mt-3 text-muted">{{ get_phrase('No food items available. Add items first.') }}</p>
                </div>
            @endif
        </div>

        {{-- RIGHT: Cart --}}
        <div class="col-lg-4">
            <div class="eSection-wrap-2" style="top:80px;">
                <h6 class="fw-bold mb-3">{{ get_phrase('Order Cart') }}</h6>
                <div id="cart-items">
                    <div class="text-center text-muted py-4" id="cart-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#ccc" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-15.5-15H.5V1H0v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96C4 15.1 5.1 16 6 16h14v-2H6.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1 1 0 0 0 23.5 3H3.21z"/></svg>
                        <p class="mt-2">{{ get_phrase('Cart is empty') }}</p>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                    <span>{{ get_phrase('Total') }}</span>
                    <span id="cart-total">0.00</span>
                </div>
                <div class="mb-3">
                    <textarea class="form-control eForm-control" id="order_note" rows="2" placeholder="{{ get_phrase('Note (optional)') }}"></textarea>
                </div>
                <button class="btn-form w-100" id="place-order-btn" onclick="submitOrder()" disabled>
                    {{ get_phrase('Place Order') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden form for submission --}}
    <form id="pos-form" method="POST" action="{{ route('admin.canteen.pos_store') }}" style="display:none;">
        @csrf
        <input type="hidden" name="student_id" id="form_student_id">
        <input type="hidden" name="note" id="form_note">
        <div id="form_items"></div>
    </form>
@endsection

@section('custom_scripts')
<script>
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty++;
        } else {
            cart[id] = { id, name, price, qty: 1 };
        }
        renderCart();
    }

    function removeFromCart(id) {
        delete cart[id];
        renderCart();
    }

    function updateQty(id, qty) {
        qty = parseInt(qty);
        if (qty < 1) { removeFromCart(id); return; }
        cart[id].qty = qty;
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const emptyMsg  = document.getElementById('cart-empty');
        const totalEl   = document.getElementById('cart-total');
        const placeBtn  = document.getElementById('place-order-btn');
        const keys      = Object.keys(cart);

        if (keys.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4" id="cart-empty"><p>{{ get_phrase('Cart is empty') }}</p></div>';
            totalEl.textContent = '0.00';
            placeBtn.disabled   = true;
            return;
        }

        let total = 0;
        let html  = '<div class="table-responsive"><table class="table table-sm mb-0"><tbody>';

        keys.forEach(id => {
            const item    = cart[id];
            const subtotal = item.price * item.qty;
            total         += subtotal;
            html += `
                <tr>
                    <td style="font-size:13px;">${item.name}</td>
                    <td style="width:90px;">
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0" onclick="updateQty(${id}, ${item.qty - 1})">−</button>
                            <span class="px-1">${item.qty}</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0" onclick="updateQty(${id}, ${item.qty + 1})">+</button>
                        </div>
                    </td>
                    <td style="font-size:13px;text-align:right;">${subtotal.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-sm text-danger p-0" onclick="removeFromCart(${id})">✕</button></td>
                </tr>`;
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
        totalEl.textContent = total.toFixed(2);
        placeBtn.disabled   = false;
    }

    function submitOrder() {
        const studentId = document.getElementById('student_select').value;
        if (!studentId) {
            toastr.error('{{ get_phrase('Please select a student first') }}');
            return;
        }
        if (Object.keys(cart).length === 0) {
            toastr.error('{{ get_phrase('Cart is empty') }}');
            return;
        }

        document.getElementById('form_student_id').value = studentId;
        document.getElementById('form_note').value = document.getElementById('order_note').value;

        const formItems = document.getElementById('form_items');
        formItems.innerHTML = '';

        let i = 0;
        Object.keys(cart).forEach(id => {
            formItems.innerHTML += `<input type="hidden" name="items[${i}][id]" value="${cart[id].id}">`;
            formItems.innerHTML += `<input type="hidden" name="items[${i}][qty]" value="${cart[id].qty}">`;
            i++;
        });

        document.getElementById('pos-form').submit();
    }
</script>
@endsection
