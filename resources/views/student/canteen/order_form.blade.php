@extends('student.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Place Order') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="{{ route('student.canteen.menu') }}">{{ get_phrase('Canteen Menu') }}</a></li>
                            <li><a href="#">{{ get_phrase('Order') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="{{ route('student.canteen.order_history') }}" class="export_btn">{{ get_phrase('My Orders') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Food Items --}}
        <div class="col-lg-8">
            @if (count($categories) > 0)
                <ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
                    @foreach ($categories as $ci => $cat)
                        @if ($cat->activeItems->count() > 0)
                            <li class="nav-item">
                                <button class="nav-link {{ $ci == 0 ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#scat-{{ $cat->id }}" type="button">
                                    {{ $cat->name }}
                                </button>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach ($categories as $ci => $cat)
                        @if ($cat->activeItems->count() > 0)
                            <div class="tab-pane fade {{ $ci == 0 ? 'show active' : '' }}" id="scat-{{ $cat->id }}">
                                <div class="row g-3">
                                    @foreach ($cat->activeItems as $item)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="eSection-wrap-2 p-0 h-100" style="border-radius:12px;overflow:hidden;cursor:pointer;" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                                                @if ($item->image)
                                                    <img src="{{ asset('assets/uploads/canteen/items/' . $item->image) }}" class="w-100" style="height:130px;object-fit:cover;" alt="{{ $item->name }}">
                                                @else
                                                    <div style="height:130px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#ccc" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                                                    </div>
                                                @endif
                                                <div class="p-2">
                                                    <p class="fw-bold mb-0" style="font-size:13px;">{{ $item->name }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-success fw-bold">{{ number_format($item->price, 2) }}</span>
                                                        <button type="button" class="btn btn-sm eBtn eBtn-black py-0 px-2" style="font-size:12px;">{{ get_phrase('Add') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="eSection-wrap-2 text-center py-5">
                    <img width="100px" src="{{ asset('assets/images/empty_box.png') }}" />
                    <p class="mt-3 text-muted">{{ get_phrase('No items available') }}</p>
                </div>
            @endif
        </div>

        {{-- Cart --}}
        <div class="col-lg-4">
            <div class="eSection-wrap-2" style="top:80px;">
                <h6 class="fw-bold mb-3">{{ get_phrase('My Cart') }}</h6>
                <div id="cart-items">
                    <div class="text-center text-muted py-3" id="cart-empty">
                        <p>{{ get_phrase('Cart is empty') }}</p>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold mb-3">
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

    <form id="student-order-form" method="POST" action="{{ route('student.canteen.order_store') }}" style="display:none;">
        @csrf
        <input type="hidden" name="note" id="form_note">
        <div id="form_items"></div>
    </form>
@endsection

@section('custom_scripts')
<script>
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) { cart[id].qty++; } else { cart[id] = { id, name, price, qty: 1 }; }
        renderCart();
    }

    function removeFromCart(id) { delete cart[id]; renderCart(); }

    function updateQty(id, qty) {
        qty = parseInt(qty);
        if (qty < 1) { removeFromCart(id); return; }
        cart[id].qty = qty;
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const totalEl   = document.getElementById('cart-total');
        const placeBtn  = document.getElementById('place-order-btn');
        const keys      = Object.keys(cart);

        if (keys.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-3"><p>{{ get_phrase('Cart is empty') }}</p></div>';
            totalEl.textContent = '0.00';
            placeBtn.disabled = true;
            return;
        }

        let total = 0;
        let html = '<table class="table table-sm"><tbody>';
        keys.forEach(id => {
            const item = cart[id];
            const sub  = item.price * item.qty;
            total += sub;
            html += `<tr>
                <td style="font-size:12px;">${item.name}</td>
                <td style="width:80px;">
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0" onclick="updateQty(${id},${item.qty-1})">−</button>
                        <span>${item.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary px-1 py-0" onclick="updateQty(${id},${item.qty+1})">+</button>
                    </div>
                </td>
                <td style="font-size:12px;text-align:right;">${sub.toFixed(2)}</td>
                <td><button type="button" class="btn btn-sm text-danger p-0" onclick="removeFromCart(${id})">✕</button></td>
            </tr>`;
        });
        html += '</tbody></table>';
        container.innerHTML = html;
        totalEl.textContent = total.toFixed(2);
        placeBtn.disabled = false;
    }

    function submitOrder() {
        if (Object.keys(cart).length === 0) { toastr.error('{{ get_phrase('Cart is empty') }}'); return; }

        document.getElementById('form_note').value = document.getElementById('order_note').value;
        const formItems = document.getElementById('form_items');
        formItems.innerHTML = '';
        let i = 0;
        Object.keys(cart).forEach(id => {
            formItems.innerHTML += `<input type="hidden" name="items[${i}][id]" value="${cart[id].id}">`;
            formItems.innerHTML += `<input type="hidden" name="items[${i}][qty]" value="${cart[id].qty}">`;
            i++;
        });
        document.getElementById('student-order-form').submit();
    }
</script>
@endsection
